<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('loja_id');
            $table->string('nome');
            $table->string('usuario')->nullable(); 
            $table->string('email')->nullable();
            $table->string('telefone')->nullable(); 
            $table->string('pin'); 
            $table->text('facial_vector');
            $table->string('codigo')->unique();
            $table->unsignedBigInteger('gerente')->nullable(); // Permitir null para segurança
            $table->decimal('saldo', 10, 2)->default(0);
            $table->decimal('limite_credito', 10, 2)->default(0);
            $table->decimal('credito_usado', 10, 2)->default(0);
            $table->string('status')->default('ativo');
            $table->string('tipo')->default('cliente');
            $table->date('data_vencimento')->nullable();
            $table->integer('dia_fechamento')->default(1);
            
            // Dados Sensíveis
            $table->text('cpf')->nullable();
            $table->text('endereco')->nullable();
            $table->text('bairro')->nullable();
            $table->text('cidade')->nullable();
            $table->text('estado')->nullable();
            $table->text('cep')->nullable();
            
            $table->timestamps();

            // CHAVES ESTRANGEIRAS SEGURAS
            // onDelete('restrict') impede apagar a loja direto se houver cliente. 
            // Nós faremos a transferência via código antes de apagar.
            $table->foreign('loja_id')->references('id')->on('lojas')->onDelete('restrict');
            $table->foreign('gerente')->references('id')->on('users')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};


