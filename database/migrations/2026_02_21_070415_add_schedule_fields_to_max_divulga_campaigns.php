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
            $table->boolean('is_scheduled')->default(false)->after('format');
            $table->json('scheduled_days')->nullable()->after('is_scheduled'); // ex: ["1", "3", "5"]
            $table->json('scheduled_times')->nullable()->after('scheduled_days'); // ex: ["09:00", "15:00"]
            $table->boolean('is_active')->default(true)->after('scheduled_times'); // pode ser pausada no painel
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            $table->dropColumn(['is_scheduled', 'scheduled_days', 'scheduled_times', 'is_active']);
        });
    }
};
