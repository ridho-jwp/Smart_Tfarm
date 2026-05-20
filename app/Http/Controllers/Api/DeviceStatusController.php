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

        // Deteksi otomatis tipe device: pompa = actuator, sensor = sensor
        $deviceId = strtolower($validated['device_id']);
        $isActuator = str_contains($deviceId, 'pump')
            || str_contains($deviceId, 'sirkulasi')
            || str_contains($deviceId, 'peristaltik')
            || str_contains($deviceId, 'relay');

        // Nama yang lebih ramah berdasarkan device_id
        $nameMap = [
            'esp32-pump-sirkulasi' => 'Mini Waterpump (Sirkulasi)',
            'esp32-pump-peristaltik' => 'Pompa Peristaltik (Nutrisi)',
        ];
        $friendlyName = $nameMap[$deviceId] ?? ('Device ' . $validated['device_id']);

        $device = Device::firstOrCreate(
            ['device_id' => $validated['device_id']],
            [
                'name' => $friendlyName,
                'type' => $isActuator ? 'actuator' : 'sensor',
            ]
        );

        $device->update([
            'last_status' => true,
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

    /**
     * ESP32 polling command terbaru untuk devicenya.
     * GET /api/v1/command/{deviceId}
     *
     * Mendukung aksi:
     *   - circulation_on  / circulation_off  (mini waterpump)
     *   - peristaltic_on  / peristaltic_off  (pompa peristaltik)
     *   - pump_on         / pump_off          (legacy)
     */
    public function getCommand(string $deviceId)
    {
        $device = Device::where('device_id', $deviceId)->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan.',
            ], 404);
        }

        $commandActions = [
            'circulation_on',
            'circulation_off',
            'peristaltic_on',
            'peristaltic_off',
            'pump_on',
            'pump_off',
        ];

        // Ambil command yang BELUM dieksekusi (executed_at null)
        $latestCommand = DeviceLog::where('device_id', $device->id)
            ->whereIn('action', $commandActions)
            ->whereNull('executed_at')          // ← hanya yang belum dieksekusi
            ->orderBy('created_at', 'desc')
            ->first();

        if ($latestCommand) {
            // Tandai sudah dikirim ke ESP32
            $latestCommand->update(['executed_at' => now()]);
        }

        return response()->json([
            'success' => true,
            'command' => $latestCommand?->action,
            'payload' => $latestCommand?->payload,
            'issued_at' => $latestCommand?->created_at?->toISOString(),
        ]);
    }
}