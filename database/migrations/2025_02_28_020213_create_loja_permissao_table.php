<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaPermissaoTable extends Migration
{
    public function up()
    {
        Schema::create('loja_permissao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('role')->nullable(); // Ex.: 'dono', 'contador', 'funcionario'
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loja_permissao');
    }
}