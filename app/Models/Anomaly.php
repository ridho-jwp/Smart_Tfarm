<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Anomaly extends Model
{
    protected $table = 'anomalies';

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

    protected $casts = [
        'resolved_at' => 'datetime',
        'confidence' => 'decimal:2',
        'value' => 'decimal:2',
        'threshold' => 'decimal:2',
    ];

    /**
     * Perangkat yang memiliki anomali ini.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
