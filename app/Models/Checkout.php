<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    protected $table = 'loja_checkout';

    protected $fillable = ['licenca_id', 'codigo', 'mac', 'descricao', 'ip', 'sistema_operacional', 'hardware', 'status'];

    public function licenca()
    {
        return $this->belongsTo(Licenca::class);
    }
}
