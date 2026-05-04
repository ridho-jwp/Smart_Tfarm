<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('anomalies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('device_id')->constrained('devices')->onDelete('cascade');
            $table->string('type');           // jenis anomali: suhu_tinggi, ph_rendah, dll
            $table->text('description')->nullable();
            $table->string('severity')->nullable()->default('medium'); // low, medium, high
            $table->decimal('confidence', 5, 4)->nullable();
            $table->decimal('value', 10, 4)->nullable();     // nilai sensor saat anomali
            $table->decimal('threshold', 10, 4)->nullable(); // batas yang dilampaui
            $table->string('image_path')->nullable();
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};