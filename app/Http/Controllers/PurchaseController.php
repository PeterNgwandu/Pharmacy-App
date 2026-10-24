<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\Purchase;
use App\Models\MedicineBatch;
use App\Models\StockMovement;
use App\Models\PurchaseItem;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PurchaseController extends Controller
{
    public function index(Request $request)
    {
        $purchases = Purchase::with([
            'supplier',
            'creator',
            'items'
        ])
        ->when($request->search, function ($query) use ($request) {
            $query->where(function ($q) use ($request) {
                $q->where(
                    'purchase_number',
                    'like',
                    '%' . $request->search . '%'
                )
                ->orWhereHas('supplier', function ($supplierQuery) use ($request) {
                    $supplierQuery->where(
                        'name',
                        'like',
                        '%' . $request->search . '%'
                    );
                });
            });
        })
        ->when($request->from_date, function ($query) use ($request) {
            $query->whereDate(
                'purchase_date',
                '>=',
                $request->from_date
            );
        })
        ->when($request->to_date, function ($query) use ($request) {
            $query->whereDate(
                'purchase_date',
                '<=',
                $request->to_date
            );
        })
        ->latest()
        ->paginate(10)
        ->withQueryString();

        return view('purchases.index', compact('purchases'));
    }

    public function create()
    {
        $suppliers = Supplier::where('status', true)->orderBy('name')->get();
        $medicines = Medicine::where('status', true)->orderBy('name')->get();

        return view('purchases.create', compact('suppliers', 'medicines'));
    }

    public function show(Purchase $purchase)
    {
        $purchase->load([
            'supplier',
            'creator',
            'items.medicine',
            'payments.creator'
        ]);

        $totalPaid = $purchase->payments->sum('amount');

        $outstandingBalance = max(0, round( (float) $purchase->total_amount - (float) $totalPaid, 2));

        if($totalPaid <= 0){
            $paymentStatus = 'Unpaid';
        }elseif($outstandingBalance > 0){
            $paymentStatus = 'Partially Paid';
        }else{
            $paymentStatus = 'Paid';
        }

        return view('purchases.show', compact('purchase', 'totalPaid', 'outstandingBalance', 'paymentStatus'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => [
                'required',
                'integer',
                'exists:suppliers,id',
            ],

            'purchase_date' => [
                'required',
                'date',
            ],

            'items' => [
                'required',
                'array',
                'min:1',
            ],

            'items.*.medicine_id' => [
                'required',
                'integer',
                'exists:medicines,id',
            ],

            'items.*.batch_number' => [
                'nullable',
                'string',
                'max:255',
            ],

            'items.*.manufactured_at' => [
                'nullable',
                'date',
            ],

            'items.*.expires_at' => [
                'required',
                'date',
            ],

            'items.*.quantity' => [
                'required',
                'integer',
                'min:1',
            ],

            'items.*.purchase_price' => [
                'required',
                'numeric',
                'min:0',
            ],

            'items.*.selling_price' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | Check Supplier
        |--------------------------------------------------------------------------
        */

        $supplier = Supplier::where('id', $validated['supplier_id'])
            ->where('status', true)
            ->first();

        if (!$supplier) {
            return back()
                ->withInput()
                ->withErrors([
                    'supplier_id' => 'The selected supplier is inactive or does not exist.',
                ]);
        }

        DB::beginTransaction();

        try {

            /*
            |--------------------------------------------------------------------------
            | Calculate Grand Total
            |--------------------------------------------------------------------------
            */

            $grandTotal = 0;

            foreach ($validated['items'] as $item) {

                $subtotal =
                    $item['quantity'] *
                    $item['purchase_price'];

                $grandTotal += $subtotal;
            }


            /*
            |--------------------------------------------------------------------------
            | Generate Purchase Number
            |--------------------------------------------------------------------------
            */

            $lastPurchase = Purchase::latest('id')->first();

            $nextNumber = $lastPurchase
                ? $lastPurchase->id + 1
                : 1;

            $purchaseNumber =
                'PUR-' . str_pad(
                    $nextNumber,
                    6,
                    '0',
                    STR_PAD_LEFT
                );


            /*
            |--------------------------------------------------------------------------
            | Create Purchase
            |--------------------------------------------------------------------------
            */

            $purchase = Purchase::create([
                'supplier_id' => $validated['supplier_id'],
                'purchase_number' => $purchaseNumber,
                'purchase_date' => $validated['purchase_date'],
                'total_amount' => $grandTotal,
                'status' => 'Pending',
                'created_by' => Auth::id(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | Create Purchase Items
            |--------------------------------------------------------------------------
            */

            foreach ($validated['items'] as $item) {

                $subtotal =
                    $item['quantity'] *
                    $item['purchase_price'];

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,

                    'medicine_id' => $item['medicine_id'],

                    // Optional Supplier/Manufacturer Batch Number
                    'batch_number' => $item['batch_number'] ?? null,

                    'manufactured_at' =>
                        $item['manufactured_at'] ?? null,

                    'expires_at' =>
                        $item['expires_at'],

                    'quantity' =>
                        $item['quantity'],

                    'purchase_price' =>
                        $item['purchase_price'],

                    'selling_price' =>
                        $item['selling_price'],

                    'subtotal' =>
                        $subtotal,
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Commit Transaction
            |--------------------------------------------------------------------------
            */

            DB::commit();

            return redirect()
                ->route('purchases.show', $purchase->id)
                ->with(
                    'success',
                    'Purchase created successfully.'
                );

        } catch (\Throwable $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with(
                    'error',
                    'Unable to create purchase. Please try again.'
                );
        }
    }

    public function receive(Purchase $purchase)
    {
        // Prevent receiving purchase more than once
        if($purchase->status !== 'Pending'){
            return redirect()
                    ->route('purchases.show', $purchase->id)
                    ->with('error', 'Only pending purchases can be received');
        }

        DB::transaction(function () use ($purchase) {

            // Lock the purchase to prevent concurrent receiving.
            $purchase = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            // Check the status again after acquiring the lock.
            if($purchase->status !== 'Pending'){
                throw ValidationException::withMessages([
                    'purchase' => 'This purchase has already been received or is no longer pending.',
                ]);
            }

            // Load all purchase items.
            $purchase->load('items.medicine');

            if($purchase->items->isEmpty()){
                throw ValidationException::withMessages([
                     'purchase' => 'This purchase has no items to receive.',
                ]);
            }


            /*
            |--------------------------------------------------------------------------
            | Generate Pharmacy Batch Numbers and Receive Stock
            |--------------------------------------------------------------------------
            */

            foreach ($purchase->items as $item) {

            $medicine = $item->medicine;

            if (!$medicine) {
                throw ValidationException::withMessages([
                    'purchase' => 'A medicine associated with this purchase could not be found.',
                ]);
            }

            if (
                empty($medicine->units_per_pack) ||
                $medicine->units_per_pack < 1
            ) {
                throw ValidationException::withMessages([
                    'purchase' => "Invalid units per pack for {$medicine->name}.",
                ]);
            }

            // Prevent receiving expired stock.
            if (
                !$item->expires_at ||
                $item->expires_at->lt(today())
            ) {
                throw ValidationException::withMessages([
                    'purchase' => "The expiry date for {$medicine->name} is invalid or has already passed.",
                ]);
            }

            // Generate the pharmacy batch number using the existing format.
            $month = now()->format('Ym');
            $prefix = "BNO-{$month}-";

            $lastBatch = MedicineBatch::where(
                'batch_number',
                'like',
                "{$prefix}%"
            )
                ->orderByDesc('id')
                ->lockForUpdate()
                ->first();

            $lastNumber = $lastBatch
                ? (int) substr($lastBatch->batch_number, -3)
                : 0;

            $nextNumber = $lastNumber + 1;

            // Three digits allow numbers 001 through 999.
            if ($nextNumber > 999) {
                throw ValidationException::withMessages([
                    'purchase' => 'The monthly pharmacy batch-number limit has been reached.',
                ]);
            }

            $batchNumber = $prefix . str_pad(
                $nextNumber,
                3,
                '0',
                STR_PAD_LEFT
            );

            // Purchase quantity is recorded in packs.
            // Inventory quantity is recorded in base units.
            $baseQuantity =
                (int) $item->quantity *
                (int) $medicine->units_per_pack;

            // Create the pharmacy inventory batch.
            $medicineBatch = MedicineBatch::create([
                'medicine_id' => $item->medicine_id,
                'batch_number' => $batchNumber,
                'supplier_batch_number' => $item->batch_number,
                'manufactured_at' => $item->manufactured_at,
                'expires_at' => $item->expires_at,
                'purchase_price' => $item->purchase_price,
                'selling_price' => $item->selling_price,
                'quantity' => $baseQuantity,
                'status' => true,
            ]);

            // Record stock entering inventory.
            StockMovement::create([
                'medicine_id' => $item->medicine_id,
                'medicine_batch_id' => $medicineBatch->id,
                'type' => 'IN',
                'quantity' => $baseQuantity,
                'reference' => $batchNumber,
                'notes' => "Stock received from purchase {$purchase->purchase_number}",
                'created_by' => Auth::id(),
            ]);
            }

            // Mark the purchase as received only after all items are processed.
            $purchase->update([
                'status' => 'Received',
            ]);
            

        });

        return redirect()
                ->route('purchases.show', $purchase->id)
                ->with('sucess', 'Purchase received successfully. Inventory batches and stock movements have been created.');
    }
}
