<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produto extends Model
{
    use HasFactory;

    protected $table = 'produtos';

    protected $fillable = [
        'produto_full_id',
        'loja_id', 
        'user_id',
        'preco',
        'preco_compra',
        'nome',
        'codigo_barra',
        'descricao',
        'estoque',
        'categoria',
        'imagem',
        'custo_medio',
        'margem',
        'comissao',
        'habilitar_venda_atacado',
        'quantidade_atacado',
        'preco_atacado',
        'codigo_fiscal',
        'estoque_minimo',
        'estoque_maximo',
        'controlar_estoque',
        'balanca_checkout',
        'custo_ultima_compra',
        'margem_ultima_compra'
    ];

    /**
     * Relacionamento: Produto pertence a um ProdutoFull
     */
    public function produtoFull()
    {
        return $this->belongsTo(ProdutoFull::class, 'produto_full_id');
    }
    /**
     * Relacionamento com os lotes do produto
     */
    public function lotes()
    {
        return $this->hasMany(ProdutoLote::class);
    }

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
