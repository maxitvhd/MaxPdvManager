<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LojaPermissao extends Model
{
    protected $table = 'loja_permissao';
    protected $fillable = ['loja_id', 'user_id', 'role'];

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}