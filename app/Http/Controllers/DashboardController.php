<?php

namespace App\Http\Controllers;

use App\Models\Device;
use App\Models\DeviceLog;
use App\Models\SensorData;
use App\Models\Anomaly;
use App\Models\PlantConfig;
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

        $unresolvedAnomalies = Anomaly::whereNull('resolved_at')->count();

        $chartData = SensorData::where('recorded_at', '>=', Carbon::now()->subHours(24))
            ->orderBy('recorded_at', 'asc')
            ->get()
            ->groupBy(function ($item) {
                return $item->recorded_at->format('H:i');
            })
            ->map(function ($group) {
                return [
                    'ph' => round($group->avg('ph'), 2),
                    'suhu' => round($group->avg('suhu'), 2),
                    'ppm' => round($group->avg('ppm'), 2),
                ];
            });

        $pumpDevices = Device::where('type', 'actuator')->get();

        $nutrisiPump = $pumpDevices->first(function ($d) {
            return str_contains(strtolower($d->name), 'nutrisi');
        });
        $hamaPump = $pumpDevices->first(function ($d) {
            return str_contains(strtolower($d->name), 'hama') || str_contains(strtolower($d->name), 'pembasmi');
        });

        $nutrisiLogs = $nutrisiPump
            ? DeviceLog::with('user')
                ->where('device_id', $nutrisiPump->id)
                ->whereIn('action', ['pump_on', 'pump_off'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
            : collect([]);

        $hamaLogs = $hamaPump
            ? DeviceLog::with('user')
                ->where('device_id', $hamaPump->id)
                ->whereIn('action', ['spray_on', 'spray_off', 'pump_on', 'pump_off'])
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get()
            : collect([]);

        return view('dashboard', compact(
            'latestSensor',
            'configs',
            'devices',
            'unresolvedAnomalies',
            'chartData',
            'pumpDevices',
            'nutrisiPump',
            'hamaPump',
            'nutrisiLogs',
            'hamaLogs'
        ));
    }

    public function latestSensorData()
    {
        $latest = SensorData::with('device')
            ->orderBy('recorded_at', 'desc')
            ->first();

        $devices = Device::all()->map(function ($device) {
            return [
                'id' => $device->id,
                'name' => $device->name,
                'is_online' => $device->is_online,
                'last_heartbeat' => $device->last_heartbeat?->diffForHumans(),
            ];
        });

        $recentChart = SensorData::where('recorded_at', '>=', Carbon::now()->subHour())
            ->orderBy('recorded_at', 'asc')
            ->get(['ph', 'suhu', 'ppm', 'recorded_at']);

        $configs = PlantConfig::all()->keyBy('parameter');

        return response()->json([
            'sensor' => $latest,
            'configs' => $configs,
            'devices' => $devices,
            'chart' => $recentChart,
        ]);
    }
}
