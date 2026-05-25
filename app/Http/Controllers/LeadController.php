<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;

class LeadController extends Controller
{
    /**
     * All Leads (Admin)
     *
     * Get all leads/clients with optional filters, staff_id, staff_name, and pagination.
     *
     * @group Admin
     *
     * @authenticated
     *
     * @queryParam staff_id integer optional Filter by staff (added_by) ID. Example: 4
     * @queryParam staff_name string optional Filter by staff name. Example: Aayan
     * @queryParam category string optional Filter by lead type. Possible values: hot,warm,cold,all. Example: hot
     * @queryParam from_date string optional Filter records from this date (Y-m-d). Example: 2026-05-01
     * @queryParam to_date string optional Filter records up to this date (Y-m-d). Example: 2026-05-25
     * @queryParam search string optional Search by name, phone or location. Example: John
     *
     * @response 200 {
     *  "status": true,
     *  "message": "Leads fetched successfully"
     * }
     */
    public function allLeads(Request $request)
    {
        $query = Client::query();

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

        // LEAD TYPE / CATEGORY FILTER
        if (
            $request->filled('category') &&
            $request->category != 'all'
        ) {

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

                $q->where('fullname', 'LIKE', '%' . $search . '%')
                    ->orWhere('phone', 'LIKE', '%' . $search . '%')
                    ->orWhere('location', 'LIKE', '%' . $search . '%');
            });
        }

        $clients = $query
            ->with('addedBy')
            ->latest()
            ->paginate(20);

        $data = collect($clients->items())->map(function ($item) {

            return [
                'id'        => $item->id,
                'client_no' => $item->fullname,
                'date'      => date('Y-m-d', strtotime($item->created_at)),
                'name'      => $item->fullname,
                'phone'     => $item->phone,
                'location'  => $item->location,
                'category'  => $item->lead_type,
                'notes'     => $item->remarks,
                'case_type' => $item->case_type,
                'added_by'  => $item->addedBy?->name ?? '-',
            ];
        });

        // COUNTS
        $totalCount = Client::count();

        $hotCount = Client::where('lead_type', 'hot')->count();

        $warmCount = Client::where('lead_type', 'warm')->count();

        $coldCount = Client::where('lead_type', 'cold')->count();

        return response()->json([

            'status'     => true,

            'message'    => 'Leads fetched successfully',

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
}
