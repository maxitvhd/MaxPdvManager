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
        Schema::create('loja_checkout', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('licenca_id');
            $table->string('codigo')->unique()->nullable();
            $table->enum('status', ['ativo', 'inativo'])->default('ativo');
            $table->string('descricao')->nullable();
            $table->string('ip')->nullable();
            $table->string('mac')->nullable();
            $table->string('sistema_operacional')->nullable();
            $table->string('hardware')->nullable();
            $table->timestamps();
        
            $table->foreign('licenca_id')->references('id')->on('loja_licencas')->onDelete('cascade');
        });
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maquinas');
    }
};
