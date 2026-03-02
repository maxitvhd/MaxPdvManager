<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tv_door_schedules', function (Blueprint $table) {
            // Playlist: múltiplos conteúdos [{id, type, duration_override}, ...]
            $table->json('content_items')->nullable()->after('schedulable_type');
            // Múltiplos horários: [{day:'mon', start:'13:00', end:'23:00'}, ...]
            $table->json('time_slots')->nullable()->after('end_time');
        });
    }

    public function down(): void
    {
        Schema::table('tv_door_schedules', function (Blueprint $table) {
            $table->dropColumn(['content_items', 'time_slots']);
        });
    }
};
