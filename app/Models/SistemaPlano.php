<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SistemaPlano extends Model
{
    protected $fillable = [
        'nome',
        'meses_validade',
        'limite_dispositivos',
        'modulos_adicionais',
        'valor'
    ];

    protected $casts = [
        'modulos_adicionais' => 'array',
        'valor' => 'decimal:2'
    ];
}
