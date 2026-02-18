<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SESSÕES DE CAIXA (Abertura e Fechamento)
        Schema::create('loja_caixa_sessoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_id');
            $table->uuid('uuid')->unique(); // Link com o SQLite
            
            $table->string('operador');
            $table->string('usuario_codigo');
            
            $table->dateTime('abertura');
            $table->dateTime('fechamento')->nullable();
            
            $table->decimal('valor_abertura', 10, 2);
            $table->decimal('valor_fechamento', 10, 2)->nullable();
            
            $table->string('status'); // 'aberto', 'fechado'
            $table->string('checkout_mac'); // Identifica o computador
            
            $table->timestamps();
            $table->foreign('loja_id')->references('id')->on('lojas')->onDelete('cascade');
        });

        // 2. MOVIMENTAÇÕES (Sangria e Suprimento)
        Schema::create('loja_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_id');
            $table->uuid('uuid')->unique();
            
            $table->string('tipo'); // 'sangria' ou 'suprimento'
            $table->string('operador');
            $table->decimal('valor', 10, 2);
            $table->string('descricao')->nullable();
            $table->dateTime('data_hora');
            $table->string('checkout_mac');
            
            $table->timestamps();
            $table->foreign('loja_id')->references('id')->on('lojas')->onDelete('cascade');
        });

        // 3. VENDAS - CABEÇALHO
        Schema::create('loja_vendas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_id');
            $table->uuid('uuid')->unique();
            
            $table->string('venda_codigo');
            $table->string('operador');
            $table->string('usuario_codigo');
            $table->dateTime('data_hora');
            
            // Totais
            $table->decimal('total', 10, 2);
            $table->string('metodo_pagamento'); 
            $table->decimal('valor_recebido', 10, 2)->nullable();
            $table->decimal('troco', 10, 2)->default(0);
            
            // Produtividade
            $table->decimal('tempo_atendimento', 10, 2)->default(0);
            
            // Cliente
            $table->boolean('is_cliente')->default(false);
            $table->string('cliente_codigo')->nullable();

            $table->string('checkout_mac')->nullable();
            $table->timestamps();

            $table->foreign('loja_id')->references('id')->on('lojas')->onDelete('cascade');
            $table->index(['loja_id', 'data_hora']); // Para filtrar relatórios por data rápido
        });

        // 4. ITENS DA VENDA (Fundamental para "Produtos Mais Vendidos")
        Schema::create('loja_vendas_itens', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_venda_id');
            
            $table->string('produto_nome');
            $table->string('codigo_barra')->nullable();
            $table->string('categoria')->nullable();
            
            $table->decimal('quantidade', 10, 3);
            $table->decimal('preco_unitario', 10, 2);
            $table->decimal('subtotal', 10, 2);
            $table->decimal('custo', 10, 2)->default(0); // Para calcular Lucro
            
            $table->foreign('loja_venda_id')->references('id')->on('loja_vendas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loja_vendas_itens');
        Schema::dropIfExists('loja_vendas');
        Schema::dropIfExists('loja_movimentacoes');
        Schema::dropIfExists('loja_caixa_sessoes');
    }
};