<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SistemaAdicional extends Model
{
    use HasFactory;

    protected $table = 'sistema_adicionais';

    protected $fillable = [
        'nome',
        'descricao',
        'tipo',
        'valor',
        'status',
    ];
}
