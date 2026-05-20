<?php
namespace App\Http\Controllers;

use App\Models\Followup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FollowupController extends Controller
{
    public function addFollowup(Request $request)
    {
        $validator = Validator::make($request->all(), [

            'client_id'      => 'required|exists:clients,id',

            'appointment_id' => 'nullable|exists:appointments,id',

            'followup_date'  => 'required|date',

            'remarks'        => 'required|string',

        ]);

        if ($validator->fails()) {

            return response()->json([

                'status'  => false,

                'message' => $validator->errors()->first(),

            ], 422);
        }

        /*
        |--------------------------------------------------------------------------
        | Create Followup
        |--------------------------------------------------------------------------
        */

        $followup = Followup::create([

            'client_id'      => $request->client_id,

            'appointment_id' => $request->appointment_id,

            'followup_date'  => $request->followup_date,

            'remarks'        => $request->remarks,

            'status'         => 0,

            'added_by'       => auth()->id(),

        ]);

        return response()->json([

            'status'  => true,

            'message' => 'Followup Added Successfully',

            'data'    => [

                'id'             => $followup->id,

                'client_id'      => $followup->client_id,

                'appointment_id' => $followup->appointment_id,

                'followup_date'  => $followup->followup_date,

                'remarks'        => $followup->remarks,

                'status'         => $followup->status,

                'status_text'    => 'Pending',

                'created_at'     => date(
                    'd M Y h:i A',
                    strtotime(
                        $followup->created_at
                    )
                ),

            ],

        ]);
    }

    /**
     * Followup List
     *
     * @group Followup
     *
     * @authenticated
     *
     * @queryParam filter string optional Possible values: all,today,this_week,pending. Example: today
     *
     * @queryParam page integer optional Page number. Example: 1
     *
     * @queryParam per_page integer optional Per page records. Example: 10
     */
    public function followupList(Request $request)
    {
        $query = Followup::with([
            'client',
            'addedBy',
        ]);

        /*
    |--------------------------------------------------------------------------
    | Default Pending
    |--------------------------------------------------------------------------
    */

        $filter = $request->filter ?? 'pending';

        /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

        // All
        if ($filter == 'all') {

            // No condition
        }

        // Today
        elseif ($filter == 'today') {

            $query->whereDate(
                'followup_date',
                today()
            );
        }

        // This Week
        elseif ($filter == 'this_week') {

            $query->whereBetween('followup_date', [

                now()->startOfWeek(),

                now()->endOfWeek(),

            ]);
        }

        // Pending
        elseif ($filter == 'pending') {

            $query->where('status', 0);
        }

        /*
    |--------------------------------------------------------------------------
    | My Followups Only
    |--------------------------------------------------------------------------
    */

        // $query->where('added_by', auth()->id());

        /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

        $perPage = $request->per_page ?? 10;

        $followups = $query->latest()->paginate($perPage);

        /*
    |--------------------------------------------------------------------------
    | Counts
    |--------------------------------------------------------------------------
    */

        $countQuery = Followup::where(
            'added_by',
            auth()->id()
        );

        $counts = [

            'all'       => (clone $countQuery)->count(),

            'today'     => (clone $countQuery)
                ->whereDate(
                    'followup_date',
                    today()
                )
                ->count(),

            'this_week' => (clone $countQuery)
                ->whereBetween('followup_date', [

                    now()->startOfWeek(),

                    now()->endOfWeek(),

                ])
                ->count(),

            'pending'   => (clone $countQuery)
                ->where('status', 0)
                ->count(),

        ];

        /*
    |--------------------------------------------------------------------------
    | Response
    |--------------------------------------------------------------------------
    */

        return response()->json([

            'status'     => true,

            'message'    => 'Followup List Fetch Successfully',

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

                'current_page' => $followups->currentPage(),

                'last_page'    => $followups->lastPage(),

                'per_page'     => $followups->perPage(),

                'total'        => $followups->total(),

            ],

            /*
        |--------------------------------------------------------------------------
        | Followup List
        |--------------------------------------------------------------------------
        */

            'data'       => collect($followups->items())->map(function ($followup) {

                return [

                    'id'            => $followup->id,

                    /*
                |--------------------------------------------------------------------------
                | Client Details
                |--------------------------------------------------------------------------
                */

                    'client'        => [

                        'id'    => $followup->client?->id,

                        'name'  => $followup->client?->fullname,

                        'phone' => $followup->client?->phone,

                    ],

                    /*
                |--------------------------------------------------------------------------
                | Followup Details
                |--------------------------------------------------------------------------
                */

                    'followup_date' => $followup->followup_date,

                    'followup_day'  => \Carbon\Carbon::parse(
                        $followup->followup_date
                    )->isToday()
                        ? 'Today'
                        : \Carbon\Carbon::parse(
                        $followup->followup_date
                    )->diffForHumans(),

                    'followup_time' => date(
                        'h:i A',
                        strtotime(
                            $followup->created_at
                        )
                    ),

                    'remarks'       => $followup->remarks,

                    /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                    'status'        => $followup->status,

                    'status_text'   => $followup->status == 0
                        ? 'Pending'
                        : 'Completed',

                    /*
                |--------------------------------------------------------------------------
                | Added By
                |--------------------------------------------------------------------------
                */

                    'added_by'      => [

                        'id'   => $followup->addedBy?->id,

                        'name' => $followup->addedBy?->name,

                    ],

                    /*
                |--------------------------------------------------------------------------
                | UI Actions
                |--------------------------------------------------------------------------
                */

                    'actions'       => [

                        'call'     => true,

                        'message'  => true,

                        'schedule' => true,

                    ],

                ];

            }),

        ]);
    }
}