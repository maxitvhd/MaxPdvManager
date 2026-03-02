<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Adiciona resolução/metadados nos schedules
        Schema::table('tv_door_schedules', function (Blueprint $table) {
            $table->string('resolution')->nullable()->default('1920x1080')->after('is_active');
        });

        // Adiciona resolução nos layouts
        Schema::table('tv_door_layouts', function (Blueprint $table) {
            $table->string('resolution')->nullable()->default('1920x1080')->after('preview_path');
        });
    }

    public function down(): void
    {
        Schema::table('tv_door_schedules', function (Blueprint $table) {
            $table->dropColumn('resolution');
        });
        Schema::table('tv_door_layouts', function (Blueprint $table) {
            $table->dropColumn('resolution');
        });
    }
};
