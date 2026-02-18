<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('codigo');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('usuario')->nullable();
            $table->string('location')->nullable();
            $table->string('endereco')->nullable();
            $table->string('bairro')->nullable();
            $table->string('cidade')->nullable();
            $table->string('estado')->nullable();
            $table->string('cep')->nullable();
            $table->string('geolocalizacao')->nullable();
            $table->string('ip')->nullable();
            $table->string('avatar')->nullable();
            $table->string('imagem')->nullable();
            $table->string('acesso')->nullable();
            $table->string('funcao')->nullable();
            $table->bigInteger('phone')->nullable();
            $table->string('location')->nullable();
            $table->string('about_me')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
