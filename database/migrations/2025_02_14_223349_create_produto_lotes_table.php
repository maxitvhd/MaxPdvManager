<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('produto_lotes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('produto_id');
            $table->unsignedBigInteger('user_id');
            $table->string('lote')->nullable();
            $table->integer('quantidade')->nullable();
            $table->date('validade')->nullable();
            $table->date('data_fabricacao')->nullable();
            $table->timestamps();

            // Chaves estrangeiras
            $table->foreign('produto_id')->references('id')->on('produtos')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('produtos_lotes');
    }
};
