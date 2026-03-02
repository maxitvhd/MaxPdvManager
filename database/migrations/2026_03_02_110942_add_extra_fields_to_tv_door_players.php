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
        Schema::table('tv_door_players', function (Blueprint $table) {
            $table->text('description')->nullable()->after('name');
            $table->string('forced_resolution', 20)->nullable()->after('status');
            $table->boolean('is_active')->default(true)->after('forced_resolution');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tv_door_players', function (Blueprint $table) {
            $table->dropColumn(['description', 'forced_resolution', 'is_active']);
        });
    }
};
