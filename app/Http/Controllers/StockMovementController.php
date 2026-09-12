<?php

namespace App\Http\Controllers;

use App\Models\Medicine;
use Illuminate\Http\Request;
use App\Models\StockMovement;
use App\Models\MedicineBatch;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class StockMovementController extends Controller
{
    public function index ()
    {
        $stockMovements = StockMovement::with(['medicine', 'medicineBatch', 'creator'])->latest()->get();
        return view('stock_movements.index', compact('stockMovements'));
    }

    public function createAdjustment()
    {
        $batches = MedicineBatch::with('medicine')
                    ->where('status', true)
                    // ->where('quantity', '>', 0)
                    ->orderByDesc('expires_at', 'asc')
                    ->get();

        return view('stock_movements.adjustment', compact('batches'));
    }

    public function adjustmentStore(Request $request)
    {
        $request->validate([
            'medicine_batch_id' => [
                'required',
                'exists:medicine_batches,id'
            ],

            'adjustment_type' => [
                'required',
                'in:increase,decrease'
            ],

            'quantity' => [
                'required',
                'numeric',
                'min:1'
            ],

            'notes' => [
                'required',
                'string',
                'max:1000'
            ],
        ]);

        DB::transaction(function() use ($request){

            $medicineBatch = MedicineBatch::lockForUpdate()
                                ->findOrFail($request->medicine_batch_id);
            
            $quantity = $request->quantity;

            // Determine Adjustment

            if($request->adjustment_type === 'increase'){

                $movementQuantity = $quantity;

                $newQuantity = $medicineBatch->quantity + $quantity;

            }else {

                // Prevent Negative Stock
                if($quantity > $medicineBatch->quantity){

                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'quantity' => 'Adjustment quantity cannot be greater than current stock.'
                    ]);

                }

                $movementQuantity = -$quantity;

                $newQuantity = $medicineBatch->quantity - $quantity;

            }

            // Update Batch Quantity

            $medicineBatch->update([
                'quantity' => $newQuantity
            ]);

            // Generate Adjustment Reference

            $month = now()->format('Ym');

            $lastMovement = StockMovement::where(
                'reference',
                'like',
                "ADJ-$month-%"
            )
            ->orderBy('id', 'desc')
            ->first();


            if ($lastMovement) {

                $lastNumber = (int) substr(
                    $lastMovement->reference,
                    -3
                );

                $nextNumber = $lastNumber + 1;

            } else {

                $nextNumber = 1;

            }


            $reference = 'ADJ-' . $month . '-' . str_pad(
                $nextNumber,
                3,
                '0',
                STR_PAD_LEFT
            );

            // Create Stock Movement

            StockMovement::create([
                'medicine_id' => $medicineBatch->medicine_id,
                'medicine_batch_id' => $medicineBatch->id,
                'type' => 'ADJUSTMENT',
                'quantity' => $movementQuantity,
                'reference' => $reference,
                'notes' => $request->notes,
                'created_by' => Auth::user()->id
            ]);

        });

        return redirect()
                ->route('stock_movement.listings')
                ->with('success', 'Stock adjustment completed successfully');

    }
}
