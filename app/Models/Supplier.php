<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'status',
        'created_by',
    ];

    protected $casts = [
        'status' => 'boolean'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

     public function payments(): HasMany
    {
        return $this->hasManyThrough(
            SupplierPayment::class,
            Purchase::class,
            'supplier_id',
            'purchase_id',
            'id',
            'id'
        );
    }
}
