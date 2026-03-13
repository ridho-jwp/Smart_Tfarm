<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->string('device_id')->unique(); // Identitas unik ESP32
            $table->string('name');
            $table->enum('type', ['sensor', 'actuator', 'camera'])->default('sensor');
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_heartbeat')->nullable();
            $table->json('metadata')->nullable(); // Info tambahan perangkat
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
