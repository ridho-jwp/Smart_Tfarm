<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::create('device_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('action'); // pump_on, pump_off, heartbeat, reboot, dll
            $table->json('payload')->nullable(); // Data tambahan
            $table->foreignId('performed_by')->nullable()
                ->constrained('users')->onDelete('set null'); // User yang melakukan aksi
            $table->timestamps();

            $table->index('action');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('device_logs');
    }
};
