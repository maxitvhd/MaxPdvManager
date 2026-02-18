<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class LojaVenda extends Model {
    protected $table = 'loja_vendas';
    protected $guarded = ['id']; // Libera todos os campos exceto ID
    
    public function itens() {
        return $this->hasMany(LojaVendaItem::class, 'loja_venda_id');
    }

    // --- ADICIONE ESTA FUNÇÃO ---
    public function cliente() {
        // Liga a coluna 'cliente_codigo' desta tabela (loja_vendas)
        // Com a coluna 'codigo' da tabela de Clientes
        return $this->belongsTo(Cliente::class, 'cliente_codigo', 'codigo');
    }
}