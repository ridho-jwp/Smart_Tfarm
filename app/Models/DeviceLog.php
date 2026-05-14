<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceLog extends Model
{
    protected $fillable = [
        'device_id',
        'action',
        'payload',
        'performed_by',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    /**
     * Perangkat terkait.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * User yang melakukan aksi (nullable — bisa dari sistem/ESP32).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'performed_by');
    }

    // Tambahkan di dalam class DeviceLog
    protected static function booted()
    {
        static::created(function ($log) {
            $device = $log->device;
            if ($device) {
                // Cek apakah action mengandung kata '_on' atau '_off'
                $isOn = str_contains($log->action, '_on');

                $metadata = $device->metadata ?? [];
                $metadata['last_status'] = $isOn ? 'on' : 'off';
                $metadata['last_action'] = $log->action;

                // JANGAN update 'is_online' di sini. 
                // 'is_online' biarkan diupdate oleh sistem heartbeat/ping dari ESP32.
                $device->update(['metadata' => $metadata]);
            }
        });
    }
}