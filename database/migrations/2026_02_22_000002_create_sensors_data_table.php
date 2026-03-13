<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('sensors_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->decimal('ph', 4, 2)->nullable(); // pH air (0.00 - 14.00)
            $table->decimal('suhu', 5, 2)->nullable(); // Suhu dalam Celsius
            $table->decimal('ppm', 8, 2)->nullable(); // Parts per million (nutrisi)
            $table->timestamp('recorded_at')->nullable(); // Waktu pencatatan dari ESP32
            $table->timestamps();

            $table->index('recorded_at');
            $table->index(['device_id', 'recorded_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensors_data');
    }
};
