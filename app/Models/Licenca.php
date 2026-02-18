<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licenca extends Model
{
    protected $table = 'loja_licencas';

    protected $fillable = ['user_id', 'loja_id', 'codigo', 'key', 'descricao', 'validade', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function Checkout()
    {
        return $this->hasMany(Checkout::class);
    }
}   
