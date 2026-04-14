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
     * Body: { device_id, ph?, suhu?, ppm?, water_level?, pump_circ_on?, pump_peri_on? }
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'device_id'     => 'required|string',
            'ph'            => 'nullable|numeric|between:0,14',
            'suhu'          => 'nullable|numeric|between:-10,60',
            'ppm'           => 'nullable|numeric|between:0,5000',
            'water_level'   => 'nullable|numeric|min:0|max:500',
            'voltage'       => 'nullable|numeric|between:0,300',
            'current'       => 'nullable|numeric|between:0,100',
            'power'         => 'nullable|numeric|between:0,25000',
            'energy'        => 'nullable|numeric|min:0',
            'frequency'     => 'nullable|numeric|between:45,65',
            'power_factor'  => 'nullable|numeric|between:0,1',
            'pump_circ_on'  => 'nullable|boolean',
            'pump_peri_on'  => 'nullable|boolean',
            'recorded_at'   => 'nullable|date',
        ]);

        // ── Cari atau buat perangkat sensor ──
        $device = Device::firstOrCreate(
            ['device_id' => $validated['device_id']],
            [
                'name'           => 'Sensor ' . $validated['device_id'],
                'type'           => 'sensor',
                'is_online'      => true,
                'last_heartbeat' => now(),
            ]
        );

        $device->update([
            'is_online'      => true,
            'last_heartbeat' => now(),
        ]);

        // ── Simpan data sensor ──
        $sensorData = SensorData::create([
            'device_id'    => $device->id,
            'ph'           => $validated['ph']          ?? null,
            'suhu'         => $validated['suhu']        ?? null,
            'ppm'          => $validated['ppm']         ?? null,
            'water_level'  => $validated['water_level'] ?? null,
            'voltage'      => $validated['voltage']     ?? null,
            'current'      => $validated['current']     ?? null,
            'power'        => $validated['power']       ?? null,
            'energy'       => $validated['energy']      ?? null,
            'frequency'    => $validated['frequency']   ?? null,
            'power_factor' => $validated['power_factor'] ?? null,
            'recorded_at'  => $validated['recorded_at'] ?? now(),
        ]);

        // ── Ambil semua konfigurasi batas ──
        $configs = PlantConfig::all()->keyBy('parameter');

        // ─────────────────────────────────────────────────
        // CEK ANOMALI: Suhu
        // ─────────────────────────────────────────────────
        if (isset($validated['suhu']) && isset($configs['suhu'])) {
            $suhu    = (float) $validated['suhu'];
            $suhuMin = (float) $configs['suhu']->min_optimal;
            $suhuMax = (float) $configs['suhu']->max_optimal;

            if ($suhu > $suhuMax) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'suhu_tinggi',
                    'description' => "Suhu air {$suhu}°C melebihi batas maksimum {$suhuMax}°C",
                    'value'       => $suhu,
                    'threshold'   => $suhuMax,
                ]);
            } elseif ($suhu < $suhuMin) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'suhu_rendah',
                    'description' => "Suhu air {$suhu}°C di bawah batas minimum {$suhuMin}°C",
                    'value'       => $suhu,
                    'threshold'   => $suhuMin,
                ]);
            }
        }

        // ─────────────────────────────────────────────────
        // CEK ANOMALI: pH
        // ─────────────────────────────────────────────────
        if (isset($validated['ph']) && isset($configs['ph'])) {
            $ph    = (float) $validated['ph'];
            $phMin = (float) $configs['ph']->min_optimal;
            $phMax = (float) $configs['ph']->max_optimal;

            if ($ph > $phMax) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'ph_tinggi',
                    'description' => "pH air {$ph} melebihi batas maksimum {$phMax}",
                    'value'       => $ph,
                    'threshold'   => $phMax,
                ]);
            } elseif ($ph < $phMin) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'ph_rendah',
                    'description' => "pH air {$ph} di bawah batas minimum {$phMin}",
                    'value'       => $ph,
                    'threshold'   => $phMin,
                ]);
            }
        }

        // ─────────────────────────────────────────────────
        // CEK ANOMALI: PPM/TDS Nutrisi
        // Jika rendah → catat perintah pompa peristaltik
        // ─────────────────────────────────────────────────
        if (isset($validated['ppm']) && isset($configs['ppm'])) {
            $ppm    = (float) $validated['ppm'];
            $ppmMin = (float) $configs['ppm']->min_optimal;
            $ppmMax = (float) $configs['ppm']->max_optimal;

            if ($ppm < $ppmMin) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'nutrisi_rendah',
                    'description' => "PPM nutrisi {$ppm} ppm di bawah batas minimum {$ppmMin} ppm. Pompa peristaltik dinyalakan otomatis.",
                    'value'       => $ppm,
                    'threshold'   => $ppmMin,
                ]);

                // Catat log perintah pompa peristaltik (akan dibaca ESP32 jika diperlukan)
                $periDevice = Device::where('device_id', 'ESP32-PUMP-PERISTALTIK')->first();
                if ($periDevice) {
                    DeviceLog::create([
                        'device_id' => $periDevice->id,
                        'action'    => 'peristaltic_on',
                        'payload'   => json_encode([
                            'reason'    => 'auto_low_ppm',
                            'ppm'       => $ppm,
                            'ppm_min'   => $ppmMin,
                            'timestamp' => now()->toISOString(),
                        ]),
                    ]);
                }
            } elseif ($ppm > $ppmMax) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'nutrisi_tinggi',
                    'description' => "PPM nutrisi {$ppm} ppm melebihi batas maksimum {$ppmMax} ppm",
                    'value'       => $ppm,
                    'threshold'   => $ppmMax,
                ]);
            }
        }

        // ─────────────────────────────────────────────────
        // CEK ANOMALI: Ketinggian Air Tandon
        // ─────────────────────────────────────────────────
        if (isset($validated['water_level']) && isset($configs['ketinggian_air'])) {
            $jarak      = (float) $validated['water_level'];
            $jarakMati  = (float) $configs['ketinggian_air']->min_optimal;  // jarak kecil = air penuh
            $jarakNyala = (float) $configs['ketinggian_air']->max_optimal;  // jarak besar = air rendah

            if ($jarak >= $jarakNyala) {
                Anomaly::create([
                    'device_id'   => $device->id,
                    'type'        => 'ketinggian_rendah',
                    'description' => "Air tandon rendah! Jarak sensor {$jarak}cm >= batas {$jarakNyala}cm. Segera isi air tandon.",
                    'value'       => $jarak,
                    'threshold'   => $jarakNyala,
                ]);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Data sensor berhasil disimpan.',
            'data'    => $sensorData,
        ], 201);
    }
}
