<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceLog;
use Illuminate\Http\Request;

class DeviceControlController extends Controller
{
    /**
     * Halaman kontrol perangkat.
     */
    public function index()
    {
        $devices = Device::where('type', 'actuator')->get();

        $recentLogs = DeviceLog::with(['device', 'user'])
            ->whereIn('action', [
                'circulation_on', 'circulation_off',
                'peristaltic_on', 'peristaltic_off',
                'pump_on', 'pump_off',
            ])
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        return view('control', compact('devices', 'recentLogs'));
    }

    /**
     * Toggle perangkat On/Off dari website.
     * Aksi yang didukung: circulation_on, circulation_off (mini waterpump)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'action'    => 'required|in:circulation_on,circulation_off,pump_on,pump_off',
        ]);

        $device = Device::findOrFail($request->device_id);

        DeviceLog::create([
            'device_id'    => $device->id,
            'action'       => $request->action,
            'payload'      => json_encode([
                'command'   => $request->action,
                'timestamp' => now()->toISOString(),
            ]),
            'performed_by' => auth()->id(),
        ]);

        $actionLabels = [
            'circulation_on'  => 'dinyalakan',
            'circulation_off' => 'dimatikan',
            'pump_on'         => 'dinyalakan',
            'pump_off'        => 'dimatikan',
        ];

        $actionText = $actionLabels[$request->action] ?? 'diperbarui';

        return redirect()->back()->with('success', "{$device->name} berhasil {$actionText}.");
    }
}
