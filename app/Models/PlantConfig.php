<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'parameter',
        'min_optimal',
        'max_optimal',
        'unit',
        'label',
    ];
}
