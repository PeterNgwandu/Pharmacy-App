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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
}
