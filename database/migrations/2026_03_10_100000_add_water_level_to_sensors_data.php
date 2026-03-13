<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tambah kolom ketinggian air ke tabel sensors_data.
     * water_level dalam satuan CM (dari dasar tandon ke permukaan air)
     */
    public function up(): void
    {
        Schema::table('sensors_data', function (Blueprint $table) {
            $table->decimal('water_level', 6, 2)->nullable()->after('ppm')
                ->comment('Ketinggian air tandon dalam cm');
        });
    }

    public function down(): void
    {
        Schema::table('sensors_data', function (Blueprint $table) {
            $table->dropColumn('water_level');
        });
    }
};
