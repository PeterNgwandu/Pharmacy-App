<?php

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\SupplierPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SupplierPaymentController extends Controller
{
    // Record a payment against a purchase
    public function store(Request $request, Purchase $purchase)
    {
        // Validate submitted payment details
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'payment_method' => ['required', 'in:CASH,LIPA_HAPA,BANK'],
            'payment_reference' => ['nullable', 'string', 'max:255'],
            'payment_date' => ['required', 'date'],
            'notes' => ['nullable', 'string', 'max:1000']
        ]);

        // Lock the purchase while checking its outstanding balance and record the payment
        DB::transaction(function () use ($purchase, $validated) {

            $lockedPurchase = Purchase::whereKey($purchase->id)->lockForUpdate()->firstOrFail();

            // Calculate the total amount already paid
            $totalPaid = $lockedPurchase->payments()->sum('amount');

            // Calculate the remaining balance
            $purchaseTotal = (float) $lockedPurchase->total_amount;

            $outstandingBalance = round($purchaseTotal - (float) $totalPaid, 2);

            // Prevent payments when the purchase is already fully paid
            if($outstandingBalance <= 0){
                throw ValidationException::withMessages([
                    'amount' => 'This purchase is already fully paid'
                ]);
            }

            $paymentAmount = round((float) $validated['amount'], 2);

            // Prevent the payment from exceeding the outstanding balance
            if($paymentAmount > $outstandingBalance){
                throw ValidationException::withMessages([
                    'amount' => 'Amount cannot exceed the outstanding balance of TZS ' . number_format($outstandingBalance, 2). '.'
                ]);
            }

            // Record the payment
            SupplierPayment::create([
                'purchase_id' => $lockedPurchase->id,
                'amount' => $paymentAmount,
                'payment_method' => $validated['payment_method'],
                'payment_reference' => $validated['payment_reference'],
                'payment_date' => $validated['payment_date'],
                'notes' => $validated['notes'],
                'created_by' => Auth::user()->id,
            ]);

        });

        // Return to the purchase details page.
        return redirect()
                ->route('purchases.show', $purchase->id)
                ->with('success', 'Supplier payment recorded successfully.');

    }
}
