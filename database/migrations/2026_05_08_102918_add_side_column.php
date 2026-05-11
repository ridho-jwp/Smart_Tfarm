<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('hamadetection', function (Blueprint $table) {
            $table->boolean('side_left')->default(false)->after('label_hama');
            $table->boolean('side_right')->default(false)->after('side_left');
        });
    }

    public function down(): void
    {
        Schema::table('hamadetection', function (Blueprint $table) {
            $table->dropColumn(['side_left', 'side_right']);
        });
    }
};