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
        Schema::create('max_divulga_configs', function (Blueprint $table) {
            $table->id();
            $table->string('provider_ia')->nullable()->default('openai'); // openai, gemini
            $table->string('api_key_ia')->nullable();
            $table->string('model_ia')->nullable();
            $table->string('provider_tts')->nullable();
            $table->string('tts_host')->nullable();
            $table->string('tts_api_key')->nullable();
            $table->string('tts_model')->nullable();
            $table->string('tts_voice')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('max_divulga_configs');
    }
};
