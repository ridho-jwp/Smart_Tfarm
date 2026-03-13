<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Device;
use App\Models\SensorData;
use App\Models\Anomaly;
use App\Models\DeviceLog;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // ============================================
        // AKUN PENGGUNA
        // ============================================
        User::updateOrCreate(
        ['email' => 'admin@smartpakcoy.com'],
        [
            'name' => 'Administrator',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]
        );

        User::updateOrCreate(
        ['email' => 'user@smartpakcoy.com'],
        [
            'name' => 'Operator',
            'password' => bcrypt('password'),
            'role' => 'user',
            'email_verified_at' => now(),
        ]
        );

        // ============================================
        // PERANGKAT IOT
        // ============================================
        $sensorDevice = Device::updateOrCreate(
        ['device_id' => 'ESP32-SENSOR-001'],
        [
            'name' => 'Sensor Utama Hidroponik',
            'type' => 'sensor',
            'is_online' => true,
            'last_heartbeat' => now(),
        ]
        );

        $pumpDevice = Device::updateOrCreate(
        ['device_id' => 'ESP32-PUMP-001'],
        [
            'name' => 'Pompa Air Nutrisi',
            'type' => 'actuator',
            'is_online' => true,
            'last_heartbeat' => now(),
        ]
        );

        $sprayDevice = Device::updateOrCreate(
        ['device_id' => 'ESP32-SPRAY-001'],
        [
            'name' => 'Pompa Penyiraman Pembasmi Hama',
            'type' => 'actuator',
            'is_online' => true,
            'last_heartbeat' => now(),
        ]
        );

        $camDevice = Device::updateOrCreate(
        ['device_id' => 'ESP32-CAM-001'],
        [
            'name' => 'Kamera Tanaman',
            'type' => 'camera',
            'is_online' => true,
            'last_heartbeat' => now(),
        ]
        );

        // ============================================
        // DATA SENSOR DUMMY (24 jam, per 5 menit = 288 record)
        // ============================================
        $now = Carbon::now();
        $energyAccumulator = 0;
        for ($i = 288; $i >= 0; $i--) {
            $time = $now->copy()->subMinutes($i * 5);
            $voltage = round(218 + (mt_rand(-40, 40) / 10), 2); // 214-222V
            $current = round(1.5 + (mt_rand(-10, 15) / 10), 3); // 0.5-3.0A
            $pf = round(0.85 + (mt_rand(0, 15) / 100), 2); // 0.85-1.00
            $power = round($voltage * $current * $pf, 2); // Watt
            $energyAccumulator += round($power * (5 / 60) / 1000, 3); // kWh per 5 min
            $frequency = round(50 + (mt_rand(-5, 5) / 10), 2); // 49.5-50.5Hz

            SensorData::create([
                'device_id' => $sensorDevice->id,
                'ph' => round(5.5 + (mt_rand(-10, 10) / 10), 2),
                'suhu' => round(25 + (mt_rand(-30, 30) / 10), 2),
                'ppm' => round(800 + mt_rand(-200, 200), 2),
                'voltage' => $voltage,
                'current' => $current,
                'power' => $power,
                'energy' => round($energyAccumulator, 3),
                'frequency' => $frequency,
                'power_factor' => $pf,
                'recorded_at' => $time,
            ]);
        }

        // ============================================
        // ANOMALI DUMMY (gambar tangkapan ESP32 Cam)
        // ============================================
        $anomalyTypes = [
            [
                'type' => 'bercak_daun',
                'description' => 'Terdeteksi bercak coklat pada daun pakcoy bagian bawah. Kemungkinan serangan jamur atau kekurangan nutrisi kalsium.',
                'severity' => 'high',
                'confidence' => 0.92,
                'hours_ago' => 2,
            ],
            [
                'type' => 'daun_kuning',
                'description' => 'Daun menguning pada tanaman pakcoy baris ke-3. Indikasi pH terlalu rendah atau kekurangan nitrogen.',
                'severity' => 'medium',
                'confidence' => 0.85,
                'hours_ago' => 5,
            ],
            [
                'type' => 'akar_busuk',
                'description' => 'Akar tanaman tampak kecoklatan dan lembek. Kemungkinan root rot akibat suhu air terlalu tinggi.',
                'severity' => 'high',
                'confidence' => 0.78,
                'hours_ago' => 8,
            ],
            [
                'type' => 'normal',
                'description' => 'Tanaman pakcoy dalam kondisi sehat. Daun hijau segar, tidak ditemukan tanda kerusakan.',
                'severity' => 'low',
                'confidence' => 0.96,
                'hours_ago' => 1,
            ],
            [
                'type' => 'hama_kutu',
                'description' => 'Terdeteksi kutu daun (aphid) pada permukaan bawah daun pakcoy. Perlu penanganan segera.',
                'severity' => 'high',
                'confidence' => 0.88,
                'hours_ago' => 12,
            ],
            [
                'type' => 'normal',
                'description' => 'Kondisi tanaman baik. Pertumbuhan normal sesuai usia tanam 14 hari.',
                'severity' => 'low',
                'confidence' => 0.94,
                'hours_ago' => 3,
            ],
            [
                'type' => 'layu',
                'description' => 'Beberapa tanaman tampak layu di siang hari. Kemungkinan suhu lingkungan terlalu tinggi.',
                'severity' => 'medium',
                'confidence' => 0.81,
                'hours_ago' => 6,
            ],
            [
                'type' => 'normal',
                'description' => 'Monitoring rutin — semua tanaman dalam keadaan sehat dan tumbuh dengan baik.',
                'severity' => 'low',
                'confidence' => 0.97,
                'hours_ago' => 0.5,
            ],
        ];

        foreach ($anomalyTypes as $anomaly) {
            Anomaly::create([
                'device_id' => $camDevice->id,
                'type' => $anomaly['type'],
                'description' => $anomaly['description'],
                'severity' => $anomaly['severity'],
                'confidence' => $anomaly['confidence'],
                'image_path' => null, // akan pakai placeholder di view
                'resolved_at' => $anomaly['severity'] === 'low' ? now() : null,
                'created_at' => now()->subHours($anomaly['hours_ago']),
                'updated_at' => now()->subHours($anomaly['hours_ago']),
            ]);
        }

        // ============================================
        // LOG KONTROL DUMMY — POMPA NUTRISI
        // ============================================
        $admin = User::where('email', 'admin@smartpakcoy.com')->first();

        DeviceLog::create([
            'device_id' => $pumpDevice->id,
            'action' => 'pump_on',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(8),
        ]);
        DeviceLog::create([
            'device_id' => $pumpDevice->id,
            'action' => 'pump_off',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(7),
        ]);
        DeviceLog::create([
            'device_id' => $pumpDevice->id,
            'action' => 'pump_on',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(4),
        ]);
        DeviceLog::create([
            'device_id' => $pumpDevice->id,
            'action' => 'pump_off',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(3),
        ]);
        DeviceLog::create([
            'device_id' => $pumpDevice->id,
            'action' => 'pump_on',
            'performed_by' => null, // otomatis oleh sistem
            'created_at' => now()->subHours(1),
        ]);
        DeviceLog::create([
            'device_id' => $pumpDevice->id,
            'action' => 'pump_off',
            'performed_by' => null, // otomatis oleh sistem
            'created_at' => now()->subMinutes(30),
        ]);

        // ============================================
        // LOG KONTROL DUMMY — POMPA PEMBASMI HAMA
        // ============================================
        DeviceLog::create([
            'device_id' => $sprayDevice->id,
            'action' => 'spray_on',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(12),
        ]);
        DeviceLog::create([
            'device_id' => $sprayDevice->id,
            'action' => 'spray_off',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(11),
        ]);
        DeviceLog::create([
            'device_id' => $sprayDevice->id,
            'action' => 'spray_on',
            'performed_by' => null, // otomatis oleh sistem
            'created_at' => now()->subHours(6),
        ]);
        DeviceLog::create([
            'device_id' => $sprayDevice->id,
            'action' => 'spray_off',
            'performed_by' => null, // otomatis oleh sistem
            'created_at' => now()->subHours(5),
        ]);
        DeviceLog::create([
            'device_id' => $sprayDevice->id,
            'action' => 'spray_on',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(2),
        ]);
        DeviceLog::create([
            'device_id' => $sprayDevice->id,
            'action' => 'spray_off',
            'performed_by' => $admin->id,
            'created_at' => now()->subHours(1),
        ]);
    }
}
