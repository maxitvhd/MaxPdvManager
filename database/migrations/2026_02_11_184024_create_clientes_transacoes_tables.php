<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Espelho da tabela 'clientes_transacoes' do PDV
        Schema::create('clientes_transacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_id');
            $table->uuid('uuid')->unique(); // A chave mágica da sincronização
            $table->string('cliente_codigo');
            $table->string('venda_codigo')->nullable();
            $table->string('tipo'); // debito, pagamento, ajuste
            $table->decimal('valor', 10, 2);
            $table->dateTime('data_hora');
            $table->string('usuario_codigo')->nullable();
            $table->string('checkout_mac')->nullable(); // Para saber qual caixa enviou
            $table->timestamps();

            $table->foreign('loja_id')->references('id')->on('lojas');
        });

        
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_transacoes');
        Schema::dropIfExists('pdv_vendas');
    }
};