<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLojaCancelamentoKeyTable extends Migration
{
    public function up()
    {
        Schema::create('loja_cancelamento_key', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loja_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Funcionário autorizado
            $table->string('chave')->unique(); // Código único gerado
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loja_cancelamento_key');
    }
}