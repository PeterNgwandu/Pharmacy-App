<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplierPayment extends Model
{
    protected $fillable = [
        'purchase_id',
        'amount',
        'payment_method',
        'payment_reference',
        'payment_date',
        'notes',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date'
    ];

    // Purchases associated with this payment
    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    // User who recorded this payment
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
