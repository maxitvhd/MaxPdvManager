<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes_leads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('antiga_loja_id')->nullable();
            $table->string('nome');
            $table->string('email')->nullable();
            $table->string('telefone')->nullable();
            $table->string('codigo_original');
            
            // Campo JSON para guardar todos os dados do cliente (saldo, endereço, etc)
            $table->json('dados_completos')->nullable();
            
            $table->string('motivo_transferencia')->default('Exclusão de Loja');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes_leads');
    }
};