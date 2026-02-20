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
        Schema::create('sistema_pagamentos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('licenca_id');
            $table->integer('dia_vencimento');
            $table->date('data_proximo_pagamento');
            $table->decimal('valor', 10, 2);
            $table->string('status')->default('pendente'); // pago, pendente, atrasado
            $table->timestamps();

            $table->foreign('licenca_id')->references('id')->on('loja_licencas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_pagamentos');
    }
};
