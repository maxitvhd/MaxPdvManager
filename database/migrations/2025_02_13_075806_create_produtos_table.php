<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('produtos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('produto_full_id')->constrained('produtos_full')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('codigo_barra');
            $table->string('nome')->nullable();
            $table->string('descricao')->nullable();
            $table->decimal('preco', 10, 2)->nullable();
            $table->decimal('preco_compra', 10, 2)->nullable();
            $table->integer('estoque')->default(0)->nullable();
            $table->string('categoria')->nullable();
            $table->string('imagem')->nullable();
            $table->decimal('custo_medio', 10, 2)->nullable();
            $table->decimal('margem', 10, 2)->nullable();
            $table->decimal('comissao', 10, 2)->nullable();
            $table->boolean('habilitar_venda_atacado')->default(false);
            $table->integer('quantidade_atacado')->nullable();
            $table->decimal('preco_atacado', 10, 2)->nullable();
            $table->string('codigo_fiscal')->nullable();
            $table->integer('estoque_minimo')->nullable();
            $table->integer('estoque_maximo')->nullable();
            $table->boolean('controlar_estoque')->default(false);
            $table->boolean('balanca_checkout')->default(false);
            $table->decimal('custo_ultima_compra', 10, 2)->nullable();
            $table->decimal('margem_ultima_compra', 10, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('produtos');
    }
};
