<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Tambah kolom value & threshold ke tabel anomalies
     * untuk menyimpan nilai sensor dan batas yang dilanggar.
     */
    public function up(): void
    {
        Schema::table('anomalies', function (Blueprint $table) {
            $table->decimal('value', 8, 2)->nullable()->after('confidence')
                ->comment('Nilai sensor saat anomali terdeteksi');
            $table->decimal('threshold', 8, 2)->nullable()->after('value')
                ->comment('Batas ambang yang dilanggar');
        });
    }

    public function down(): void
    {
        Schema::table('anomalies', function (Blueprint $table) {
            $table->dropColumn(['value', 'threshold']);
        });
    }
};
