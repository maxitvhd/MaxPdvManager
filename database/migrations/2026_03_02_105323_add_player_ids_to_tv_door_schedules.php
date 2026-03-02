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
        Schema::table('tv_door_schedules', function (Blueprint $table) {
            $table->json('player_ids')->nullable()->after('player_id');
        });

        // Migrar dados existentes de player_id para player_ids
        \DB::table('tv_door_schedules')->get()->each(function ($s) {
            if ($s->player_id) {
                \DB::table('tv_door_schedules')
                    ->where('id', $s->id)
                    ->update(['player_ids' => json_encode([(int)$s->player_id])]);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tv_door_schedules', function (Blueprint $table) {
            $table->dropColumn('player_ids');
        });
    }
};
