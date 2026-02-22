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
        Schema::table('max_divulga_configs', function (Blueprint $table) {
            $table->decimal('tts_default_speed', 4, 2)->nullable()->default(1.25)->after('tts_api_key');
            $table->decimal('tts_default_noise_scale', 4, 3)->nullable()->default(0.750)->after('tts_default_speed');
            $table->decimal('tts_default_noise_w', 4, 3)->nullable()->default(0.850)->after('tts_default_noise_scale');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('max_divulga_configs', function (Blueprint $table) {
            $table->dropColumn(['tts_default_speed', 'tts_default_noise_scale', 'tts_default_noise_w']);
        });
    }
};
