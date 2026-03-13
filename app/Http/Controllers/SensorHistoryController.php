<?php

namespace App\Http\Controllers;

use App\Models\SensorData;
use App\Models\Anomaly;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SensorHistoryController extends Controller
{
    /**
     * Tampilkan rangkuman data sensor per 5 menit + status anomali.
     */
    public function index(Request $request)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        // Ambil data sensor hari yang dipilih
        $sensorData = SensorData::with('device')
            ->whereDate('recorded_at', $date)
            ->orderBy('recorded_at', 'desc')
            ->get();

        // Ambil anomali hari tersebut
        $anomalies = Anomaly::whereDate('created_at', $date)->get();

        // Kelompokkan per 5 menit
        $grouped = $sensorData->groupBy(function ($item) {
            $minutes = $item->recorded_at->minute;
            $rounded = floor($minutes / 5) * 5;
            return $item->recorded_at->format('Y-m-d H:') . str_pad($rounded, 2, '0', STR_PAD_LEFT);
        })->map(function ($group, $key) use ($anomalies) {
            $startTime = Carbon::parse($key);
            $endTime = $startTime->copy()->addMinutes(5);

            // Cek apakah ada anomali dalam rentang 5 menit ini
            $hasAnomaly = $anomalies->filter(function ($anomaly) use ($startTime, $endTime) {
                    return $anomaly->created_at >= $startTime
                    && $anomaly->created_at < $endTime
                    && $anomaly->type !== 'normal';
                }
                )->count() > 0;

                return [
                'waktu' => Carbon::parse($key)->format('H:i'),
                'tanggal' => Carbon::parse($key)->format('d/m/Y'),
                'ph_avg' => round($group->avg('ph'), 2),
                'ph_min' => round($group->min('ph'), 2),
                'ph_max' => round($group->max('ph'), 2),
                'suhu_avg' => round($group->avg('suhu'), 2),
                'suhu_min' => round($group->min('suhu'), 2),
                'suhu_max' => round($group->max('suhu'), 2),
                'ppm_avg' => round($group->avg('ppm'), 2),
                'ppm_min' => round($group->min('ppm'), 2),
                'ppm_max' => round($group->max('ppm'), 2),
                'voltage_avg' => round($group->avg('voltage'), 2),
                'voltage_min' => round($group->min('voltage'), 2),
                'voltage_max' => round($group->max('voltage'), 2),
                'power_avg' => round($group->avg('power'), 2),
                'power_min' => round($group->min('power'), 2),
                'power_max' => round($group->max('power'), 2),
                'energy_avg' => round($group->avg('energy'), 3),
                'jumlah_data' => $group->count(),
                'has_anomaly' => $hasAnomaly,
                ];
            })->values();

        return view('history', compact('grouped', 'date'));
    }
}
