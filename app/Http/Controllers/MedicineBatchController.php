<?php

namespace App\Http\Controllers;

use App\Models\MedicineBatch;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use App\Models\Medicine;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class MedicineBatchController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $batches = MedicineBatch::with('medicine')->orderBy('expires_at', 'asc')->get();
        return view('medicines_batch.index', compact('batches'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $medicines = Medicine::orderBy('name')->get();
        return view('medicines_batch.create', compact('medicines'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            //'batch_number' => ['required', 'string', 'max:255', Rule::unique('medicine_batches', 'batch_number')->where(fn ($query) => $query->where('medicine_id', $request->medicine_id))],
            'manufactured_at' => ['nullable', 'date'],
            'expires_at' => ['required', 'date', 'after:manufactured_at'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'status' => ['required', 'boolean']
        ]);

        $batchNumber = DB::transaction(function () use ($request) {

        $month = now()->format('Ym');

        $lastBatch = MedicineBatch::where('batch_number', 'like', "BNO-$month-%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastBatch) {
            $lastNumber = (int) substr($lastBatch->batch_number, -3);
            $nextNumber = $lastNumber + 1;
        } else {
            $nextNumber = 1;
        }

        $batchNumber = 'BNO-' . $month . '-' . str_pad(
            $nextNumber,
            3,
            '0',
            STR_PAD_LEFT
        );

        // Get Medicine
        $medicine = Medicine::findOrFail($request->medicine_id);

        $baseQuantity = $request->quantity * $medicine->units_per_pack;

        // Create Medicine Batch
        $medicineBatch = MedicineBatch::create([
            'medicine_id' => $request->medicine_id,
            'batch_number' => $batchNumber,
            'manufactured_at' => $request->manufactured_at,
            'expires_at' => $request->expires_at,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'quantity' => $baseQuantity,
            'status' => $request->status,
        ]);

        // Create Stock Movement
        StockMovement::create([
            'medicine_id' => $request->medicine_id,
            'medicine_batch_id' => $medicineBatch->id,
            'type' => 'IN',
            'quantity' => $baseQuantity,
            'reference' => $batchNumber,
            'notes' => 'Medicine received into stock',
            'created_by' => Auth::user()->id
        ]);

        return $batchNumber;
    });

    return redirect()
            ->route('medicines_batch.index')
            ->with('success', "Medicine Received Successfully. Batch Number: {$batchNumber}");
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(MedicineBatch $medicineBatch)
    {
        $medicines = Medicine::orderBy('name')->get();
        return view('medicines_batch.edit', compact('medicineBatch', 'medicines'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, MedicineBatch $medicineBatch)
    {
        $request->validate([
            'medicine_id' => ['required', 'exists:medicines,id'],
            'manufactured_at' => ['nullable', 'date'],
            'expires_at' => ['required', 'date', 'after:manufactured_at'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'numeric', 'min:1'],
            'status' => ['required', 'boolean'],
        ]);

        $medicineBatch->update([
            'medicine_id' => $request->medicine_id,
            'manufactured_at' => $request->manufactured_at,
            'expires_at' => $request->expires_at,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'quantity' => $request->quantity,
            'status' => $request->status,
        ]);

        return redirect()
                ->route('medicines_batch.index')
                ->with('success', 'Medicine Batch updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MedicineBatch $medicineBatch)
    {
        $batchNumber = $medicineBatch->batch_number;
        $medicineBatch->delete();

        return redirect()
                ->route('medicines_batch.index')
                ->with('success', "Medicine Batch Deleted Successfull: Batch Number - {$batchNumber}");
    }

    // FEEO Implementation
    public function testFefo(Request $request)
    {
        $medicines = Medicine::orderBy('name')->get();

        $batches = collect();

        if ($request->filled('medicine_id')) {

            $request->validate([
                'medicine_id' => [
                    'required',
                    'exists:medicines,id'
                ],
            ]);

            $batches = MedicineBatch::availableForFefo()
                ->with('medicine')
                ->where('medicine_id', $request->medicine_id)
                ->get();
        }

        return view(
            'medicines_batch.fefo_test',
            compact('batches', 'medicines')
        );
    }
}
