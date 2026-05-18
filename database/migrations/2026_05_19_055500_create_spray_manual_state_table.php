<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('spray_manual_state', function (Blueprint $table) {
            $table->id();
            // Mode: true = otomatis aktif (ikuti deteksi hama), false = manual penuh
            $table->boolean('auto_mode')->default(true);
            // State manual penyemprotan kiri & kanan
            $table->boolean('manual_kiri')->default(false);
            $table->boolean('manual_kanan')->default(false);
            // Siapa yang terakhir mengubah state
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spray_manual_state');
    }
};
