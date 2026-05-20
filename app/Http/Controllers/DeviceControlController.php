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
            'action' => 'required|in:circulation_on,circulation_off,peristaltic_on,peristaltic_off,pump_on,pump_off',
        ]);

        $device = Device::findOrFail($request->device_id);

        // Tentukan status ON/OFF
        $isOn = in_array($request->action, [
            'circulation_on',
            'peristaltic_on',
            'pump_on'
        ]);

        // Simpan status terbaru ke metadata device
        $metadata = $device->metadata ?? [];
        $metadata['last_status'] = $isOn ? 'on' : 'off';

        $device->update([
            'metadata' => $metadata
        ]);

        // Simpan log command
        $log = DeviceLog::create([
            'device_id' => $device->id,
            'action' => $request->action,
            'payload' => [                        // ← array langsung, bukan json_encode()
                'command' => $request->action,
                'status' => $metadata['last_status'],
                'timestamp' => now()->toISOString(),
            ],
            'performed_by' => auth()->id(),
        ]);

        $actionText = $isOn ? 'dinyalakan' : 'dimatikan';

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "{$device->name} berhasil {$actionText}.",
                'status' => $metadata['last_status'],
                'action' => $request->action,
                'time' => $log->created_at->format('d/m H:i'),
                'user' => auth()->user()->name ?? 'Sistem'
            ]);
        }

        return redirect()->back()->with(
            'success',
            "{$device->name} berhasil {$actionText}."
        );
    }
}