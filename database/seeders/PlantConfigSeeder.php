<?php

namespace Database\Seeders;

use App\Models\PlantConfig;
use Illuminate\Database\Seeder;

class PlantConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $configs = [
            [
                'parameter' => 'ph',
                'label' => 'pH Air',
                'min_optimal' => 5.5,
                'max_optimal' => 6.5,
                'unit' => null,
            ],
            [
                'parameter' => 'suhu',
                'label' => 'Suhu Air',
                'min_optimal' => 20.0,
                'max_optimal' => 30.0,
                'unit' => '°C',
            ],
            [
                'parameter' => 'ppm',
                'label' => 'PPM Nutrisi',
                'min_optimal' => 500.0,
                'max_optimal' => 1200.0,
                'unit' => 'ppm',
            ],
            [
                'parameter' => 'ketinggian_air',
                'label' => 'Ketinggian Air Tandon',
                'min_optimal' => 10.0,  // pompa nyala jika air < 10 cm
                'max_optimal' => 30.0,  // pompa mati jika air >= 30 cm
                'unit' => 'cm',
            ],
        ];

        foreach ($configs as $config) {
            PlantConfig::updateOrCreate(
                ['parameter' => $config['parameter']],
                $config
            );
        }
    }
}
