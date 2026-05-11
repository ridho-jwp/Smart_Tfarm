<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SensorData extends Model
{
    protected $table = 'sensors_data';

    protected $fillable = [
        'device_id',
        'ph',
        'suhu',
        'ppm',
        'water_level',
        'voltage',
        'current',
        'power',
        'energy',
        'frequency',
        'power_factor',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'ph' => 'decimal:2',
            'suhu' => 'decimal:2',
            'ppm' => 'decimal:2',
            'water_level' => 'decimal:2',
            'voltage' => 'decimal:2',
            'current' => 'decimal:3',
            'power' => 'decimal:2',
            'energy' => 'decimal:3',
            'frequency' => 'decimal:2',
            'power_factor' => 'decimal:2',
            'recorded_at' => 'datetime',
        ];
    }

    /**
     * Perangkat yang mengirim data ini.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}