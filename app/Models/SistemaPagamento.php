<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaPagamento extends Model
{
    protected $fillable = [
        'licenca_id',
        'dia_vencimento',
        'data_proximo_pagamento',
        'valor',
        'status',
        'dados_assinatura'
    ];

    protected $casts = [
        'data_proximo_pagamento' => 'date',
        'valor' => 'decimal:2',
        'dados_assinatura' => 'array'
    ];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class);
    }
}
