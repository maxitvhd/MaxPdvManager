<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            // Vincula a campanha à loja (caso não exista ainda)
            if (!Schema::hasColumn('max_divulga_campaigns', 'loja_id')) {
                $table->unsignedBigInteger('loja_id')->nullable()->after('tenant_id');
            }
            // Texto de acompanhamento para disparo social (WhatsApp, Instagram etc.)
            if (!Schema::hasColumn('max_divulga_campaigns', 'copy_acompanhamento')) {
                $table->longText('copy_acompanhamento')->nullable()->after('copy');
            }
        });
    }

    public function down(): void
    {
        Schema::table('max_divulga_campaigns', function (Blueprint $table) {
            $table->dropColumn(['loja_id', 'copy_acompanhamento']);
        });
    }
};
