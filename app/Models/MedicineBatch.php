<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedicineBatch extends Model
{
    protected $fillable = [
        'medicine_id',
        'batch_number',
        'manufactured_at',
        'expires_at',
        'purchase_price',
        'selling_price',
        'quantity',
        'status'
    ];

    protected $casts = [
        'manufactured_at' => 'date',
        'expires_at' => 'date',
        'quantity' => 'decimal:2',
        'purchase_price' => 'decimal:2',
        'selling_price' => 'decimal:2'
    ];

    // Get batches available for FEFO
    public function scopeAvailableForFefo($query)
    {
        return $query
                ->where('status', true)
                ->where('quantity', '>', 0)
                ->where('expires_at', '>=', today())
                ->orderBy('expires_at', 'asc');
    }

    public function medicine(): BelongsTo
    {
        return $this->belongsTo(Medicine::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
