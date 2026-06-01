<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClientController extends Controller
{
    /**
     * Add Client
     */
    public function addClient(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'call_type' => 'required|in:incoming,outgoing',

            'lead_type' => 'required|in:cold,hot,warm',

            'fullname'  => 'required|string|max:255',

            // 'phone'     => 'required|digits_between:10,15',
            'phone'     => 'required|digits:10|unique:clients,phone',

            'location'  => 'required|string|max:255',

            'referance' => 'required|string|max:255',

            'case_type' => 'required|string|max:255',

            'remarks'   => 'required|string',

        ], [
            'referance.required' => 'The reference field is required.',
        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors(),

            ], 422);
        }

        $client = Client::create([

            'call_type' => $request->call_type,

            'lead_type' => $request->lead_type,

            'fullname'  => $request->fullname,

            'phone'     => $request->phone,

            'location'  => $request->location,

            'referance' => $request->referance,

            'case_type' => $request->case_type,

            'remarks'   => $request->remarks,

            'added_by'  => auth()->id(),

            'status'    => 1,

        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Client Added Successfully',

            'data'    => $client,

        ]);
    }

    /**
     * Client List
     *
     * Get all clients with optional search, filters and pagination.
     *
     * @group Client
     *
     * @authenticated
     *
     * @queryParam search string optional Search by client name or phone number. Example: asif
     *
     * @queryParam lead_type string optional Filter by lead type. Possible values: cold,warm,hot. Example: hot
     *
     * @queryParam date_filter string optional Filter by date. Possible values: today,yesterday,this_week,custom. Example: today
     *
     * @queryParam from_date string optional Required only when date_filter=custom. Example: 2026-05-01
     *
     * @queryParam to_date string optional Required only when date_filter=custom. Example: 2026-05-20
     *
     * @queryParam page integer optional Pagination page number. Example: 1
     *
     * @queryParam per_page integer optional Number of records per page. Example: 10
     *
     * @response 200 {
     *  "status": true,
     *  "message": "Client List Fetch Successfully"
     * }
     */

    public function clientList(Request $request)
    {
        $query = Client::with('addedBy');

        /*
        |--------------------------------------------------------------------------
        | My Data / All Data
        |--------------------------------------------------------------------------
        */

        if ($request->filled('data_type')) {

            // My Clients
            if ($request->data_type == 'my') {

                $query->where('added_by', auth()->id());
            }

            // All Clients
            elseif ($request->data_type == 'all') {

                // No condition
            }

        } else {

            // Default My Data
            $query->where('added_by', auth()->id());
        }

        /*
        |--------------------------------------------------------------------------
        | Search By Name / Phone
        |--------------------------------------------------------------------------
        */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%");

            });
        }

        /*
        |--------------------------------------------------------------------------
        | Lead Type Filter
        |--------------------------------------------------------------------------
        */

        if ($request->filled('lead_type')) {

            $query->where('lead_type', $request->lead_type);
        }

        /*
        |--------------------------------------------------------------------------
        | Date Filters
        |--------------------------------------------------------------------------
        */

        if ($request->filled('date_filter')) {

            // Today
            if ($request->date_filter == 'today') {

                $query->whereDate('created_at', today());
            }

            // Yesterday
            elseif ($request->date_filter == 'yesterday') {

                $query->whereDate(
                    'created_at',
                    today()->subDay()
                );
            }

            // This Week
            elseif ($request->date_filter == 'this_week') {

                $query->whereBetween('created_at', [

                    now()->startOfWeek(),

                    now()->endOfWeek(),

                ]);
            }

            // Custom Date
            elseif (
                $request->date_filter == 'custom'
                &&
                $request->filled('from_date')
                &&
                $request->filled('to_date')
            ) {

                $query->whereBetween('created_at', [

                    $request->from_date . ' 00:00:00',

                    $request->to_date . ' 23:59:59',

                ]);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

        $perPage = $request->per_page ?? 10;

        $clients = $query->latest()->paginate($perPage);

        /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

        $countQuery = Client::query();

        // Apply my/all filter to counts also
        if (! $request->filled('data_type') || $request->data_type == 'my') {

            $countQuery->where('added_by', auth()->id());
        }

        $counts = [

            'today'     => (clone $countQuery)
                ->whereDate('created_at', today())
                ->count(),

            'yesterday' => (clone $countQuery)
                ->whereDate(
                    'created_at',
                    today()->subDay()
                )
                ->count(),

            'this_week' => (clone $countQuery)
                ->whereBetween('created_at', [

                    now()->startOfWeek(),

                    now()->endOfWeek(),

                ])
                ->count(),

            'all'       => (clone $countQuery)->count(),

            'cold'      => (clone $countQuery)
                ->where('lead_type', 'cold')
                ->count(),

            'warm'      => (clone $countQuery)
                ->where('lead_type', 'warm')
                ->count(),

            'hot'       => (clone $countQuery)
                ->where('lead_type', 'hot')
                ->count(),

        ];

        /*
        |--------------------------------------------------------------------------
        | Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'status'     => true,

            'message'    => 'Client List Fetch Successfully',

            /*
            |--------------------------------------------------------------------------
            | Counts
            |--------------------------------------------------------------------------
            */

            'counts'     => $counts,

            /*
            |--------------------------------------------------------------------------
            | Pagination
            |--------------------------------------------------------------------------
            */

            'pagination' => [

                'current_page' => $clients->currentPage(),

                'last_page'    => $clients->lastPage(),

                'per_page'     => $clients->perPage(),

                'total'        => $clients->total(),

            ],

            /*
            |--------------------------------------------------------------------------
            | Client List
            |--------------------------------------------------------------------------
            */

            'data'       => collect($clients->items())->map(function ($client) {

                return [

                    'id'            => $client->id,

                    'fullname'      => $client->fullname,

                    'phone'         => $client->phone,

                    'location'      => $client->location,

                    'referance'     => $client->referance,

                    'call_type'     => $client->call_type,

                    'lead_type'     => $client->lead_type,

                    'case_type'     => $client->case_type,

                    'remarks'       => $client->remarks,

                    'status'        => $client->status,

                    /*
                    |--------------------------------------------------------------------------
                    | Added By
                    |--------------------------------------------------------------------------
                    */

                    'added_by'      => [

                        'id'   => $client->addedBy?->id,

                        'name' => $client->addedBy?->name,

                    ],

                    /*
                    |--------------------------------------------------------------------------
                    | Created Date
                    |--------------------------------------------------------------------------
                    */

                    'created_date'  => date(
                        'd M Y',
                        strtotime($client->created_at)
                    ),

                    'created_time'  => date(
                        'h:i A',
                        strtotime($client->created_at)
                    ),

                    'created_label' => \Carbon\Carbon::parse(
                        $client->created_at
                    )->diffForHumans(),

                ];

            }),

        ]);
    }

    /**
     * Show Client Details
     *
     * @group Client
     *
     * @authenticated
     *
     * @urlParam id integer required Client ID. Example: 1
     */
    public function showClient($id)
    {
        $client = Client::with('addedBy')->find($id);

        if (! $client) {

            return response()->json([

                'status'  => false,

                'message' => 'Client Not Found',

            ], 404);
        }

        /*
    |--------------------------------------------------------------------------
    | Appointment Counts
    |--------------------------------------------------------------------------
    */

        $appointmentCount = Appointment::where(
            'client_id',
            $client->id
        )->count();

        $todayAppointmentCount = Appointment::where(
            'client_id',
            $client->id
        )
            ->whereDate(
                'appointment_date',
                today()
            )
            ->count();

        $upcomingAppointmentCount = Appointment::where(
            'client_id',
            $client->id
        )
            ->whereDate(
                'appointment_date',
                '>',
                today()
            )
            ->count();

        return response()->json([

            'status'  => true,

            'message' => 'Client Details Fetch Successfully',

            'data'    => [

                'id'                 => $client->id,

                'fullname'           => $client->fullname,

                'phone'              => $client->phone,

                'location'           => $client->location,

                'referance'          => $client->referance,

                'call_type'          => $client->call_type,

                'lead_type'          => $client->lead_type,

                'case_type'          => $client->case_type,

                'remarks'            => $client->remarks,

                'status'             => $client->status,

                /*
            |--------------------------------------------------------------------------
            | Appointment Counts
            |--------------------------------------------------------------------------
            */

                'appointment_counts' => [

                    'total_appointments'    => $appointmentCount,

                    'today_appointments'    => $todayAppointmentCount,

                    'upcoming_appointments' => $upcomingAppointmentCount,

                ],

                /*
            |--------------------------------------------------------------------------
            | Added By
            |--------------------------------------------------------------------------
            */

                'added_by'           => [

                    'id'   => $client->addedBy?->id,

                    'name' => $client->addedBy?->name,

                ],

                /*
            |--------------------------------------------------------------------------
            | Created Date
            |--------------------------------------------------------------------------
            */

                'created_date'       => date(
                    'd M Y',
                    strtotime($client->created_at)
                ),

                'created_time'       => date(
                    'h:i A',
                    strtotime($client->created_at)
                ),

                'created_label'      => \Carbon\Carbon::parse(
                    $client->created_at
                )->diffForHumans(),

            ],

        ]);
    }

    public function allClients(Request $request)
    {
        $query = Client::query();

        // LEAD TYPE FILTER
        if (
            $request->filled('category') &&
            $request->category != 'all'
        ) {

            $query->where(
                'lead_type',
                $request->category
            );
        }

        // FROM DATE
        if ($request->filled('from_date')) {

            $query->whereDate(
                'created_at',
                '>=',
                $request->from_date
            );
        }

        // TO DATE
        if ($request->filled('to_date')) {

            $query->whereDate(
                'created_at',
                '<=',
                $request->to_date
            );
        }

        // SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'fullname',
                    'LIKE',
                    '%' . $search . '%'
                )
                    ->orWhere(
                        'phone',
                        'LIKE',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'location',
                        'LIKE',
                        '%' . $search . '%'
                    );
            });
        }

        $clients = $query
            ->with('addedBy')
            ->latest()
            ->paginate(10);

        $data = collect($clients->items())->map(function ($item) {

            return [

                'id'        => $item->id,

                'client_no' => $item->fullname,
                'call_type' => $item->call_type,

                'date'      => date(
                    'Y-m-d',
                    strtotime($item->created_at)
                ),

                'name'      => $item->name,

                'phone'     => $item->phone,

                'location'  => $item->location,

                'category'  => $item->lead_type,
                'notes'     => $item->lead_type,

                'referance' => $item->referance,
                'case_type' => $item->case_type,

                'added_by'  => $item->addedBy?->name ?? '-',
            ];
        });

        // COUNTS
        $totalCount = Client::count();

        $hotCount = Client::where(
            'lead_type',
            'hot'
        )->count();

        $warmCount = Client::where(
            'lead_type',
            'warm'
        )->count();

        $coldCount = Client::where(
            'lead_type',
            'cold'
        )->count();

        return response()->json([

            'status'     => true,

            'message'    => 'Clients fetched successfully',

            'counts'     => [

                'total' => $totalCount,

                'hot'   => $hotCount,

                'warm'  => $warmCount,

                'cold'  => $coldCount,
            ],

            'pagination' => [

                'current_page' => $clients->currentPage(),

                'last_page'    => $clients->lastPage(),

                'per_page'     => $clients->perPage(),

                'total'        => $clients->total(),
            ],

            'data'       => $data,
        ]);
    }

    /**
     * 
     * 
        * Get My  Clients (For Dashboard)
     */
    public function myClients(Request $request)
    {
        $userId = auth()->id();

        $query = Client::where('added_by', $userId);

        // LEAD TYPE FILTER
        if ($request->filled('category') && $request->category != 'all') {
            $query->where('lead_type', $request->category);
        }

        // FROM DATE
        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        // TO DATE
        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        // SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('fullname', 'LIKE', "%{$search}%")
                    ->orWhere('phone', 'LIKE', "%{$search}%")
                    ->orWhere('location', 'LIKE', "%{$search}%");
            });
        }

        $clients = $query
            ->latest()
            ->paginate(10);

        $data = collect($clients->items())->map(function ($item) {

            return [
                'id'        => $item->id,
                'client_no' => $item->fullname,
                'call_type' => $item->call_type,

                'date'      => date(
                    'Y-m-d',
                    strtotime($item->created_at)
                ),

                'name'      => $item->name,
                'phone'     => $item->phone,
                'location'  => $item->location,

                'category'  => $item->lead_type,
                'notes'     => $item->lead_type,

                'referance' => $item->referance,
                'case_type' => $item->case_type,

                'added_by'  => $item->added_by,
            ];
        });

        // COUNTS (ONLY LOGGED-IN USER CLIENTS)

        $totalCount = Client::where(
            'added_by',
            $userId
        )->count();

        $hotCount = Client::where(
            'added_by',
            $userId
        )
            ->where('lead_type', 'hot')
            ->count();

        $warmCount = Client::where(
            'added_by',
            $userId
        )
            ->where('lead_type', 'warm')
            ->count();

        $coldCount = Client::where(
            'added_by',
            $userId
        )
            ->where('lead_type', 'cold')
            ->count();

        return response()->json([
            'status'     => true,

            'message'    => 'Clients fetched successfully',

            'counts'     => [
                'total' => $totalCount,
                'hot'   => $hotCount,
                'warm'  => $warmCount,
                'cold'  => $coldCount,
            ],

            'pagination' => [
                'current_page' => $clients->currentPage(),
                'last_page'    => $clients->lastPage(),
                'per_page'     => $clients->perPage(),
                'total'        => $clients->total(),
            ],

            'data'       => $data,
        ]);
    }

    /**
     * Update Client
     *
     * @group Client
     *
     * @authenticated
     *
     * @urlParam id integer required Client ID. Example: 1
     */

    public function updateClient(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [

            'call_type' => 'required|in:incoming,outgoing',

            'lead_type' => 'required|in:cold,hot,warm',

            'fullname'  => 'required|string|max:255',

            'phone'     => 'required|digits:10|unique:clients,phone,' . $id,

            'location'  => 'required|string|max:255',

            'referance' => 'required|string|max:255',

            'case_type' => 'required|string|max:255',

            'remarks'   => 'required|string',

        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors(),
            ], 422);
        }

        $client = Client::find($id);

        if (! $client) {
            return response()->json([
                'status'  => false,
                'message' => 'Client not found',
            ], 404);
        }

        $client->update([
            'call_type' => $request->call_type,
            'lead_type' => $request->lead_type,
            'fullname'  => $request->fullname,
            'phone'     => $request->phone,
            'location'  => $request->location,
            'referance' => $request->referance,
            'case_type' => $request->case_type,
            'remarks'   => $request->remarks,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Client Updated Successfully',
            'data'    => $client->fresh(),
        ]);
    }

    public function deleteClient($id)
    {
        $client = Client::find($id);

        if (! $client) {
            return response()->json([
                'status'  => false,
                'message' => 'Client not found',
            ], 404);
        }

        $client->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Client Deleted Successfully',
        ]);
    }

    public function searchByPhone(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|digits:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $client = Client::where('phone', $request->phone)->first();

        if (! $client) {
            return response()->json([
                'status'  => false,
                'message' => 'Client not found',
            ], 200);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Client found',
            'data'    => [
                'id'        => $client->id,
                'fullname'  => $client->fullname,
                'phone'     => $client->phone,
                'location'  => $client->location,
                'case_type' => $client->case_type,
                'lead_type' => $client->lead_type,
            ],
        ]);
    }
}