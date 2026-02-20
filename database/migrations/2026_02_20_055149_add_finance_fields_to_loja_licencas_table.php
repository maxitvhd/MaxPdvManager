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
        Schema::table('loja_licencas', function (Blueprint $table) {
            $table->unsignedBigInteger('plano_id')->nullable()->after('loja_id');
            $table->integer('limite_dispositivos')->default(1)->after('validade');
            $table->json('modulos_adicionais')->nullable()->after('limite_dispositivos');
            $table->date('data_inativacao_grace_period')->nullable()->after('modulos_adicionais');

            $table->foreign('plano_id')->references('id')->on('sistema_planos')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loja_licencas', function (Blueprint $table) {
            $table->dropForeign(['plano_id']);
            $table->dropColumn(['plano_id', 'limite_dispositivos', 'modulos_adicionais', 'data_inativacao_grace_period']);
        });
    }
};
