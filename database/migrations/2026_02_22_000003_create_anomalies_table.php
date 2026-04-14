<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hamadetection', function (Blueprint $table) {
            $table->id('id_analisis'); 
            
            $table->string('image_url');
            $table->string('label_hama');
            $table->float('confidence');
            $table->boolean('is_pestisida_pump')->default(false);
            $table->timestamps(); 
        });
    }

    public function down(): void
    {
    }
};
?>