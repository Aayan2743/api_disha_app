<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class AppointmentController extends Controller
{
    /**
     * Add Appointment
     */

    public function addAppointment(Request $request)
    {
        $validator = Validator::make($request->all(), [

            /*
        |--------------------------------------------------------------------------
        | Appointment Fields
        |--------------------------------------------------------------------------
        */

            'appointment_type' => 'required|in:online,offline',

            'client_type'      => 'required|in:new_client,existing_client',

            'appointment_date' => 'required|date',

            'appointment_time' => 'required',

            'fee_amount'       => 'required|numeric',

            'payment_method'   => 'required|in:cash,online_payment',

            'remarks'          => 'nullable|string',

            /*
        |--------------------------------------------------------------------------
        | Existing Client
        |--------------------------------------------------------------------------
        */

            'client_id'        => 'required_if:client_type,existing_client',

            /*
        |--------------------------------------------------------------------------
        | New Client
        |--------------------------------------------------------------------------
        */

            'client_name'      => 'required_if:client_type,new_client',

            'client_phone'     => 'required_if:client_type,new_client',

            'call_type'        => 'required_if:client_type,new_client|in:incoming,outgoing,walking',

            'location'         => 'required_if:client_type,new_client',

            'referance'        => 'required_if:client_type,new_client',

            'case_type'        => 'required_if:client_type,new_client',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Check Appointment Already Exists
    |--------------------------------------------------------------------------
    */

        $alreadyAppointment = Appointment::whereDate(
            'appointment_date',
            $request->appointment_date
        )
            ->where(
                'appointment_time',
                $request->appointment_time
            )
            ->first();

        if ($alreadyAppointment) {

            return response()->json([

                'status'  => false,

                'message' => 'Appointment Time Already Booked',

            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Existing Client
    |--------------------------------------------------------------------------
    */

        if ($request->client_type == 'existing_client') {

            $client = Client::find($request->client_id);

            if (! $client) {

                return response()->json([

                    'status'  => false,

                    'message' => 'Client Not Found',

                ], 404);
            }

            $clientId = $client->id;

            $clientName = $client->fullname;

            $clientPhone = $client->phone;

        } else {

            /*
        |--------------------------------------------------------------------------
        | Check Existing Phone
        |--------------------------------------------------------------------------
        */

            $existingPhone = Client::where(
                'phone',
                $request->client_phone
            )->first();

            if ($existingPhone) {

                return response()->json([

                    'status'  => false,

                    'message' => 'Phone Number Already Exists',

                ], 422);
            }

            /*
        |--------------------------------------------------------------------------
        | Create New Client
        |--------------------------------------------------------------------------
        */

            $newClient = Client::create([

                'fullname'  => $request->client_name,

                'phone'     => $request->client_phone,

                'location'  => $request->location,

                'referance' => $request->referance,

                'case_type' => $request->case_type,

                'call_type' => $request->call_type,

                'lead_type' => 'warm',

                'remarks'   => $request->remarks,

                'added_by'  => auth()->id(),

                'status'    => 1,

            ]);

            $clientId = $newClient->id;

            $clientName = $newClient->fullname;

            $clientPhone = $newClient->phone;
        }

        /*
    |--------------------------------------------------------------------------
    | Create Appointment
    |--------------------------------------------------------------------------
    */

        $appointment = Appointment::create([

            'appointment_type' => $request->appointment_type,

            'client_type'      => $request->client_type,

            'client_id'        => $clientId,

            'client_name'      => $clientName,

            'client_phone'     => $clientPhone,

            'appointment_date' => $request->appointment_date,

            'appointment_time' => $request->appointment_time,

            'fee_amount'       => $request->fee_amount,

            'payment_method'   => $request->payment_method,

            'remarks'          => $request->remarks,

            'added_by'         => auth()->id(),

            'status'           => 1,

        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Appointment Added Successfully',

            'data'    => [

                'id'               => $appointment->id,

                'appointment_type' => $appointment->appointment_type,

                'client_type'      => $appointment->client_type,

                'client_id'        => $appointment->client_id,

                'client_name'      => $appointment->client_name,

                'client_phone'     => $appointment->client_phone,

                'appointment_date' => $appointment->appointment_date,

                'appointment_time' => $appointment->appointment_time,

                'fee_amount'       => $appointment->fee_amount,

                'payment_method'   => $appointment->payment_method,

                'remarks'          => $appointment->remarks,

                'status'           => $appointment->status,

                'created_at'       => date(
                    'd M Y h:i A',
                    strtotime($appointment->created_at)
                ),

            ],

        ]);
    }

    public function addAppointment_using_clinet(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'appointment_type' => 'required|in:online,offline',

            'client_id'        => 'required|exists:clients,id',

            'appointment_date' => 'required|date',

            'appointment_time' => 'required',

            'fee_amount'       => 'required|numeric',

            'payment_method'   => 'required|in:cash,online_payment',

            'remarks'          => 'nullable|string',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Check Appointment Already Exists
    |--------------------------------------------------------------------------
    */

        $alreadyAppointment = Appointment::whereDate(
            'appointment_date',
            $request->appointment_date
        )
            ->where(
                'appointment_time',
                $request->appointment_time
            )
            ->first();

        if ($alreadyAppointment) {

            return response()->json([

                'status'  => false,

                'message' => 'Appointment Time Already Booked',

            ], 422);
        }

        /*
    |--------------------------------------------------------------------------
    | Get Client Details
    |--------------------------------------------------------------------------
    */

        $client = Client::find($request->client_id);

        $appointment = Appointment::create([

            'appointment_type' => $request->appointment_type,

            'client_type'      => 'existing_client',

            'client_id'        => $client->id,

            'client_name'      => $client->fullname,

            'client_phone'     => $client->phone,

            'appointment_date' => $request->appointment_date,

            'appointment_time' => $request->appointment_time,

            'fee_amount'       => $request->fee_amount,

            'payment_method'   => $request->payment_method,

            'remarks'          => $request->remarks,

            'added_by'         => auth()->id(),

            'status'           => 1,

        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Appointment Added Successfully',

            'data'    => $appointment,

        ]);
    }

    public function fetchClient(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'client_phone' => 'required',

        ]);

        if ($validator->fails()) {

            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $client = Client::where('phone', $request->client_phone)->first();

        if (! $client) {

            return response()->json([
                'status'  => false,
                'message' => 'Client not found',
            ], 404);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Client fetched successfully',
            'data'    => $client,
        ]);
    }

    public function appointmentList(Request $request)
    {
        $query = Appointment::with('addedBy');

        /*
    |--------------------------------------------------------------------------
    | Default Today Date
    |--------------------------------------------------------------------------
    */

        $appointmentDate = $request->appointment_date ?? date('Y-m-d');

        $query->whereDate('appointment_date', $appointmentDate);

        /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where('client_name', 'LIKE', "%{$search}%")
                    ->orWhere('client_phone', 'LIKE', "%{$search}%");

            });
        }

        /*
    |--------------------------------------------------------------------------
    | Status Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('status')) {

            $query->where('status', $request->status);
        }

        /*
    |--------------------------------------------------------------------------
    | Appointment Type Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('appointment_type')) {

            $query->where(
                'appointment_type',
                $request->appointment_type
            );
        }

        /*
    |--------------------------------------------------------------------------
    | Client Type Filter
    |--------------------------------------------------------------------------
    */

        if ($request->filled('client_type')) {

            $query->where(
                'client_type',
                $request->client_type
            );
        }

        /*
    |--------------------------------------------------------------------------
    | My Added / All Added
    |--------------------------------------------------------------------------
    */

        if ($request->filled('data_type')) {

            // Only My Added Data
            if ($request->data_type == 'my_added') {

                $query->where('added_by', auth()->id());
            }

            // All Added Data
            elseif ($request->data_type == 'all_added') {

                // No condition
            }

        } else {

            // Default My Added
            $query->where('added_by', auth()->id());
        }

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $perPage = $request->per_page ?? 10;

        $appointments = $query->latest()->paginate($perPage);

        /*
    |--------------------------------------------------------------------------
    | Counts Query
    |--------------------------------------------------------------------------
    */

        $countQuery = Appointment::whereDate(
            'appointment_date',
            $appointmentDate
        );

        /*
    |--------------------------------------------------------------------------
    | My Added / All Added Counts
    |--------------------------------------------------------------------------
    */

        if (
            ! $request->filled('data_type')
            ||
            $request->data_type == 'my_added'
        ) {

            $countQuery->where('added_by', auth()->id());
        }

        /*
    |--------------------------------------------------------------------------
    | Counts
    |--------------------------------------------------------------------------
    */

        $counts = [

            'all'             => (clone $countQuery)->count(),

            'confirmed'       => (clone $countQuery)
                ->where('status', 1)
                ->count(),

            'pending'         => (clone $countQuery)
                ->where('status', 2)
                ->count(),

            'cancelled'       => (clone $countQuery)
                ->where('status', 3)
                ->count(),

            'online'          => (clone $countQuery)
                ->where(
                    'appointment_type',
                    'online'
                )
                ->count(),

            'offline'         => (clone $countQuery)
                ->where(
                    'appointment_type',
                    'offline'
                )
                ->count(),

            'new_client'      => (clone $countQuery)
                ->where(
                    'client_type',
                    'new_client'
                )
                ->count(),

            'existing_client' => (clone $countQuery)
                ->where(
                    'client_type',
                    'existing_client'
                )
                ->count(),

        ];

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'status'        => true,

            'message'       => 'Appointment List Fetch Successfully',

            'selected_date' => date(
                'l, F d, Y',
                strtotime($appointmentDate)
            ),

            /*
        |--------------------------------------------------------------------------
        | Counts
        |--------------------------------------------------------------------------
        */

            'counts'        => $counts,

            /*
        |--------------------------------------------------------------------------
        | Pagination
        |--------------------------------------------------------------------------
        */

            'pagination'    => [

                'current_page' => $appointments->currentPage(),

                'last_page'    => $appointments->lastPage(),

                'per_page'     => $appointments->perPage(),

                'total'        => $appointments->total(),

            ],

            /*
        |--------------------------------------------------------------------------
        | Appointment List
        |--------------------------------------------------------------------------
        */

            'data'          => collect($appointments->items())->map(function ($appointment) {

                return [

                    'id'               => $appointment->id,

                    'client_name'      => $appointment->client_name,

                    'client_phone'     => $appointment->client_phone,

                    'appointment_type' => $appointment->appointment_type,

                    'client_type'      => $appointment->client_type,

                    'appointment_date' => $appointment->appointment_date,

                    'appointment_time' => date(
                        'h:i A',
                        strtotime(
                            $appointment->appointment_time
                        )
                    ),

                    'fee_amount'       => $appointment->fee_amount,

                    'payment_method'   => $appointment->payment_method,

                    'remarks'          => $appointment->remarks,

                    /*
                |--------------------------------------------------------------------------
                | Added By
                |--------------------------------------------------------------------------
                */

                    'added_by'         => [

                        'id'   => $appointment->addedBy?->id,

                        'name' => $appointment->addedBy?->name,

                    ],

                    /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                    'status'           => $appointment->status,

                    'status_text'      => match ($appointment->status) {

                        1       => 'Confirmed',

                        2       => 'Pending',

                        3       => 'Cancelled',

                        default => 'Unknown',

                    },

                    /*
                |--------------------------------------------------------------------------
                | UI Labels
                |--------------------------------------------------------------------------
                */

                    'type_label'       => ucfirst(
                        $appointment->appointment_type
                    ),

                    'client_label'     => $appointment->client_type ==
                    'new_client'
                        ? 'New Client'
                        : 'Old Client',

                ];

            }),

        ]);
    }

    public function allAppointments(Request $request)
    {
        $query = Appointment::query();

        // DATE FILTER
        if ($request->filled('appointment_date')) {

            $query->whereDate(
                'appointment_date',
                $request->appointment_date
            );
        }

        // VISIT FILTER
        if (
            $request->filled('appointment_type') &&
            $request->appointment_type != 'all'
        ) {

            $query->where(
                'appointment_type',
                $request->appointment_type
            );
        }

        // SEARCH PHONE
        if ($request->filled('search')) {

            $query->where(
                'client_phone',
                'LIKE',
                '%' . $request->search . '%'
            );
        }

        // STATUS FILTER
        if (
            $request->filled('status') &&
            $request->status != 'all'
        ) {

            $query->where('status', $request->status);
        }

        $appointments = $query

            ->with('addedBy')
            ->orderBy('appointment_date', 'DESC')
            ->orderBy('appointment_time', 'ASC')
            ->get();

        $data = $appointments->map(function ($item) {

            return [

                'id'               => $item->id,

                'time'             => date(
                    'H:i',
                    strtotime($item->appointment_time)
                ),

                'client_name'      => $item->client_name,

                'client_phone'     => $item->client_phone,

                'purpose'          => $item->remarks,

                'visit'            => ucfirst($item->appointment_type),

                'fee_amount'       => $item->fee_amount,

                'payment_method'   => $item->payment_method,
                'payment_status'   => $item->payment_status,

                'remarks'          => $item->remarks,

                'status'           => $item->status,

                'appointment_date' => $item->appointment_date,
                'added_by'         => $item->addedBy?->name,
            ];
        });

        return response()->json([

            'status'  => true,

            'message' => 'Appointments fetched successfully',

            'count'   => $data->count(),

            'data'    => $data,

        ]);
    }

    public function markReached($id)
    {
        $appointment = Appointment::find($id);

        if (! $appointment) {

            return response()->json([
                'status'  => false,
                'message' => 'Appointment not found',
            ], 404);
        }

        $appointment->status = 4; // Mark as reached (pending)
        $appointment->save();

        return response()->json([
            'status'  => true,
            'message' => 'Appointment marked as reached',
        ]);
    }

    public function feeCollected($id, Request $request)
    {
        $appointment = Appointment::find($id);

        if (! $appointment) {

            return response()->json([
                'status'  => false,
                'message' => 'Appointment not found',
            ], 404);
        }

        $appointment->payment_status           = 'completed';                        // Mark payment as completed
        $appointment->payment_collected_method = $request->payment_collected_method; // Mark payment as completed
        $appointment->save();

        return response()->json([
            'status'  => true,
            'message' => 'Payment marked as collected',
        ]);

    }

    public function calendarAppointments(Request $request)
    {
        $month = $request->month ?? date('m');
        $year  = $request->year ?? date('Y');

        $query = Appointment::whereYear(
            'appointment_date',
            $year
        )
            ->whereMonth(
                'appointment_date',
                $month
            );

        // STAFF ID FILTER (added_by)
        if ($request->filled('staff_id')) {

            $query->where('added_by', $request->staff_id);
        }

        // STAFF NAME FILTER (via addedBy relationship)
        if ($request->filled('staff_name')) {

            $staffName = $request->staff_name;

            $query->whereHas('addedBy', function ($q) use ($staffName) {

                $q->where('name', 'LIKE', '%' . $staffName . '%');
            });
        }

        $appointments = $query->get();

        $grouped = $appointments
            ->groupBy('appointment_date')
            ->map(function ($items) {

                $total = $items->count();

                $closed = $items
                    ->where('status', 2)
                    ->count();

                $open = $total - $closed;

                return [

                    'total'  => $total,

                    'open'   => $open,

                    'closed' => $closed,
                ];
            });

        return response()->json([

            'status'  => true,

            'message' => 'Calendar appointments fetched',

            'data'    => $grouped,
        ]);
    }

    // DATE APPOINTMENTS
    public function dateAppointments(Request $request)
    {
        $validator = validator($request->all(), [

            'date'   => 'required|date',
            'search' => 'nullable',
        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        $query = Appointment::query();

        $query->whereDate(
            'appointment_date',
            $request->date
        );

        // SEARCH
        if ($request->filled('search')) {

            $search = $request->search;

            $query->where(function ($q) use ($search) {

                $q->where(
                    'client_name',
                    'LIKE',
                    '%' . $search . '%'
                )
                    ->orWhere(
                        'client_phone',
                        'LIKE',
                        '%' . $search . '%'
                    )
                    ->orWhere(
                        'remarks',
                        'LIKE',
                        '%' . $search . '%'
                    );
            });
        }

        $appointments = $query
            ->with('addedBy')
            ->orderBy('appointment_time', 'ASC')
            ->get();

        $total = $appointments->count();

        $paid = $appointments
            ->where('payment_status', 'completed')
            ->count();

        $unpaid = $appointments
            ->where('payment_status', 'pending')
            ->count();

        $reached = $appointments
            ->where('status', 2)
            ->count();

        $data = $appointments->map(function ($item) {

            return [

                'id'             => $item->id,

                'time'           => date(
                    'H:i',
                    strtotime($item->appointment_time)
                ),

                'visit_type'     => $item->appointment_type,

                'payment_status' => $item->payment_status,

                'reached_status' =>
                $item->status == 2
                    ? 'reached'
                    : 'pending',

                'title'          => $item->case_type,

                'client_name'    => $item->client_name,

                'client_phone'   => $item->client_phone,

                'remarks'        => $item->remarks,

                'status'         =>
                $item->status == 2
                    ? 'closed'
                    : 'open',

                'added_by'       => $item->addedBy?->name ?? '-',
            ];
        });

        return response()->json([

            'status'  => true,

            'message' => 'Appointments fetched successfully',

            'counts'  => [

                'total'   => $total,

                'paid'    => $paid,

                'unpaid'  => $unpaid,

                'reached' => $reached,
            ],

            'data'    => $data,
        ]);
    }

    public function staffAttendance(Request $request)
    {
        $date = $request->date
            ? Carbon::parse($request->date)->format('Y-m-d')
            : date('Y-m-d');

        // ALL STAFF
        $users = User::whereIn('role', [
            'admin',
            'receptionist',
            'telecaller',
        ])->get();

        $attendance = Attendance::whereDate(
            'attendance_date',
            $date
        )
            ->get()
            ->keyBy('user_id');

        $presentCount = 0;
        $lateCount    = 0;
        $absentCount  = 0;

        $data = $users->map(function ($user) use (
            $attendance,
            &$presentCount,
            &$lateCount,
            &$absentCount
        ) {

            $att = $attendance[$user->id] ?? null;

            // ABSENT
            if (! $att) {

                $absentCount++;

                return [

                    'user_id'     => $user->id,

                    'name'        => $user->name,

                    'phone'       => $user->phone,

                    'role'        => $user->role,

                    'status'      => 'absent',

                    'login_time'  => null,

                    'logout_time' => null,

                    'hours'       => null,
                ];
            }

            $loginTime = $att->punch_in
                ? Carbon::parse($att->punch_in)
                : null;

            $logoutTime = $att->punch_out
                ? Carbon::parse($att->punch_out)
                : null;

            // PRESENT / LATE
            if ($loginTime) {

                if ($loginTime->format('H:i') > '10:00') {

                    $lateCount++;

                    $status = 'late';

                } else {

                    $presentCount++;

                    $status = 'present';
                }

            } else {

                $status = 'absent';

                $absentCount++;
            }

            // TOTAL HOURS
            $hours = null;

            if ($att->total_minutes) {

                $hrs = floor($att->total_minutes / 60);

                $mins = $att->total_minutes % 60;

                $hours = $hrs . 'h ' . $mins . 'm';
            }

            return [

                'user_id'     => $user->id,

                'name'        => $user->name,

                'phone'       => $user->phone,

                'role'        => ucfirst($user->role),

                'status'      => $status,

                'login_time'  => $loginTime
                    ? $loginTime->format('H:i')
                    : null,

                'logout_time' => $logoutTime
                    ? $logoutTime->format('H:i')
                    : null,

                'hours'       => $hours,
            ];
        });

        return response()->json([

            'status'  => true,

            'message' => 'Attendance fetched successfully',

            'counts'  => [

                'present' => $presentCount,

                'late'    => $lateCount,

                'absent'  => $absentCount,
            ],

            'date'    => $date,

            'data'    => $data,
        ]);
    }
}
