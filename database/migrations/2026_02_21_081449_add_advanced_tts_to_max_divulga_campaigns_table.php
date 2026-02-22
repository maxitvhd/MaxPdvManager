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
            $table->decimal('noise_scale', 4, 3)->nullable()->after('audio_speed');
            $table->decimal('noise_w', 4, 3)->nullable()->after('noise_scale');
            $table->string('audio_file_path')->nullable()->after('file_path')->comment('Caminho do mp3 quando criado junto com PDF/PNG');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            $table->dropColumn(['noise_scale', 'noise_w', 'audio_file_path']);
        });
    }
};
