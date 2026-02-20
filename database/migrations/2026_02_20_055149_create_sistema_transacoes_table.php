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
        Schema::create('sistema_transacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('loja_id');
            $table->unsignedBigInteger('licenca_id');
            $table->decimal('valor', 10, 2);
            $table->string('metodo_pagamento'); // pix, credit_card
            $table->string('tipo')->default('pagamento'); // pagamento, estorno
            $table->json('dados_pagamento')->nullable();
            $table->timestamp('data_transacao')->useCurrent();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('loja_id')->references('id')->on('lojas')->onDelete('cascade');
            $table->foreign('licenca_id')->references('id')->on('loja_licencas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_transacoes');
    }
};
