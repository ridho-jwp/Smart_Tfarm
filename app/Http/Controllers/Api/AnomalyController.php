<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Anomaly;
use App\Models\Device;
use App\Jobs\ProcessAnomalyImage;
use Illuminate\Http\Request;

class AnomalyController extends Controller
{
    /**
     * Terima laporan anomali dari ESP32 (misal: gambar tanaman sakit).
     *
     * POST /api/v1/anomaly
     * Body: { device_id, type, description?, image? (file), severity? }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'type' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'image' => 'nullable|image|max:5120', // Max 5MB
            'severity' => 'nullable|in:low,medium,high',
        ]);

        $device = Device::where('device_id', $validated['device_id'])->first();

        if (!$device) {
            return response()->json([
                'success' => false,
                'message' => 'Perangkat tidak ditemukan.',
            ], 404);
        }

        // Simpan gambar jika ada
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('anomalies', 'public');
        }

        // Buat record anomali
        $anomaly = Anomaly::create([
            'device_id' => $device->id,
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'image_path' => $imagePath,
            'severity' => $validated['severity'] ?? 'medium',
        ]);

        // Dispatch job untuk proses gambar AI (target < 7 detik)
        if ($imagePath) {
            ProcessAnomalyImage::dispatch($anomaly);
        }

        return response()->json([
            'success' => true,
            'message' => 'Anomali berhasil dilaporkan.',
            'data' => $anomaly,
        ], 201);
    }
}
