<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdutoLote extends Model
{
    use HasFactory;

    protected $fillable = [
        'produto_id',
        'user_id',
        'lote',
        'validade',
        'quantidade',
        'data_fabricacao',
    ];

    /**
     * Relacionamento com o produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class);
    }

    /**
     * Relacionamento com o usuário
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
