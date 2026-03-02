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
        // Players
        Schema::create('tv_door_players', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->string('name');
            $table->string('pairing_code', 10)->nullable()->unique();
            $table->string('device_token', 64)->nullable()->unique();
            $table->enum('status', ['pending', 'online', 'offline'])->default('pending');
            $table->timestamp('last_seen_at')->nullable();
            $table->json('meta_data')->nullable();
            $table->timestamps();
        });

        // Categories for Media
        Schema::create('tv_door_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->string('name');
            $table->timestamps();
        });

        // Media (Photos/Videos)
        Schema::create('tv_door_media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->foreignId('category_id')->nullable()->constrained('tv_door_categories')->onDelete('set null');
            $table->string('name');
            $table->string('file_path');
            $table->enum('type', ['image', 'video']);
            $table->integer('duration')->default(10); // in seconds
            $table->timestamps();
        });

        // Layouts (Canva-like compositions)
        Schema::create('tv_door_layouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained('lojas')->onDelete('cascade');
            $table->string('name');
            $table->json('content'); // JSON defining elements like clock, products, etc.
            $table->string('preview_path')->nullable();
            $table->timestamps();
        });

        // Schedules
        Schema::create('tv_door_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('player_id')->constrained('tv_door_players')->onDelete('cascade');
            // Can schedule a Layout, a Media file, or a MaxDivulga Campaign
            $table->unsignedBigInteger('schedulable_id');
            $table->string('schedulable_type'); // TvDoorLayout, TvDoorMedia, MaxDivulgaCampaign
            $table->json('days'); // ['mon', 'tue', ...]
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('priority')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tv_door_schedules');
        Schema::dropIfExists('tv_door_layouts');
        Schema::dropIfExists('tv_door_media');
        Schema::dropIfExists('tv_door_categories');
        Schema::dropIfExists('tv_door_players');
    }
};
