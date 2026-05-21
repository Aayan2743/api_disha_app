<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    /**
     * Dashboard Analytics
     *
     * @group Dashboard
     *
     * @authenticated
     */

    public function dashboardAnalytics(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | Today Date
        |--------------------------------------------------------------------------
        */

        $today = date('Y-m-d');

        /*
        |--------------------------------------------------------------------------
        | Login User
        |--------------------------------------------------------------------------
        */

        $userId = auth()->id();

        /*
        |--------------------------------------------------------------------------
        | Total Calls
        |--------------------------------------------------------------------------
        */

        $totalCalls = Client::where(
            'added_by',
            $userId
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Today's Appointments
        |--------------------------------------------------------------------------
        */

        $appointments = Appointment::where(
            'added_by',
            $userId
        )
            ->whereDate(
                'appointment_date',
                $today
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Total Clients
        |--------------------------------------------------------------------------
        */

        $clients = Client::where(
            'added_by',
            $userId
        )->count();

        /*
        |--------------------------------------------------------------------------
        | Pending Followups
        |--------------------------------------------------------------------------
        */

        // $pendingFollowups = Followup::where(
        //     'added_by',
        //     $userId
        // )
        //     ->where(
        //         'status',
        //         0
        //     )
        //     ->count();

        /*
        |--------------------------------------------------------------------------
        | Calls This Month
        |--------------------------------------------------------------------------
        */

        $callsThisMonth = Client::where(
            'added_by',
            $userId
        )
            ->whereMonth(
                'created_at',
                now()->month
            )
            ->whereYear(
                'created_at',
                now()->year
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | Conversion Rate
        |--------------------------------------------------------------------------
        */

        $hotClients = Client::where(
            'added_by',
            $userId
        )
            ->where(
                'lead_type',
                'hot'
            )
            ->count();

        $conversionRate = $clients > 0
            ? round(
            ($hotClients / $clients) * 100
        )
            : 0;

        /*
        |--------------------------------------------------------------------------
        | Recent Activities
        |--------------------------------------------------------------------------
        */

        $recentAppointments = Appointment::where(
            'added_by',
            $userId
        )
            ->latest()
            ->take(5)
            ->get();

        $activities = [];

        foreach ($recentAppointments as $appointment) {

            $activities[] = [

                'id'               => $appointment->id,

                'title'            => $appointment->client_name,

                'subtitle'         => 'Appointment booked',

                'time'             => date(
                    'h:i A',
                    strtotime(
                        $appointment->created_at
                    )
                ),

                'date'             => \Carbon\Carbon::parse(
                    $appointment->created_at
                )->diffForHumans(),

                'appointment_type' => ucfirst(
                    $appointment->appointment_type
                ),

            ];
        }

        /*
        |--------------------------------------------------------------------------
        | Dashboard Response
        |--------------------------------------------------------------------------
        */

        return response()->json([

            'status'  => true,

            'message' => 'Dashboard Analytics Fetch Successfully',

            'data'    => [

                /*
                |--------------------------------------------------------------------------
                | Top Cards
                |--------------------------------------------------------------------------
                */

                'total_calls'         => $totalCalls,

                'clients'             => $clients,

                'appointments_today'  => $appointments,

                'followups'           => 0,

                /*
                |--------------------------------------------------------------------------
                | Conversion
                |--------------------------------------------------------------------------
                */

                'conversion_rate'     => $conversionRate,

                /*
                |--------------------------------------------------------------------------
                | Bottom Analytics
                |--------------------------------------------------------------------------
                */

                'client_satisfaction' => 92,

                'calls_this_month'    => $callsThisMonth,

                'pending_followups'   => 0,

                /*
                |--------------------------------------------------------------------------
                | Quick Actions
                |--------------------------------------------------------------------------
                */

                // 'quick_actions'       => [

                //     [
                //         'title'    => 'Add Client',
                //         'subtitle' => 'Create new',
                //     ],

                //     [
                //         'title'    => 'Appointment',
                //         'subtitle' => 'Schedule',
                //     ],

                // ],

                /*
                |--------------------------------------------------------------------------
                | Recent Activities
                |--------------------------------------------------------------------------
                */

                'recent_activities'   => $activities,

            ],

        ]);
    }

    /**
     * Reception Dashboard
     */

    public function receptionDashboard()
    {
        $today = Carbon::today();

        // TODAY APPOINTMENTS
        $todayAppointments = Appointment::whereDate(
            'appointment_date',
            $today
        )->get();

        // TOTAL CLIENTS
        $todayClients = $todayAppointments->count();

        // TOTAL COLLECTED
        $totalCollected = Appointment::whereDate(
            'appointment_date',
            $today
        )
            ->where('payment_status', 'completed')
            ->sum('fee_amount');

        // CASH COLLECTED
        $cashCollected = Appointment::whereDate(
            'appointment_date',
            $today
        )
            ->where('payment_status', 'completed')
            ->where('payment_collected_method', 'cash')
            ->sum('fee_amount');

        // UPI COLLECTED
        $upiCollected = Appointment::whereDate(
            'appointment_date',
            $today
        )
            ->where('payment_status', 'completed')
            ->where('payment_collected_method', 'upi')
            ->sum('fee_amount');

        // TODAY APPOINTMENTS STATUS
        $reachedCount = $todayAppointments
            ->where('status', 2)
            ->count();

        $paidCount = $todayAppointments
            ->where('payment_status', 'completed')
            ->count();

        $pendingFeeCount = $todayAppointments
            ->where('payment_status', 'pending')
            ->count();

        // RECENT WALKINS
        $recentWalkins = Appointment::whereDate(
            'appointment_date',
            $today
        )
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($item) {

                return [

                    'id'             => $item->id,

                    'client_name'    => $item->client_name,

                    'client_phone'   => $item->client_phone,

                    'location'       => $item->location,

                    'fee_amount'     => $item->fee_amount,

                    'payment_method' => $item->payment_collected_method,
                ];
            });

        return response()->json([

            'status'  => true,

            'message' => 'Dashboard fetched successfully',

            'data'    => [

                'today_clients'      => $todayClients,

                'total_collected'    => $totalCollected,

                'cash_collected'     => $cashCollected,

                'upi_collected'      => $upiCollected,

                'today_appointments' => [

                    'total'       => $todayClients,

                    'reached'     => $reachedCount,

                    'paid'        => $paidCount,

                    'fee_pending' => $pendingFeeCount,
                ],

                'recent_walkins'     => $recentWalkins,
            ],
        ]);
    }

    public function adminDashboard()
    {
        $today = Carbon::today();

        /*
        |--------------------------------------------------------------------------
        | TOTAL CLIENTS
        |--------------------------------------------------------------------------
        */

        $totalClients = Client::count();

        /*
        |--------------------------------------------------------------------------
        | ACTIVE CLIENTS
        |--------------------------------------------------------------------------
        */

        $activeCases = Client::where(
            'status',
            1
        )->count();

        /*
        |--------------------------------------------------------------------------
        | TOTAL COLLECTED
        |--------------------------------------------------------------------------
        */

        $totalCollected = Appointment::where(
            'payment_status',
            'completed'
        )
            ->sum('fee_amount');

        /*
        |--------------------------------------------------------------------------
        | TOTAL PENDING
        |--------------------------------------------------------------------------
        */

        $totalPending = Appointment::where(
            'payment_status',
            'pending'
        )
            ->sum('fee_amount');

        /*
        |--------------------------------------------------------------------------
        | TODAY LEADS
        |--------------------------------------------------------------------------
        */

        $todayTotal = Client::whereDate(
            'created_at',
            $today
        )->count();

        $todayHot = Client::whereDate(
            'created_at',
            $today
        )
            ->where(
                'lead_type',
                'hot'
            )
            ->count();

        $todayWarm = Client::whereDate(
            'created_at',
            $today
        )
            ->where(
                'lead_type',
                'warm'
            )
            ->count();

        $todayCold = Client::whereDate(
            'created_at',
            $today
        )
            ->where(
                'lead_type',
                'cold'
            )
            ->count();

        /*
        |--------------------------------------------------------------------------
        | TODAY COLLECTIONS
        |--------------------------------------------------------------------------
        */

        $todayConsultFees = Appointment::whereDate(
            'appointment_date',
            $today
        )
            ->where(
                'payment_status',
                'completed'
            )
            ->sum('fee_amount');

        /*
        |--------------------------------------------------------------------------
        | FOLLOWUPS
        |--------------------------------------------------------------------------
        */

        $todayFollowups = Appointment::whereDate(
            'appointment_date',
            $today
        )
            ->where(
                'status',
                1
            )
            ->count();

        return response()->json([

            'status'  => true,

            'message' => 'Admin dashboard fetched successfully',

            'data'    => [

                'total_clients'     => $totalClients,

                'active_cases'      => $activeCases,

                'collected_amount'  => $totalCollected,

                'pending_dues'      => $totalPending,

                'today_leads'       => [

                    'total' => $todayTotal,

                    'hot'   => $todayHot,

                    'warm'  => $todayWarm,

                    'cold'  => $todayCold,
                ],

                'today_collections' => [

                    'consult_fees' => $todayConsultFees,
                ],

                'followups'         => [

                    'today'         => $today->format('Y-m-d'),

                    'pending_count' => $todayFollowups,
                ],
            ],
        ]);
    }

}
