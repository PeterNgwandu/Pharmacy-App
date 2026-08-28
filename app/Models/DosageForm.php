<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Fa\Schema;

class DosageForm extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    // Relationships | Dosage has many Medicines
    public function medicines()
    {
        return $this->hasMany(Medicine::class);
    }
}
