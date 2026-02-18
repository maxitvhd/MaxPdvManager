<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LojaVendaItem extends Model {
    protected $table = 'loja_vendas_itens';
    protected $guarded = ['id'];
    public $timestamps = false; // Itens não precisam de created_at individual
}