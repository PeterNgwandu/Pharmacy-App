<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SaleItemBatch extends Model
{
     protected $fillable = [
        'sale_item_id',
        'medicine_batch_id',
        'quantity',
        'unit_price',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
    ];

    public function saleItem(): BelongsTo
    {
        return $this->belongsTo(SaleItem::class);
    }

    public function medicineBatch(): BelongsTo
    {
        return $this->belongsTo(MedicineBatch::class);
    }
}
