<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaTransacao extends Model
{
    protected $table = 'sistema_transacoes';

    protected $fillable = [
        'user_id',
        'loja_id',
        'licenca_id',
        'valor',
        'metodo_pagamento',
        'tipo',
        'dados_pagamento',
        'data_transacao'
    ];

    protected $casts = [
        'dados_pagamento' => 'array',
        'data_transacao' => 'datetime',
        'valor' => 'decimal:2'
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function licenca()
    {
        return $this->belongsTo(Licenca::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
