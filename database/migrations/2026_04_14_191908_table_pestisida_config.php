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
        Schema::create('pestisida_config', function (Blueprint $table) {
            $table->id();
            
            // Menambahkan foreign key ke tabel config_presets
            // constrained() secara otomatis mendeteksi tabel 'config_presets' 
            // jika nama kolomnya 'config_preset_id'. 
            // Karena Anda meminta 'id_preset', kita tentukan nama tabelnya secara manual.
            $table->foreignId('id_preset')
                  ->constrained('config_presets') 
                  ->onDelete('cascade'); // Jika preset dihapus, data pestisida ini ikut terhapus

            $table->decimal('dosis', 10, 2);
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pestisida_config');
    }
};