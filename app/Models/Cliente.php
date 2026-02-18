<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

    protected $fillable = [
    'loja_id',
    'gerente',       // <--- Faltava este
    'nome',
    'email',
    'telefone',
    'pin',           // <--- Faltava este
    'facial_vector',
    'codigo',
    'usuario',
    'saldo',
    'limite_credito',
    'credito_usado',
    'status',
    'tipo',
    'data_vencimento',
    'dia_fechamento',
    'cpf',
    'endereco',
    'bairro',
    'cidade',
    'estado',
    'cep',
];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'saldo' => 'decimal:2',
        'limite_credito' => 'decimal:2',
        'credito_usado' => 'decimal:2',
        'data_vencimento' => 'date',
        // Criptografia automática do Laravel
        'cpf' => 'encrypted',
        'endereco' => 'encrypted',
        'bairro' => 'encrypted',
        'cidade' => 'encrypted',
        'estado' => 'encrypted',
        'cep' => 'encrypted',
    ];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }
}

