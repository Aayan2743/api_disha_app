<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Client;
use Illuminate\Http\Request;

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

}