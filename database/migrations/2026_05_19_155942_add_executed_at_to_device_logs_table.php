<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // database/migrations/xxxx_add_executed_at_to_device_logs_table.php
    public function up(): void
    {
        Schema::table('device_logs', function (Blueprint $table) {
            $table->timestamp('executed_at')->nullable()->after('performed_by');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('device_logs', function (Blueprint $table) {
            //
        });
    }
};