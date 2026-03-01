<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClienteTransacao extends Model
{
    use HasFactory;

    protected $table = 'clientes_transacoes';

    protected $fillable = [
        'loja_id',
        'uuid',
        'cliente_codigo',
        'venda_codigo',
        'tipo',
        'valor',
        'data_hora',
        'usuario_codigo',
        'checkout_mac',
        'descricao',
    ];

    protected $casts = [
        'valor'     => 'decimal:2',
        'data_hora' => 'datetime',
    ];

    /* ===================== RELACIONAMENTOS ===================== */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_codigo', 'codigo');
    }

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    /* ===================== HELPERS ===================== */

    /**
     * Label legível do tipo de transação
     */
    public function tipoLabel(): string
    {
        return match ($this->tipo) {
            'debito'    => 'Débito (Compra)',
            'credito'   => 'Crédito',
            'pagamento' => 'Pagamento',
            'ajuste'    => 'Ajuste Manual',
            'estorno'   => 'Estorno',
            default     => ucfirst($this->tipo),
        };
    }

    /**
     * Cor do badge para o tipo de transação
     */
    public function tipoColor(): string
    {
        return match ($this->tipo) {
            'debito'    => 'danger',
            'credito',
            'pagamento',
            'estorno'   => 'success',
            'ajuste'    => 'info',
            default     => 'secondary',
        };
    }

    /**
     * Ícone para o tipo de transação
     */
    public function tipoIcon(): string
    {
        return match ($this->tipo) {
            'debito'    => 'fa-arrow-up text-danger',
            'credito'   => 'fa-arrow-down text-success',
            'pagamento' => 'fa-check-circle text-success',
            'ajuste'    => 'fa-sliders-h text-info',
            'estorno'   => 'fa-undo text-success',
            default     => 'fa-circle text-secondary',
        };
    }
}