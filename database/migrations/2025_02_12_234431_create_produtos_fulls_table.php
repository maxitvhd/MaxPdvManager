<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('produtos_full', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_barra')->unique();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->string('categoria')->nullable();
            $table->string('peso')->nullable();
            $table->string('tamanho')->nullable();
            $table->string('imagem')->nullable();
            $table->text('descricao_ingredientes')->nullable();
            $table->text('ingredientes')->nullable();
            $table->string('embalagem')->nullable();
            $table->string('fabricante')->nullable();
            $table->string('marca')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('produtos_full');
    }
};
