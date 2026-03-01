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
        'gerente',
        'nome',
        'email',
        'telefone',
        'pin',
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
        'link_ativacao',
        'link_expires_at',
        'foto_perfil',
        'foto_cpf',
        'foto_habilitacao',
        'foto_comprovante',
    ];

    protected $hidden = ['pin', 'facial_vector'];

    protected $casts = [
        'saldo'          => 'decimal:2',
        'limite_credito' => 'decimal:2',
        'credito_usado'  => 'decimal:2',
        'data_vencimento'=> 'date',
        'link_expires_at'=> 'datetime',
        // Criptografia automática do Laravel
        'cpf'      => 'encrypted',
        'endereco' => 'encrypted',
        'bairro'   => 'encrypted',
        'cidade'   => 'encrypted',
        'estado'   => 'encrypted',
        'cep'      => 'encrypted',
    ];

    /* ===================== RELACIONAMENTOS ===================== */

    public function loja()
    {
        return $this->belongsTo(Loja::class);
    }

    public function gerenteUsuario()
    {
        return $this->belongsTo(User::class, 'gerente');
    }

    public function transacoes()
    {
        return $this->hasMany(ClienteTransacao::class, 'cliente_codigo', 'codigo');
    }

    /* ===================== SCOPES ===================== */

    public function scopePorLoja($query, $lojaId)
    {
        return $query->where('loja_id', $lojaId);
    }

    public function scopeAtivos($query)
    {
        return $query->where('status', 'ativo');
    }

    /* ===================== HELPERS ===================== */

    /**
     * Retorna o label legível do status em português
     */
    public function statusLabel(): string
    {
        return match ($this->status) {
            'esp_facial'      => 'Aguard. Facial/PIN',
            'esp_documentos'  => 'Aguard. Documentos',
            'esp_dados'       => 'Aguard. Dados',
            'processando'     => 'Em Análise',
            'ativo'           => 'Ativo',
            'bloqueado'       => 'Bloqueado',
            'pag_atrasado'    => 'Pag. Atrasado',
            'cobranca'        => 'Em Cobrança',
            'juridico'        => 'Jurídico',
            'cancelado'       => 'Cancelado',
            default           => ucfirst($this->status),
        };
    }

    /**
     * Retorna a classe de cor Bootstrap para o badge de status
     */
    public function statusColor(): string
    {
        return match ($this->status) {
            'ativo'           => 'success',
            'processando',
            'esp_facial',
            'esp_documentos',
            'esp_dados'       => 'warning',
            'pag_atrasado'    => 'orange',
            'bloqueado',
            'cobranca'        => 'danger',
            'juridico'        => 'dark',
            'cancelado'       => 'secondary',
            default           => 'info',
        };
    }

    /**
     * Retorna ícone FontAwesome para o status
     */
    public function statusIcon(): string
    {
        return match ($this->status) {
            'ativo'           => 'fa-check-circle',
            'processando'     => 'fa-clock',
            'esp_facial'      => 'fa-camera',
            'esp_documentos'  => 'fa-file-upload',
            'esp_dados'       => 'fa-user-edit',
            'pag_atrasado'    => 'fa-exclamation-triangle',
            'bloqueado'       => 'fa-lock',
            'cobranca'        => 'fa-bell',
            'juridico'        => 'fa-gavel',
            'cancelado'       => 'fa-times-circle',
            default           => 'fa-circle',
        };
    }

    /**
     * Saldo disponível = limite_credito - credito_usado
     */
    public function getSaldoDisponivelAttribute(): float
    {
        return max(0, $this->limite_credito - $this->credito_usado);
    }

    /**
     * Percentual de crédito utilizado
     */
    public function getPercentualUsoAttribute(): float
    {
        if ($this->limite_credito <= 0) return 0;
        return round(($this->credito_usado / $this->limite_credito) * 100, 1);
    }

    /**
     * Verifica se o link de ativação é válido e não expirou
     */
    public function linkAtivacaoValido(): bool
    {
        return $this->link_ativacao
            && $this->link_expires_at
            && $this->link_expires_at->isFuture();
    }

    /**
     * Dias de atraso no pagamento (baseado em data_vencimento)
     */
    public function getDiasAtrasoAttribute(): int
    {
        if (!$this->data_vencimento) return 0;
        $hoje = now()->startOfDay();
        $venc = $this->data_vencimento->startOfDay();
        return max(0, $venc->diffInDays($hoje, false) * -1);
    }
}
