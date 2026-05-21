<?php
namespace App\Http\Controllers;

use App\Models\Appointment;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
    public function allCollections(Request $request)
    {
        $query = Appointment::query();

        // ONLY COMPLETED PAYMENTS
        $query->where('payment_status', 'completed');

        // TYPE FILTER
        if (
            $request->filled('type') &&
            $request->type != 'all'
        ) {

            $query->where(
                'payment_type',
                $request->type
            );
        }

        // FROM DATE
        if ($request->filled('from_date')) {

            $query->whereDate(
                'appointment_date',
                '>=',
                $request->from_date
            );
        }

        // TO DATE
        if ($request->filled('to_date')) {

            $query->whereDate(
                'appointment_date',
                '<=',
                $request->to_date
            );
        }

        // MIN AMOUNT
        if ($request->filled('min_amount')) {

            $query->where(
                'fee_amount',
                '>=',
                $request->min_amount
            );
        }

        // MAX AMOUNT
        if ($request->filled('max_amount')) {

            $query->where(
                'fee_amount',
                '<=',
                $request->max_amount
            );
        }

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
                        'referance',
                        'LIKE',
                        '%' . $search . '%'
                    );
            });
        }

        $collections = $query
            ->with('addedBy')
            ->latest()
            ->paginate(20);

        $data = collect($collections->items())->map(function ($item) {

            return [

                'id'           => $item->id,

                'date'         => $item->appointment_date,

                'client_name'  => $item->client_name,

                'client_no'    => 'C-' . $item->client_id,

                'phone'        => $item->client_phone,

                'amount'       => $item->fee_amount,

                'method'       => ucfirst(
                    $item->payment_collected_method
                ),

                'payment_type' => $item->payment_type,

                'collected_by' =>
                $item->addedBy?->name ?? '-',
            ];
        });

        // SUMMARY
        $totalAmount = $query->sum('fee_amount');

        $avgAmount = $query->avg('fee_amount');

        return response()->json([

            'status'     => true,

            'message'    => 'Collections fetched successfully',

            'summary'    => [

                'rows'           => $collections->total(),

                'total_amount'   => $totalAmount,

                'average_amount' => round($avgAmount),

            ],

            'pagination' => [

                'current_page' => $collections->currentPage(),

                'last_page'    => $collections->lastPage(),

                'per_page'     => $collections->perPage(),

                'total'        => $collections->total(),
            ],

            'data'       => $data,
        ]);
    }
}
