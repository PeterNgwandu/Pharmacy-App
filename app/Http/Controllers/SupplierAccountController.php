<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SupplierAccountController extends Controller
{
    /**
     * Display supplier account summaries.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');

        $paymentStatus = $request->input('payment_status');

        $supplierOptions = Supplier::orderBy('name')->get([
            'id',
            'name',
        ]);

        $supplierId = $request->input('supplier_id');

        $suppliers = Supplier::query()
            ->select([
                'suppliers.id',
                'suppliers.name',
                'suppliers.phone',
                'suppliers.email',
                'suppliers.status',
            ])
            ->selectSub(
                DB::table('purchases')
                    ->selectRaw('COALESCE(SUM(purchases.total_amount), 0)')
                    ->whereColumn('purchases.supplier_id', 'suppliers.id'),
                'total_purchases'
            )
            ->selectSub(
                DB::table('purchases')
                    ->join(
                        'supplier_payments',
                        'supplier_payments.purchase_id',
                        '=',
                        'purchases.id'
                    )
                    ->selectRaw('COALESCE(SUM(supplier_payments.amount), 0)')
                    ->whereColumn('purchases.supplier_id', 'suppliers.id'),
                'total_paid'
            )
            ->when($search, function ($query, $search) {
                $query->where('suppliers.name', 'ilike', "%{$search}%");
            })
            ->when($supplierId, function ($query, $supplierId) {
                $query->where('suppliers.id', $supplierId);
            })
            ->when($paymentStatus, function ($query, $paymentStatus) {
                if ($paymentStatus === 'unpaid') {
                    $query->whereRaw(
                        '(SELECT COALESCE(SUM(p.total_amount), 0)
                          FROM purchases p
                          WHERE p.supplier_id = suppliers.id) > 0'
                    )->whereRaw(
                        '(SELECT COALESCE(SUM(sp.amount), 0)
                          FROM supplier_payments sp
                          JOIN purchases p ON p.id = sp.purchase_id
                          WHERE p.supplier_id = suppliers.id) = 0'
                    );
                } elseif ($paymentStatus === 'partial') {
                    $query->whereRaw(
                        '(SELECT COALESCE(SUM(sp.amount), 0)
                          FROM supplier_payments sp
                          JOIN purchases p ON p.id = sp.purchase_id
                          WHERE p.supplier_id = suppliers.id) > 0'
                    )->whereRaw(
                        '(SELECT COALESCE(SUM(sp.amount), 0)
                          FROM supplier_payments sp
                          JOIN purchases p ON p.id = sp.purchase_id
                          WHERE p.supplier_id = suppliers.id)
                         <
                         (SELECT COALESCE(SUM(p.total_amount), 0)
                          FROM purchases p
                          WHERE p.supplier_id = suppliers.id)'
                    );
                } elseif ($paymentStatus === 'paid') {
                    $query->whereRaw(
                        '(SELECT COALESCE(SUM(sp.amount), 0)
                          FROM supplier_payments sp
                          JOIN purchases p ON p.id = sp.purchase_id
                          WHERE p.supplier_id = suppliers.id)
                         >=
                         (SELECT COALESCE(SUM(p.total_amount), 0)
                          FROM purchases p
                          WHERE p.supplier_id = suppliers.id)'
                    )->whereRaw(
                        '(SELECT COALESCE(SUM(p.total_amount), 0)
                          FROM purchases p
                          WHERE p.supplier_id = suppliers.id) > 0'
                    );
                }
            })
            ->orderBy('suppliers.name')
            ->paginate(15)
            ->withQueryString();

        // Overall totals across all suppliers, independent of search filters.
        $summary = DB::table('purchases')
            ->selectRaw('COALESCE(SUM(total_amount), 0) AS total_purchases')
            ->first();

        $totalPaid = DB::table('supplier_payments')
            ->sum('amount');

        $totalPurchases = (float) $summary->total_purchases;

        $summary = [
            'total_purchases' => $totalPurchases,
            'total_paid' => (float) $totalPaid,
            'outstanding_balance' => max(
                0,
                round($totalPurchases - (float) $totalPaid, 2)
            ),
        ];

        return view('supplier_accounts.index', compact(
            'suppliers',
            'supplierId',
            'supplierOptions',
            'search',
            'paymentStatus',
            'summary'
        ));
    }
}