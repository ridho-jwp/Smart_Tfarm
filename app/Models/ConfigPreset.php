<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConfigPreset extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'ph_min',
        'ph_max',
        'suhu_min',
        'suhu_max',
        'ppm_min',
        'ppm_max',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];
}
