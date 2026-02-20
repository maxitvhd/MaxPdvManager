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
        Schema::create('sistema_planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->integer('meses_validade')->default(1);
            $table->integer('limite_dispositivos')->default(1);
            $table->json('modulos_adicionais')->nullable();
            $table->decimal('valor', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sistema_planos');
    }
};
