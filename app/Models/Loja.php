<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Loja extends Model
{
    protected $table = 'lojas';

    protected $fillable = ['nome', 'user_id', 'cnpj', 'cpf', 'codigo', 'logo', 'descricao','email', 'telefone', 'endereco', 'bairro', 'cidade', 'estado', 'cep','status'];

    public function licencas()
    {
        return $this->hasMany(Licenca::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function permissoes()
    {
        return $this->hasMany(LojaPermissao::class, 'loja_id');
    }

    public function getRouteKeyName()
    {
        // Permite bindings de rota usando o código da loja
        return 'codigo';
    }
}