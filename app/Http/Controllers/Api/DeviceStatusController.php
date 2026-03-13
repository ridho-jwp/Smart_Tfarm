<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use Illuminate\Http\Request;

class DeviceStatusController extends Controller
{
    public function heartbeat(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
        ]);

        $device = Device::firstOrCreate(
            ['device_id' => $validated['device_id']],
            [
                'name' => 'Device ' . $validated['device_id'],
                'type' => 'sensor',
            ]
        );

        $device->update([
            'is_online' => true,
            'last_heartbeat' => now(),
        ]);

        DeviceLog::create([
            'device_id' => $device->id,
            'action' => 'heartbeat',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Heartbeat diterima.',
            'server_time' => now()->toISOString(),
        ]);
    }

    public function getCommand(string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan.',
            ], 404);
        }

        $latestCommand = DeviceLog::where('device_id', $device->id)
            ->whereIn('action', ['pump_on', 'pump_off'])
            ->orderBy('created_at', 'desc')
            ->first();

        return response()->json([
            'success' => true,
            'command' => $latestCommand ? $latestCommand->action : null,
            'payload' => $latestCommand?->payload,
            'issued_at' => $latestCommand?->created_at?->toISOString(),
        ]);
    }
}
