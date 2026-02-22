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
            $table->text('copy_locucao')->nullable()->after('copy_acompanhamento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            $table->dropColumn('copy_locucao');
        });
    }
};
