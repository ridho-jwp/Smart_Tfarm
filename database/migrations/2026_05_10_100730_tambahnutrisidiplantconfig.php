<?php
// database/migrations/xxxx_add_nutrisi_calibration_to_plant_configs.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Blueprint;
use Illuminate\Database\Schema\Blueprint as SchemaBlueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plant_configs', function (SchemaBlueprint $table) {
            // Volume tandon dalam liter — dipakai untuk menghitung total kebutuhan AB Mix
            $table->decimal('volume_tandon_liter', 8, 2)
                ->default(100)
                ->after('unit')
                ->comment('Volume air tandon dalam liter');

            // Berapa ppm naik per 1 mL AB Mix per 1 liter air
            // Contoh: 1 mL AB Mix pekat di 1 L air → naik ~7 ppm  → isi 0.7
            // Sesuaikan lewat kalibrasi fisik
            $table->decimal('ppm_per_ml_per_liter', 8, 4)
                ->default(0.7000)
                ->after('volume_tandon_liter')
                ->comment('Kenaikan PPM per mL AB Mix per liter air');

            // Kecepatan pompa peristaltik dalam mL/detik
            // Sesuaikan dengan pompa fisik yang dipakai
            $table->decimal('ml_per_detik', 8, 4)
                ->default(1.0000)
                ->after('ppm_per_ml_per_liter')
                ->comment('Kecepatan pompa peristaltik dalam mL/detik');
        });
    }

    public function down(): void
    {
        Schema::table('plant_configs', function (SchemaBlueprint $table) {
            $table->dropColumn([
                'volume_tandon_liter',
                'ppm_per_ml_per_liter',
                'ml_per_detik',
            ]);
        });
    }
};