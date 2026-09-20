<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SaleItemBatch;
use App\Models\User;
use App\Models\StockMovement;
use App\Models\Payment;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    public function index()
    {
        return view('sales.index');
    }

    public function create()
    {
        $medicines = Medicine::where('status', true)->orderBy('name')->get();
        return view('sales.create', compact('medicines'));
    }

    public function preview(Request $request)
    {
        $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.medicine_id' => [
                'required',
                'integer',
                'exists:medicines,id'
            ],
            'items.*.quantity' => [
                'required',
                'integer',
                'min:1'
            ],
        ]);

        $results = [];

        $grandTotal = 0;

        foreach ($request->items as $item) {

            $medicine = Medicine::findOrFail(
                $item['medicine_id']
            );

            $requestedPacks = (int) $item['quantity'];

            /*
            |--------------------------------------------------------------------------
            | Get available batches using FEFO
            |--------------------------------------------------------------------------
            */

            $batches = MedicineBatch::availableForFefo()
                ->where('medicine_id', $medicine->id)
                ->get();


            $remainingPacks = $requestedPacks;

            $itemTotal = 0;

            $allocations = [];


            /*
            |--------------------------------------------------------------------------
            | Allocate requested packs from batches
            |--------------------------------------------------------------------------
            */

            foreach ($batches as $batch) {

                \Log::info('FEFO DEBUG', [
                    'medicine' => $medicine->name,
                    'units_per_pack' => $medicine->units_per_pack,
                    'batch_id' => $batch->id,
                    'batch_number' => $batch->batch_number,
                    'quantity' => $batch->quantity,
                    'status' => $batch->status,
                    'expires_at' => $batch->expires_at,
                ]);

                if ($remainingPacks <= 0) {
                    break;
                }

            }

            foreach ($batches as $batch) {
                

                if ($remainingPacks <= 0) {
                    break;
                }


                /*
                |--------------------------------------------------------------------------
                | Batch quantity is stored in base units.
                | Convert it to complete packs.
                |--------------------------------------------------------------------------
                */

                $availablePacks = intdiv(
                    (int) $batch->quantity,
                    (int) $medicine->units_per_pack
                );


                if ($availablePacks <= 0) {
                    continue;
                }


                /*
                |--------------------------------------------------------------------------
                | Determine how many packs to take from this batch
                |--------------------------------------------------------------------------
                */

                $allocatedPacks = min(
                    $availablePacks,
                    $remainingPacks
                );


                /*
                |--------------------------------------------------------------------------
                | Calculate subtotal using batch selling price
                |--------------------------------------------------------------------------
                */

                $subtotal =
                    $allocatedPacks *
                    (float) $batch->selling_price;


                $itemTotal += $subtotal;


                /*
                |--------------------------------------------------------------------------
                | Store FEFO allocation information
                |--------------------------------------------------------------------------
                */

                $allocations[] = [

                    'batch_id' =>
                        $batch->id,

                    'batch_number' =>
                        $batch->batch_number,

                    'expires_at' =>
                        $batch->expires_at->format('Y-m-d'),

                    'quantity' =>
                        $allocatedPacks,

                    'unit_price' =>
                        (float) $batch->selling_price,

                    'subtotal' =>
                        $subtotal,

                ];


                $remainingPacks -= $allocatedPacks;

            }


            /*
            |--------------------------------------------------------------------------
            | Check stock availability
            |--------------------------------------------------------------------------
            */

            if ($remainingPacks > 0) {

                return response()->json([

                    'success' => false,

                    'message' =>
                        "Insufficient stock for {$medicine->name}.",

                ], 422);
            }


            /*
            |--------------------------------------------------------------------------
            | Effective price per pack
            |--------------------------------------------------------------------------
            */

            $effectiveUnitPrice =
                $itemTotal / $requestedPacks;


            $results[] = [

                'medicine_id' =>
                    $medicine->id,

                'medicine_name' =>
                    $medicine->name,

                'quantity' =>
                    $requestedPacks,

                'pack_unit' =>
                    $medicine->pack_unit,

                'unit_price' =>
                    $effectiveUnitPrice,

                'subtotal' =>
                    $itemTotal,

                'allocations' =>
                    $allocations,

            ];


            $grandTotal += $itemTotal;

        }


        return response()->json([

            'success' => true,

            'items' => $results,

            'grand_total' => $grandTotal,

        ]);
    }

    private function calculateFefo(
        int $medicineId,
        int $requestedPacks
    ): array {

        $medicine = Medicine::findOrFail($medicineId);


        $batches = MedicineBatch::availableForFefo()
            ->where('medicine_id', $medicineId)
            ->lockForUpdate()
            ->get();


        $remainingPacks = $requestedPacks;

        $allocations = [];

        $totalAmount = 0;


        foreach ($batches as $batch) {

            if ($remainingPacks <= 0) {
                break;
            }


            /*
            |--------------------------------------------------------------------------
            | Convert batch stock from base units to complete packs
            |--------------------------------------------------------------------------
            */

            $availablePacks = intdiv(
                (int) $batch->quantity,
                $medicine->units_per_pack
            );


            if ($availablePacks <= 0) {
                continue;
            }


            /*
            |--------------------------------------------------------------------------
            | Allocate packs from this batch
            |--------------------------------------------------------------------------
            */

            $allocatedPacks = min(
                $availablePacks,
                $remainingPacks
            );


            /*
            |--------------------------------------------------------------------------
            | Calculate price
            |--------------------------------------------------------------------------
            */

            $subtotal =
                $allocatedPacks *
                $batch->selling_price;


            $totalAmount += $subtotal;


            /*
            |--------------------------------------------------------------------------
            | Save allocation
            |--------------------------------------------------------------------------
            */

            $allocations[] = [

                'medicine_batch_id' =>
                    $batch->id,

                'batch_number' =>
                    $batch->batch_number,

                'expires_at' =>
                    $batch->expires_at->format('Y-m-d'),

                'quantity' =>
                    $allocatedPacks,

                'unit_price' =>
                    (float) $batch->selling_price,

                'subtotal' =>
                    $subtotal,

            ];


            $remainingPacks -=
                $allocatedPacks;

        }


        /*
        |--------------------------------------------------------------------------
        | Insufficient stock
        |--------------------------------------------------------------------------
        */

        if ($remainingPacks > 0) {

            throw \Illuminate\Validation\ValidationException::withMessages([

                'quantity' =>
                    "Insufficient stock for {$medicine->name}. " .
                    "Requested {$requestedPacks} packs, " .
                    "but only " .
                    ($requestedPacks - $remainingPacks) .
                    " packs are available."

            ]);

        }


        return [

            'medicine_id' =>
                $medicine->id,

            'medicine_name' =>
                $medicine->name,

            'requested_packs' =>
                $requestedPacks,

            'total_amount' =>
                $totalAmount,

            'allocations' =>
                $allocations,

        ];
    }

    public function store(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | 1. VALIDATE REQUEST
        |--------------------------------------------------------------------------
        */

        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],

            'items.*.medicine_id' => [
                'required',
                'integer',
                'exists:medicines,id'
            ],

            'items.*.quantity' => [
                'required',
                'numeric',
                'integer',
                'min:1'
            ],

            'payment_method' => [
                'required',
                'in:CASH,LIPA_HAPA,BANK',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:0',
            ],
        ]);


        /*
        |--------------------------------------------------------------------------
        | 2. START DATABASE TRANSACTION
        |--------------------------------------------------------------------------
        */

        try {

            $sale = DB::transaction(function () use ($validated) {

                /*
                |--------------------------------------------------------------------------
                | 3. CALCULATE TOTAL
                |--------------------------------------------------------------------------
                */

                $grandTotal = 0;

                $cartItems = [];


                /*
                |--------------------------------------------------------------------------
                | 4. PREPARE CART ITEMS
                |--------------------------------------------------------------------------
                */

                foreach ($validated['items'] as $item) {

                    $medicine = Medicine::with('batches')
                        ->findOrFail($item['medicine_id']);

                    $packQuantity = (int) $item['quantity'];

                    /*
                    |--------------------------------------------------------------------------
                    | Get FEFO batches
                    |--------------------------------------------------------------------------
                    */

                    $batches = MedicineBatch::where('medicine_id', $medicine->id)
                        ->where('status', true)
                        ->where('quantity', '>', 0)
                        ->whereDate('expires_at', '>=', now()->toDateString())
                        ->orderBy('expires_at', 'asc')
                        ->lockForUpdate()
                        ->get();


                    if ($batches->isEmpty()) {
                        throw new \Exception(
                            "No available stock for {$medicine->name}."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Units per pack
                    |--------------------------------------------------------------------------
                    */

                    $unitsPerPack = (int) $medicine->units_per_pack;

                    if ($unitsPerPack <= 0) {
                        throw new \Exception(
                            "Invalid units per pack for {$medicine->name}."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Convert packs to base units
                    |--------------------------------------------------------------------------
                    */

                    $requiredBaseUnits = $packQuantity * $unitsPerPack;


                    /*
                    |--------------------------------------------------------------------------
                    | Calculate available stock
                    |--------------------------------------------------------------------------
                    */

                    $availableBaseUnits = $batches->sum(function ($batch) {
                        return (int) $batch->quantity;
                    });


                    if ($availableBaseUnits < $requiredBaseUnits) {

                        throw new \Exception(
                            "Insufficient stock for {$medicine->name}. " .
                            "Required: {$requiredBaseUnits} {$medicine->base_unit}, " .
                            "Available: {$availableBaseUnits} {$medicine->base_unit}."
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Use the first FEFO batch price
                    |--------------------------------------------------------------------------
                    |
                    | The selling price is stored per pack.
                    |
                    */

                    $sellingPrice = (float) $batches->first()->selling_price;


                    $subtotal = $packQuantity * $sellingPrice;

                    $grandTotal += $subtotal;


                    /*
                    |--------------------------------------------------------------------------
                    | Store prepared cart item
                    |--------------------------------------------------------------------------
                    */

                    $cartItems[] = [
                        'medicine' => $medicine,
                        'pack_quantity' => $packQuantity,
                        'required_base_units' => $requiredBaseUnits,
                        'selling_price' => $sellingPrice,
                        'subtotal' => $subtotal,
                        'batches' => $batches,
                    ];
                }


                /*
                |--------------------------------------------------------------------------
                | 5. GENERATE INVOICE NUMBER
                |--------------------------------------------------------------------------
                */

                $invoiceNumber = $this->generateInvoiceNumber();


                /*
                |--------------------------------------------------------------------------
                | 6. CREATE SALE
                |--------------------------------------------------------------------------
                */

                $sale = Sale::create([
                    'invoice_number' => $invoiceNumber,
                    'total_amount' => $grandTotal,
                    'created_by' => Auth::id(),
                ]);


                /*
                |--------------------------------------------------------------------------
                | 7. PROCESS EACH SALE ITEM
                |--------------------------------------------------------------------------
                */

                foreach ($cartItems as $cartItem) {

                    $medicine = $cartItem['medicine'];

                    $remainingBaseUnits =
                        $cartItem['required_base_units'];


                    /*
                    |--------------------------------------------------------------------------
                    | Create Sale Item
                    |--------------------------------------------------------------------------
                    */

                    $saleItem = SaleItem::create([
                        'sale_id' => $sale->id,
                        'medicine_id' => $medicine->id,
                        'quantity' => $cartItem['pack_quantity'],
                        'unit_price' => $cartItem['selling_price'],
                        'subtotal' => $cartItem['subtotal'],
                    ]);


                    /*
                    |--------------------------------------------------------------------------
                    | 8. FEFO BATCH ALLOCATION
                    |--------------------------------------------------------------------------
                    */

                    foreach ($cartItem['batches'] as $batch) {

                        if ($remainingBaseUnits <= 0) {
                            break;
                        }


                        $batchAvailable =
                            (int) $batch->quantity;


                        /*
                        |--------------------------------------------------------------------------
                        | Amount taken from this batch
                        |--------------------------------------------------------------------------
                        */

                        $allocatedBaseUnits = min(
                            $remainingBaseUnits,
                            $batchAvailable
                        );


                        /*
                        |--------------------------------------------------------------------------
                        | Deduct stock
                        |--------------------------------------------------------------------------
                        */

                        $batch->quantity =
                            $batchAvailable - $allocatedBaseUnits;

                        $batch->save();


                        /*
                        |--------------------------------------------------------------------------
                        | Create Sale Item Batch
                        |--------------------------------------------------------------------------
                        */

                        SaleItemBatch::create([
                            'sale_item_id' => $saleItem->id,
                            'medicine_batch_id' => $batch->id,
                            'quantity' => $allocatedBaseUnits,
                            'unit_price' => $cartItem['selling_price'],
                        ]);


                        /*
                        |--------------------------------------------------------------------------
                        | Create Stock Movement
                        |--------------------------------------------------------------------------
                        */

                        StockMovement::create([
                            'medicine_id' => $medicine->id,
                            'medicine_batch_id' => $batch->id,
                            'type' => 'OUT',
                            'quantity' => $allocatedBaseUnits,
                            'reference' => $invoiceNumber,
                            'notes' => 'Medicine sold - FEFO allocation',
                            'created_by' => Auth::id(),
                        ]);


                        /*
                        |--------------------------------------------------------------------------
                        | Update remaining quantity
                        |--------------------------------------------------------------------------
                        */

                        $remainingBaseUnits -= $allocatedBaseUnits;
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Safety Check
                    |--------------------------------------------------------------------------
                    */

                    if ($remainingBaseUnits > 0) {

                        throw new \Exception(
                            "Unable to allocate complete stock for {$medicine->name}."
                        );
                    }
                }


                /*
                |--------------------------------------------------------------------------
                | 9. CREATE PAYMENT
                |--------------------------------------------------------------------------
                */

                $sale->payment()->create([
                    'amount' => $grandTotal,
                    'payment_method' => $validated['payment_method'],
                ]);


                /*
                |--------------------------------------------------------------------------
                | 10. RETURN SALE
                |--------------------------------------------------------------------------
                */

                return $sale;
            });


            /*
            |--------------------------------------------------------------------------
            | 11. SUCCESS
            |--------------------------------------------------------------------------
            */

            return redirect()
                ->route('sales.show', $sale->id)
                ->with(
                    'success',
                    "Sale {$sale->invoice_number} completed successfully."
                );


        } catch (\Throwable $e) {

            /*
            |--------------------------------------------------------------------------
            | LOG ERROR
            |--------------------------------------------------------------------------
            */

            Log::error('Sale creation failed', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);


            /*
            |--------------------------------------------------------------------------
            | RETURN ERROR
            |--------------------------------------------------------------------------
            */

            return back()
                ->withInput()
                ->with(
                    'error',
                    $e->getMessage()
                );
        }
    }

    /**
     * Generate unique invoice number.
     */
    private function generateInvoiceNumber()
    {
        do {

            $invoiceNumber =
                'INV-' .
                now()->format('Ymd') .
                '-' .
                strtoupper(substr(uniqid(), -6));

        } while (
            Sale::where(
                'invoice_number',
                $invoiceNumber
            )->exists()
        );


        return $invoiceNumber;
    }
}
