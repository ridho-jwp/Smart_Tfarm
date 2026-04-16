<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pestisida_config', function (Blueprint $table) {
            // Menambahkan kolom status setelah kolom deskripsi
            $table->enum('status', ['pending', 'dikirim'])->default('pending')->after('deskripsi');
        });
    }

    public function down(): void
    {
        Schema::table('pestisida_config', function (Blueprint $table) {
            // Menghapus kolom status jika migrasi di-rollback
            $table->dropColumn('status');
        });
    }
};