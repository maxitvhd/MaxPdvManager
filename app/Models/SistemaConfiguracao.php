<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaConfiguracao extends Model
{
    protected $table = 'sistema_configuracoes';

    protected $fillable = [
        'mercadopago_public_key',
        'mercadopago_access_token',
        'email_recebimento',
        'dias_vencimento_permitidos',
        'carencia_dias'
    ];

    protected $casts = [
        'dias_vencimento_permitidos' => 'array',
    ];
}
