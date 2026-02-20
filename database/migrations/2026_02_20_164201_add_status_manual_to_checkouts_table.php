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
        Schema::table('loja_checkout', function (Blueprint $table) {
            // true se o usuário clicou no botão "Ativar/Desativar" da Web
            $table->boolean('status_manual')->default(false)->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loja_checkout', function (Blueprint $table) {
            $table->dropColumn('status_manual');
        });
    }
};
