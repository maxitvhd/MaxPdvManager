<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LojaCancelamento extends Model
{
    protected $table = 'loja_cancelamento';
    
    // Atualizado para suportar Sincronização e os campos do PDV
    protected $fillable = [
        'loja_id', 
        'uuid', 
        'usuario_codigo', // Importante
        'venda_codigo', 
        'produtos', 
        'valor_total', 
        'data_hora',
        'observacao',
        'autorizado_por',
        'checkout_mac',
        'user_id',
        'venda_id',
        'cancelamento_key_id'
    ];

    protected $casts = [
        'produtos' => 'array', 
        'data_hora' => 'datetime'
    ];

    public function loja() { return $this->belongsTo(Loja::class); }
    public function user() { return $this->belongsTo(User::class); }
    public function cancelamentoKey() { return $this->belongsTo(LojaCancelamentoKey::class, 'cancelamento_key_id'); }
}