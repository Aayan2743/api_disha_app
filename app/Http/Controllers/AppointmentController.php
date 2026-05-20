<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;
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

    public function appointmentList_w(Request $request)
    {
        $query = Appointment::query();

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
    | Appointment Type
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
    | Client Type
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
    | Pagination
    |--------------------------------------------------------------------------
    */

        $perPage = $request->per_page ?? 10;

        $appointments = $query->latest()->paginate($perPage);

        /*
    |--------------------------------------------------------------------------
    | Counts
    |--------------------------------------------------------------------------
    */

        $countQuery = Appointment::whereDate(
            'appointment_date',
            $appointmentDate
        );

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
                        'H:i',
                        strtotime(
                            $appointment->appointment_time
                        )
                    ),

                    'fee_amount'       => $appointment->fee_amount,

                    'payment_method'   => $appointment->payment_method,

                    'remarks'          => $appointment->remarks,

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
}