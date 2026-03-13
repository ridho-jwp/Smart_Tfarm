<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anomaly extends Model
{
    protected $fillable = [
        'device_id',
        'type',
        'description',
        'image_path',
        'severity',
        'confidence',
        'value',
        'threshold',
        'resolved_at',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
            'value' => 'decimal:2',
            'threshold' => 'decimal:2',
            'resolved_at' => 'datetime',
        ];
    }

    /**
     * Perangkat yang mendeteksi anomali.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Cek apakah anomali sudah diselesaikan.
     */
    public function isResolved(): bool
    {
        return $this->resolved_at !== null;
    }
}
