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
        Schema::table('clientes_transacoes', function (Blueprint $table) {
            $table->string('descricao')->nullable()->after('usuario_codigo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes_transacoes', function (Blueprint $table) {
            $table->dropColumn('descricao');
        });
    }
};
