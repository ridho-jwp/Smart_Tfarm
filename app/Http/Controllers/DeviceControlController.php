<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceLog;
use Illuminate\Http\Request;

class DeviceControlController extends Controller
{
    /**
     * Halaman kontrol perangkat (admin only).
     */
    public function index()
    {
        $devices = Device::where('type', 'actuator')->get();

        // Log kontrol terbaru
        $recentLogs = DeviceLog::with(['device', 'user'])
            ->whereIn('action', ['pump_on', 'pump_off', 'spray_on', 'spray_off'])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('control', compact('devices', 'recentLogs'));
    }

    /**
     * Toggle pompa On/Off.
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'action' => 'required|in:pump_on,pump_off,spray_on,spray_off',
        ]);

        $device = Device::findOrFail($request->device_id);

        // Simpan command ke device_logs
        DeviceLog::create([
            'device_id' => $device->id,
            'action' => $request->action,
            'payload' => [
                'command' => $request->action,
                'timestamp' => now()->toISOString(),
            ],
            'performed_by' => auth()->id(),
        ]);

        $actionLabels = [
            'pump_on' => 'dinyalakan',
            'pump_off' => 'dimatikan',
            'spray_on' => 'dinyalakan',
            'spray_off' => 'dimatikan',
        ];

        $actionText = $actionLabels[$request->action] ?? 'diperbarui';

        return redirect()->back()->with('success', "{$device->name} berhasil {$actionText}.");
    }
}
