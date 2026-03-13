<?php

namespace App\Http\Controllers;

use App\Models\PlantConfig;
use Illuminate\Http\Request;

class PlantConfigController extends Controller
{
    public function index()
    {
        // Kolom kunci di tabel adalah 'parameter' (bukan 'sensor_type')
        $configs = PlantConfig::all()->keyBy('parameter');
        return view('configs.index', compact('configs'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'ph_min' => 'required|numeric|min:0|max:14',
            'ph_max' => 'required|numeric|min:0|max:14|gte:ph_min',
            'suhu_min' => 'required|numeric',
            'suhu_max' => 'required|numeric|gte:suhu_min',
            'ppm_min' => 'required|numeric|min:0',
            'ppm_max' => 'required|numeric|min:0|gte:ppm_min',
            'ketinggian_air_min' => 'required|numeric|min:0',
            'ketinggian_air_max' => 'required|numeric|min:0|gte:ketinggian_air_min',
        ]);

        $updates = [
            'ph' => ['min_optimal' => $request->ph_min, 'max_optimal' => $request->ph_max],
            'suhu' => ['min_optimal' => $request->suhu_min, 'max_optimal' => $request->suhu_max],
            'ppm' => ['min_optimal' => $request->ppm_min, 'max_optimal' => $request->ppm_max],
            'ketinggian_air' => ['min_optimal' => $request->ketinggian_air_min, 'max_optimal' => $request->ketinggian_air_max],
        ];

        foreach ($updates as $param => $values) {
            PlantConfig::where('parameter', $param)->update($values);
        }

        return redirect()->route('configs.index')->with('success', 'Konfigurasi berhasil diperbarui.');
    }
}
