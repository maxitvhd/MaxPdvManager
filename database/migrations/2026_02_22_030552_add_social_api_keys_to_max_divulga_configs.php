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
            $table->string('facebook_client_id')->nullable();
            $table->string('facebook_client_secret')->nullable();
            $table->string('google_client_id')->nullable();
            $table->string('google_client_secret')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('max_divulga_configs', function (Blueprint $table) {
            $table->dropColumn(['facebook_client_id', 'facebook_client_secret', 'google_client_id', 'google_client_secret']);
        });
    }
};
