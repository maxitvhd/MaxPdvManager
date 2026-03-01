<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration segura: adiciona os campos de documentos e link de ativação
 * à tabela clientes — apenas se ainda não existirem (idempotente).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            if (!Schema::hasColumn('clientes', 'link_ativacao')) {
                $table->string('link_ativacao')->nullable()->after('cep');
            }
            if (!Schema::hasColumn('clientes', 'link_expires_at')) {
                $table->dateTime('link_expires_at')->nullable()->after('link_ativacao');
            }
            if (!Schema::hasColumn('clientes', 'foto_perfil')) {
                $table->string('foto_perfil')->nullable()->after('link_expires_at');
            }
            if (!Schema::hasColumn('clientes', 'foto_cpf')) {
                $table->string('foto_cpf')->nullable()->after('foto_perfil');
            }
            if (!Schema::hasColumn('clientes', 'foto_habilitacao')) {
                $table->string('foto_habilitacao')->nullable()->after('foto_cpf');
            }
            if (!Schema::hasColumn('clientes', 'foto_comprovante')) {
                $table->string('foto_comprovante')->nullable()->after('foto_habilitacao');
            }
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $cols = ['link_ativacao','link_expires_at','foto_perfil','foto_cpf','foto_habilitacao','foto_comprovante'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('clientes', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
