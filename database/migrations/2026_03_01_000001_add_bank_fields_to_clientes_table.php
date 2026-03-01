<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Link de ativação temporário para setup de facial + PIN
            $table->string('link_ativacao')->nullable()->after('cep');
            $table->dateTime('link_expires_at')->nullable()->after('link_ativacao');

            // Fotos de documentos e perfil
            $table->string('foto_perfil')->nullable()->after('link_expires_at');
            $table->string('foto_cpf')->nullable()->after('foto_perfil');
            $table->string('foto_habilitacao')->nullable()->after('foto_cpf');
            $table->string('foto_comprovante')->nullable()->after('foto_habilitacao');
        });
    }

    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            $table->dropColumn([
                'link_ativacao',
                'link_expires_at',
                'foto_perfil',
                'foto_cpf',
                'foto_habilitacao',
                'foto_comprovante',
            ]);
        });
    }
};
