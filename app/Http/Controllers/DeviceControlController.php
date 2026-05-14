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
    /**
     * Toggle perangkat On/Off dari website.
     * Aksi yang didukung: circulation_on, circulation_off (mini waterpump)
     */
    public function toggle(Request $request)
    {
        $request->validate([
            'device_id' => 'required|exists:devices,id',
            'action' => 'required|in:circulation_on,circulation_off,pump_on,pump_off',
        ]);

        $device = Device::findOrFail($request->device_id);

        $log = DeviceLog::create([
            'device_id' => $device->id,
            'action' => $request->action,
            'payload' => json_encode([
                'command' => $request->action,
                'timestamp' => now()->toISOString(),
            ]),
            'performed_by' => auth()->id(),
        ]);

        $actionLabels = [
            'circulation_on' => 'dinyalakan',
            'circulation_off' => 'dimatikan',
            'pump_on' => 'dinyalakan',
            'pump_off' => 'dimatikan',
        ];

        $actionText = $actionLabels[$request->action] ?? 'diperbarui';

        // Perbaikan: Cek jika request adalah AJAX
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$device->name} berhasil {$actionText}.",
                'action' => $request->action,
                'time' => $log->created_at->format('d/m H:i'),
                'user' => auth()->user()->name ?? 'Sistem'
            ]);
        }

        return redirect()->back()->with('success', "{$device->name} berhasil {$actionText}.");
    }
}