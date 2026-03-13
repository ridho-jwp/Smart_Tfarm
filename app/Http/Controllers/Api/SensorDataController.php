<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SensorData;
use App\Models\Anomaly;
use App\Models\PlantConfig;
use Illuminate\Http\Request;

class SensorDataController extends Controller
{
    /**
     * Terima data sensor dari ESP32.
     *
     * POST /api/v1/sensor-data
     * Headers: X-API-Key: <token>
     * Body: { device_id, ph?, suhu?, ppm?, water_level?, ... }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'ph' => 'nullable|numeric|between:0,14',
            'suhu' => 'nullable|numeric|between:-10,60',
            'ppm' => 'nullable|numeric|between:0,5000',
            'water_level' => 'nullable|numeric|min:0|max:500', // ketinggian air dalam cm
            'voltage' => 'nullable|numeric|between:0,300',
            'current' => 'nullable|numeric|between:0,100',
            'power' => 'nullable|numeric|between:0,25000',
            'energy' => 'nullable|numeric|min:0',
            'frequency' => 'nullable|numeric|between:45,65',
            'power_factor' => 'nullable|numeric|between:0,1',
            'recorded_at' => 'nullable|date',
        ]);

        // ── Cari atau buat perangkat sensor ──
        $device = Device::firstOrCreate(
            ['device_id' => $validated['device_id']],
            [
                'name' => 'Sensor ' . $validated['device_id'],
                'type' => 'sensor',
                'is_online' => true,
                'last_heartbeat' => now(),
            ]
        );

        $device->update([
            'is_online' => true,
            'last_heartbeat' => now(),
        ]);

        // ── Simpan data sensor ──
        $sensorData = SensorData::create([
            'device_id' => $device->id,
            'ph' => $validated['ph'] ?? null,
            'suhu' => $validated['suhu'] ?? null,
            'ppm' => $validated['ppm'] ?? null,
            'water_level' => $validated['water_level'] ?? null,
            'voltage' => $validated['voltage'] ?? null,
            'current' => $validated['current'] ?? null,
            'power' => $validated['power'] ?? null,
            'energy' => $validated['energy'] ?? null,
            'frequency' => $validated['frequency'] ?? null,
            'power_factor' => $validated['power_factor'] ?? null,
            'recorded_at' => $validated['recorded_at'] ?? now(),
        ]);

        // ── Ambil konfigurasi batas ──
        $configs = PlantConfig::all()->keyBy('parameter');

        // ─────────────────────────────────────────────────
        // LOGIKA 1: Cek Suhu → Buat Anomali jika melebihi batas
        // ─────────────────────────────────────────────────
        if (isset($validated['suhu']) && isset($configs['suhu'])) {
            $suhu = (float) $validated['suhu'];
            $suhuMin = (float) $configs['suhu']->min_optimal;
            $suhuMax = (float) $configs['suhu']->max_optimal;

            if ($suhu > $suhuMax) {
                Anomaly::create([
                    'device_id' => $device->id,
                    'type' => 'suhu_tinggi',
                    'description' => "Suhu air {$suhu}°C melebihi batas maksimum {$suhuMax}°C",
                    'value' => $suhu,
                    'threshold' => $suhuMax,
                ]);
            } elseif ($suhu < $suhuMin) {
                Anomaly::create([
                    'device_id' => $device->id,
                    'type' => 'suhu_rendah',
                    'description' => "Suhu air {$suhu}°C di bawah batas minimum {$suhuMin}°C",
                    'value' => $suhu,
                    'threshold' => $suhuMin,
                ]);
            }
        }

        // ─────────────────────────────────────────────────
        // LOGIKA 2: Cek Ketinggian Air → Auto-kontrol pompa tandon (berdasarkan JARAK)
        // ─────────────────────────────────────────────────
        if (isset($validated['water_level']) && isset($configs['ketinggian_air'])) {
            // Pada ESP32 terbaru, water_level yang dikirim adalah "jarak" (cm)
            $jarak = (float) $validated['water_level'];
            
            // Konfigurasi db: min_optimal = jarak pompa MATI (air penuh / jarak dekat)
            // Konfigurasi db: max_optimal = jarak pompa NYALA (air rendah / jarak jauh)
            $jarakMati = (float) $configs['ketinggian_air']->min_optimal;
            $jarakNyala = (float) $configs['ketinggian_air']->max_optimal;

            // Cari device pompa tandon (type = actuator, mengandung kata 'tandon' atau 'pompa')
            $pumpDevice = Device::where('type', 'actuator')
                ->where(function ($q) {
                    $q->where('name', 'like', '%tandon%')
                        ->orWhere('name', 'like', '%pompa%')
                        ->orWhere('device_id', 'like', '%PUMP%');
                })
                ->first();

            if ($pumpDevice) {
                // Cek status pompa saat ini dari log terakhir
                $lastLog = DeviceLog::where('device_id', $pumpDevice->id)
                    ->whereIn('action', ['pump_on', 'pump_off'])
                    ->orderBy('created_at', 'desc')
                    ->first();

                $pumpCurrentlyOn = $lastLog && $lastLog->action === 'pump_on';

                if ($jarak >= $jarakNyala && !$pumpCurrentlyOn) {
                    // Air terlalu rendah (jarak besar) → nyalakan pompa
                    DeviceLog::create([
                        'device_id' => $pumpDevice->id,
                        'action' => 'pump_on',
                        'payload' => json_encode([
                            'reason' => 'auto_water_level',
                            'jarak_air' => $jarak,
                            'jarak_nyala' => $jarakNyala,
                            'timestamp' => now()->toISOString(),
                        ]),
                    ]);

                    // Buat juga anomali ketinggian air
                    Anomaly::create([
                        'device_id' => $device->id,
                        'type' => 'ketinggian_rendah',
                        'description' => "Air rendah (jarak {$jarak}cm >= batas {$jarakNyala}cm). Pompa dinyalakan otomatis.",
                        'value' => $jarak,
                        'threshold' => $jarakNyala,
                    ]);

                } elseif ($jarak <= $jarakMati && $pumpCurrentlyOn) {
                    // Air sudah penuh (jarak kecil) → matikan pompa
                    DeviceLog::create([
                        'device_id' => $pumpDevice->id,
                        'action' => 'pump_off',
                        'payload' => json_encode([
                            'reason' => 'auto_water_full',
                            'jarak_air' => $jarak,
                            'jarak_mati' => $jarakMati,
                            'timestamp' => now()->toISOString(),
                        ]),
                    ]);
                }
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data sensor berhasil disimpan.',
            'data' => $sensorData,
        ], 201);
    }
}
