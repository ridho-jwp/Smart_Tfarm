<?php

namespace App\Http\Controllers;

use App\Models\ConfigPreset;
use App\Models\PlantConfig;
use Illuminate\Http\Request;

class PlantConfigController extends Controller
{
    // ─── Tampilkan halaman konfigurasi ───────────────────────────────────────
    public function index()
    {
        $configs = PlantConfig::all()->keyBy('parameter');
        $presets = ConfigPreset::orderBy('is_default', 'desc')->orderBy('created_at')->get();
        return view('configs.index', compact('configs', 'presets'));
    }

    // ─── Update konfigurasi aktif (form manual) ───────────────────────────────
    public function update(Request $request)
    {
        $request->validate([
            'ph_min'             => 'required|numeric|min:0|max:14',
            'ph_max'             => 'required|numeric|min:0|max:14|gte:ph_min',
            'suhu_min'           => 'required|numeric',
            'suhu_max'           => 'required|numeric|gte:suhu_min',
            'ppm_min'            => 'required|numeric|min:0',
            'ppm_max'            => 'required|numeric|min:0|gte:ppm_min',
            'ketinggian_air_min' => 'required|numeric|min:0',
            'ketinggian_air_max' => 'required|numeric|min:0|gte:ketinggian_air_min',
        ]);

        $this->applyValues(
            $request->ph_min,            $request->ph_max,
            $request->suhu_min,          $request->suhu_max,
            $request->ppm_min,           $request->ppm_max,
            $request->ketinggian_air_min, $request->ketinggian_air_max
        );

        return redirect()->route('configs.index')
            ->with('success', 'Konfigurasi berhasil diperbarui.');
    }

    // ─── Terapkan preset ke konfigurasi aktif ─────────────────────────────────
    public function applyPreset(Request $request)
    {
        $request->validate(['preset_id' => 'required|exists:config_presets,id']);

        $preset = ConfigPreset::findOrFail($request->preset_id);

        // Hanya update pH, suhu, PPM — ketinggian air TIDAK ikut preset
        $updates = [
            'ph'   => ['min_optimal' => $preset->ph_min,   'max_optimal' => $preset->ph_max],
            'suhu' => ['min_optimal' => $preset->suhu_min, 'max_optimal' => $preset->suhu_max],
            'ppm'  => ['min_optimal' => $preset->ppm_min,  'max_optimal' => $preset->ppm_max],
        ];

        foreach ($updates as $param => $values) {
            PlantConfig::where('parameter', $param)->update($values);
        }

        return redirect()->route('configs.index')
            ->with('success', 'Preset "' . $preset->name . '" berhasil diterapkan.');
    }

    // ─── Simpan preset baru ───────────────────────────────────────────────────
    public function storePreset(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'ph_min'      => 'required|numeric|min:0|max:14',
            'ph_max'      => 'required|numeric|min:0|max:14|gte:ph_min',
            'suhu_min'    => 'required|numeric',
            'suhu_max'    => 'required|numeric|gte:suhu_min',
            'ppm_min'     => 'required|numeric|min:0',
            'ppm_max'     => 'required|numeric|min:0|gte:ppm_min',
        ]);

        ConfigPreset::create([
            'name'        => $request->name,
            'description' => $request->description,
            'ph_min'      => $request->ph_min,
            'ph_max'      => $request->ph_max,
            'suhu_min'    => $request->suhu_min,
            'suhu_max'    => $request->suhu_max,
            'ppm_min'     => $request->ppm_min,
            'ppm_max'     => $request->ppm_max,
            'is_default'  => false,
        ]);

        return redirect()->route('configs.index')
            ->with('success', 'Preset "' . $request->name . '" berhasil disimpan.')
            ->withFragment('tab-kelola');
    }

    // ─── Hapus preset (semua preset bisa dihapus) ─────────────────────────────
    public function destroyPreset(ConfigPreset $preset)
    {
        $name = $preset->name;
        $preset->delete();
        return redirect()->route('configs.index')
            ->with('success', 'Preset "' . $name . '" berhasil dihapus.')
            ->withFragment('tab-kelola');
    }

    // ─── Helper: tulis nilai ke tabel plant_configs ───────────────────────────
    private function applyValues(
        float $phMin,   float $phMax,
        float $suhuMin, float $suhuMax,
        float $ppmMin,  float $ppmMax,
        float $airMin,  float $airMax
    ): void {
        $updates = [
            'ph'             => ['min_optimal' => $phMin,   'max_optimal' => $phMax],
            'suhu'           => ['min_optimal' => $suhuMin, 'max_optimal' => $suhuMax],
            'ppm'            => ['min_optimal' => $ppmMin,  'max_optimal' => $ppmMax],
            'ketinggian_air' => ['min_optimal' => $airMin,  'max_optimal' => $airMax],
        ];

        foreach ($updates as $param => $values) {
            PlantConfig::where('parameter', $param)->update($values);
        }
    }
}
