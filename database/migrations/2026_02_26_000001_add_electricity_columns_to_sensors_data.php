<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('sensors_data', function (Blueprint $table) {
            $table->decimal('voltage', 6, 2)->nullable()->after('ppm'); // Tegangan (V)
            $table->decimal('current', 6, 3)->nullable()->after('voltage'); // Arus (A)
            $table->decimal('power', 8, 2)->nullable()->after('current'); // Daya (W)
            $table->decimal('energy', 10, 3)->nullable()->after('power'); // Energi (kWh)
            $table->decimal('frequency', 5, 2)->nullable()->after('energy'); // Frekuensi (Hz)
            $table->decimal('power_factor', 4, 2)->nullable()->after('frequency'); // Power Factor
        });
    }

    public function down(): void
    {
        Schema::table('sensors_data', function (Blueprint $table) {
            $table->dropColumn(['voltage', 'current', 'power', 'energy', 'frequency', 'power_factor']);
        });
    }
};
