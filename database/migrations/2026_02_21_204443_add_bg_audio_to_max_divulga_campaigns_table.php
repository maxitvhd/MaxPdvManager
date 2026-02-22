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
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            $table->string('bg_audio')->nullable()->after('audio_speed');
            $table->decimal('bg_volume', 3, 2)->default(0.20)->after('bg_audio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            $table->dropColumn(['bg_audio', 'bg_volume']);
        });
    }
};
