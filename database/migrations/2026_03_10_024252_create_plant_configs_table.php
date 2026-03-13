<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plant_configs', function (Blueprint $table) {
            $table->id();
            $table->string('parameter')->unique(); // e.g., 'ph', 'ppm', 'suhu'
            $table->float('min_optimal');
            $table->float('max_optimal');
            $table->string('unit')->nullable();
            $table->string('label');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plant_configs');
    }
};
