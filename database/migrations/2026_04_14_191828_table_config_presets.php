<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('config_presets', function (Blueprint $table) {
            $table->id();
            $table->string('name');                      // Nama preset, misal "Pakcoy Usia 1 Minggu"
            $table->string('description')->nullable();   // Deskripsi singkat
            $table->decimal('ph_min',    5, 2);
            $table->decimal('ph_max',    5, 2);
            $table->decimal('suhu_min',  5, 2);
            $table->decimal('suhu_max',  5, 2);
            $table->decimal('ppm_min',   7, 2);
            $table->decimal('ppm_max',   7, 2);
            $table->decimal('ketinggian_air_min', 5, 2);
            $table->decimal('ketinggian_air_max', 5, 2);
            $table->boolean('is_default')->default(false); // Preset bawaan sistem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('config_presets');
    }
};
