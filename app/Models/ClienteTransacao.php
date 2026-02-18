<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteTransacao extends Model
{
    use HasFactory;

    protected $table = 'clientes_transacoes'; // Nome da tabela que criamos na migration anterior

    protected $fillable = [
        'loja_id',
        'uuid',
        'cliente_codigo',
        'venda_codigo',
        'tipo',
        'valor',
        'data_hora',
        'usuario_codigo',
        'checkout_mac'
    ];
}