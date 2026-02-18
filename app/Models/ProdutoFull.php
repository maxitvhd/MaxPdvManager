<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoFull extends Model
{
    use HasFactory;

    protected $table = 'produtos_full';

    protected $fillable = [
        'codigo_barra',
        'nome',
        'descricao',
        'categoria',
        'peso',
        'tamanho',
        'imagem',
        'descricao_ingredientes',
        'ingredientes',
        'embalagem',
        'fabricante',
        'marca'
    ];

    /**
     * Relacionamento: Um ProdutoFull pode ter vários Produtos (de diferentes clientes)
     */
    public function produtos()
    {
        return $this->hasMany(Produto::class, 'produto_full_id');
    }
}

