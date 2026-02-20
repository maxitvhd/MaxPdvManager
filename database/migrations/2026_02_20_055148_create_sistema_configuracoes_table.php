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
        Schema::create('sistema_configuracoes', function (Blueprint $table) {
            $table->id();
            $table->string('mercadopago_public_key')->nullable();
            $table->string('mercadopago_access_token')->nullable();
            $table->string('email_recebimento')->nullable();
            $table->json('dias_vencimento_permitidos')->nullable(); // ex: [5, 10, 20, 25]
            $table->integer('carencia_dias')->default(10);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_configuracoes');
    }
};
