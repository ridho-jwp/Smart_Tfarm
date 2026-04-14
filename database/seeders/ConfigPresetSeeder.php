<?php

namespace Database\Seeders;

use App\Models\ConfigPreset;
use Illuminate\Database\Seeder;

class ConfigPresetSeeder extends Seeder
{
    public function run(): void
    {
        $presets = [
            [
                'name'               => 'Pakcoy Usia 1 Minggu',
                'description'        => 'Fase awal pertumbuhan (7 hari setelah semai). Nutrisi rendah, pH stabil.',
                'ph_min'             => 6.0,
                'ph_max'             => 6.5,
                'suhu_min'           => 22.0,
                'suhu_max'           => 27.0,
                'ppm_min'            => 400,
                'ppm_max'            => 600,
                'ketinggian_air_min' => 10.0,
                'ketinggian_air_max' => 20.0,
                'is_default'         => true,
            ],
            [
                'name'               => 'Pakcoy Usia 2 Minggu',
                'description'        => 'Fase vegetatif awal (14 hari). Nutrisi mulai ditingkatkan.',
                'ph_min'             => 5.8,
                'ph_max'             => 6.3,
                'suhu_min'           => 22.0,
                'suhu_max'           => 28.0,
                'ppm_min'            => 600,
                'ppm_max'            => 900,
                'ketinggian_air_min' => 10.0,
                'ketinggian_air_max' => 20.0,
                'is_default'         => true,
            ],
            [
                'name'               => 'Pakcoy Usia 3 Minggu',
                'description'        => 'Fase vegetatif penuh (21 hari). Nutrisi optimal, siapkan panen.',
                'ph_min'             => 5.5,
                'ph_max'             => 6.2,
                'suhu_min'           => 20.0,
                'suhu_max'           => 28.0,
                'ppm_min'            => 900,
                'ppm_max'            => 1200,
                'ketinggian_air_min' => 10.0,
                'ketinggian_air_max' => 20.0,
                'is_default'         => true,
            ],
            [
                'name'               => 'Pakcoy Siap Panen (4 Minggu)',
                'description'        => 'Fase generatif/panen (28 hari+). Nutrisi tinggi, pantau ketat.',
                'ph_min'             => 5.5,
                'ph_max'             => 6.0,
                'suhu_min'           => 18.0,
                'suhu_max'           => 26.0,
                'ppm_min'            => 1200,
                'ppm_max'            => 1600,
                'ketinggian_air_min' => 10.0,
                'ketinggian_air_max' => 20.0,
                'is_default'         => true,
            ],
        ];

        foreach ($presets as $preset) {
            ConfigPreset::create($preset);
        }
    }
}
