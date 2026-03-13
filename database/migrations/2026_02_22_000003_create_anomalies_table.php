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
            $table->string('type'); // Jenis anomali (hama, penyakit, dll)
            $table->text('description')->nullable();
            $table->string('image_path')->nullable(); // Path gambar hasil deteksi
            $table->enum('severity', ['low', 'medium', 'high'])->default('medium');
            $table->decimal('confidence', 5, 2)->nullable(); // Confidence score AI (0-100%)
            $table->timestamp('resolved_at')->nullable();
            $table->timestamps();

            $table->index('severity');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('anomalies');
    }
};
