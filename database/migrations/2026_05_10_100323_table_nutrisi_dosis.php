<?php
// database/migrations/xxxx_create_nutrisi_doses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Blueprint;
use Illuminate\Database\Schema\Blueprint as SchemaBlueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('nutrisi_dosis', function (SchemaBlueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->cascadeOnDelete();

            // Snapshot kondisi saat dosis dibuat
            $table->decimal('ppm_saat_ini', 8, 2);
            $table->decimal('ppm_target', 8, 2);  // = min_optimal dari config
            $table->decimal('ppm_deficit', 8, 2);  // ppm_target - ppm_saat_ini

            // Parameter kalibrasi (disimpan agar audit trail lengkap)
            $table->decimal('volume_tandon_liter', 8, 2); // volume air tandon (liter)
            $table->decimal('ppm_per_ml', 8, 4); // berapa ppm naik per 1 mL AB Mix per liter
            $table->decimal('ml_per_detik', 8, 4); // kecepatan pompa peristaltik (mL/s)

            // Hasil kalkulasi
            $table->decimal('dosis_ml', 8, 2); // mL yang harus dipompa
            $table->decimal('durasi_detik', 8, 2); // dosis_ml / ml_per_detik

            // Status antrian
            $table->enum('status', ['pending', 'dispatched', 'done', 'cancelled'])
                ->default('pending');

            $table->timestamp('dispatched_at')->nullable();
            $table->timestamp('done_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nutrisi_dosis');
    }
};