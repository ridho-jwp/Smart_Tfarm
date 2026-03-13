<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Device extends Model
{
    protected $fillable = [
        'device_id',
        'name',
        'type',
        'is_online',
        'last_heartbeat',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'is_online' => 'boolean',
            'last_heartbeat' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Data sensor dari perangkat ini.
     */
    public function sensorData(): HasMany
    {
        return $this->hasMany(SensorData::class);
    }

    /**
     * Anomali yang terdeteksi oleh perangkat ini.
     */
    public function anomalies(): HasMany
    {
        return $this->hasMany(Anomaly::class);
    }

    /**
     * Log aktivitas perangkat ini.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DeviceLog::class);
    }

    /**
     * Data sensor terbaru.
     */
    public function latestSensorData()
    {
        return $this->hasOne(SensorData::class)->latestOfMany();
    }
}
