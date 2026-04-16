<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('config_presets', function (Blueprint $table) {
            // Hapus kolom ketinggian air dari preset — dikelola terpisah
            $table->dropColumn(['ketinggian_air_min', 'ketinggian_air_max']);

            // Perlebar kolom PPM agar bisa menyimpan nilai besar (misal 10000 ppm)
            $table->decimal('ppm_min', 8, 2)->change();
            $table->decimal('ppm_max', 8, 2)->change();
        });
    }

    public function down(): void
    {
        Schema::table('config_presets', function (Blueprint $table) {
            $table->decimal('ketinggian_air_min', 5, 2)->default(0);
            $table->decimal('ketinggian_air_max', 5, 2)->default(20);
            $table->decimal('ppm_min', 7, 2)->change();
            $table->decimal('ppm_max', 7, 2)->change();
        });
    }
};
