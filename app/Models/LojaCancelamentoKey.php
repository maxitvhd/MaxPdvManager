<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LojaCancelamentoKey extends Model
{
    protected $table = 'loja_cancelamento_key';
    protected $fillable = ['loja_id', 'user_id', 'chave'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->chave) {
                $model->chave = Str::random(12); // Gera chave única
            }
        });
    }

    public function loja() { return $this->belongsTo(Loja::class); }
    public function user() { return $this->belongsTo(User::class); }
}