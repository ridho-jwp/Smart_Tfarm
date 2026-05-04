<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Buat tabel hamadetection untuk menyimpan hasil analisis deteksi hama
     * menggunakan ESP32 Cam + Roboflow AI.
     */
    public function up(): void
    {
        if (!Schema::hasTable('hamadetection')) {
            Schema::create('hamadetection', function (Blueprint $table) {
                $table->id('id_analisis');
                $table->string('image_url')->nullable();          // path gambar hasil bounding box
                $table->string('label_hama')->nullable();         // sehat / ulat / siput / berlubang / tidak terdeteksi
                $table->decimal('confidence', 5, 4)->default(0);  // confidence score (0.0000 - 1.0000)
                $table->boolean('is_pestisida_pump')->default(false); // apakah pompa pestisida diaktifkan
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('hamadetection');
    }
};
