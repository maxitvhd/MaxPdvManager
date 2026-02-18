<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaCancelamentoTable extends Migration
{
    public function up()
    {
        Schema::create('loja_cancelamento', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_id');
            
            // Chave de Sincronização
            $table->uuid('uuid')->unique(); 

            // Identificação (Strings para suportar código do PDV)
            $table->string('usuario_codigo')->nullable(); // O erro estava aqui (faltava essa coluna)
            $table->string('venda_codigo')->nullable();   // Padronizado (antes era codigo_venda)
            
            // Dados do Cancelamento
            $table->json('produtos')->nullable();
            $table->decimal('valor_total', 10, 2);
            $table->dateTime('data_hora');
            
            // Detalhes extras que o PDV envia
            $table->text('observacao')->nullable();       // Faltava
            $table->string('autorizado_por')->nullable(); // Faltava
            $table->string('checkout_mac')->nullable();   // Faltava
            
            // Campos de relacionamento legado (Opcionais agora)
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->unsignedBigInteger('venda_id')->nullable();
            $table->unsignedBigInteger('cancelamento_key_id')->nullable();

            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('loja_id')->references('id')->on('lojas')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('loja_cancelamento');
    }
}