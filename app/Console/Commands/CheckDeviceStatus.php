<?php

namespace App\Console\Commands;

use App\Models\Device;
use Carbon\Carbon;
use Illuminate\Console\Command;

class CheckDeviceStatus extends Command
{
    protected $signature = 'devices:check-status';

    protected $description = 'Cek status online/offline perangkat berdasarkan heartbeat terakhir';

    public function handle(): void
    {
        // Tandai perangkat sebagai offline jika tidak ada heartbeat > 3 menit
        $threshold = Carbon::now()->subMinutes(3);

        $offlineDevices = Device::where('is_online', true)
            ->where(function ($query) use ($threshold) {
            $query->where('last_heartbeat', '<', $threshold)
                ->orWhereNull('last_heartbeat');
        })
            ->get();

        foreach ($offlineDevices as $device) {
            $device->update(['is_online' => false]);
            $this->warn("Perangkat '{$device->name}' ({$device->device_id}) ditandai OFFLINE.");
        }

        $onlineCount = Device::where('is_online', true)->count();
        $offlineCount = $offlineDevices->count();

        $this->info("Selesai. Online: {$onlineCount}, Baru offline: {$offlineCount}");
    }
}
