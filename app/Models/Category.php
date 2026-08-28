<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    // Relationship | Category has many medicines
    public function medicines ()
    {
        return $this->hasMany(Medicine::class);
    }
}
