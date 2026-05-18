<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SensorData;
use App\Models\Anomaly;
use App\Models\PlantConfig;
use App\Models\SprayManualState;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $latestSensor = SensorData::with('device')
            ->orderBy('recorded_at', 'desc')
            ->first();

        $configs = PlantConfig::all()->keyBy('parameter');

        $devices = Device::all();

        // $unresolvedAnomalies = Anomaly::whereNull('resolved_at')->count();

        // Data chart 24 jam: ph, suhu, ppm, water_level
        $chartData = SensorData::where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return $item->recorded_at->format('H:i');
            })
            ->map(function ($group) {
                return [
                    'ph'          => round($group->avg('ph'), 2),
                    'suhu'        => round($group->avg('suhu'), 2),
                    'ppm'         => round($group->avg('ppm'), 2),
                    'water_level' => round($group->avg('water_level'), 2),
                    'voltage'     => round($group->avg('voltage'), 2),
                    'power'       => round($group->avg('power'), 2),
                ];
            });

        $pumpDevices = Device::where('type', 'actuator')->get();

        // Pompa Sirkulasi (mini waterpump) — dikontrol dari website
        $circPump = $pumpDevices->first(function ($d) {
            return str_contains(strtolower($d->device_id), 'sirkulasi')
                || str_contains(strtolower($d->name), 'sirkulasi')
                || str_contains(strtolower($d->name), 'circulation');
        });

        // Pompa Peristaltik (nutrisi otomatis)
        $periPump = $pumpDevices->first(function ($d) {
            return str_contains(strtolower($d->device_id), 'peristaltik')
                || str_contains(strtolower($d->name), 'peristaltik');
        });

        // Log riwayat sirkulasi
        $circLogs = $circPump
            ? DeviceLog::with('user')
                ->where('device_id', $circPump->id)
                ->whereIn('action', ['circulation_on', 'circulation_off'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
            : collect([]);

        // Log riwayat peristaltik
        $periLogs = $periPump
            ? DeviceLog::with('user')
                ->where('device_id', $periPump->id)
                ->whereIn('action', ['peristaltic_on', 'peristaltic_off'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
            : collect([]);

        // Status sirkulasi (apakah pompa sedang ON atau OFF berdasarkan log terakhir)
        $circLastLog = $circPump
            ? DeviceLog::where('device_id', $circPump->id)
                ->whereIn('action', ['circulation_on', 'circulation_off'])
                ->orderBy('created_at', 'desc')
                ->first()
            : null;
        $circIsOn = $circLastLog && $circLastLog->action === 'circulation_on';

        // Status peristaltik
        $periLastLog = $periPump
            ? DeviceLog::where('device_id', $periPump->id)
                ->whereIn('action', ['peristaltic_on', 'peristaltic_off'])
                ->orderBy('created_at', 'desc')
                ->first()
            : null;
        $periIsOn = $periLastLog && $periLastLog->action === 'peristaltic_on';

        // State penyemprotan manual/otomatis
        $sprayState = SprayManualState::getState();

        return view('dashboard', compact(
            'latestSensor',
            'configs',
            'devices',
            // 'unresolvedAnomalies',
            'chartData',
            'pumpDevices',
            'circPump',
            'periPump',
            'circLogs',
            'periLogs',
            'circIsOn',
            'periIsOn',
            'sprayState'
        ));
    }

    public function latestSensorData()
    {
        $latest = SensorData::with('device')
            ->orderBy('recorded_at', 'desc')
            ->first();

        $devices = Device::all()->map(function ($device) {
            return [
                'id'             => $device->id,
                'name'           => $device->name,
                'is_online'      => $device->is_online,
                'last_heartbeat' => $device->last_heartbeat?->diffForHumans(),
            ];
        });

        $configs = PlantConfig::all()->keyBy('parameter');

        return response()->json([
            'sensor'  => $latest,
            'configs' => $configs,
            'devices' => $devices,
        ]);
    }
}
