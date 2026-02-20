<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Licenca extends Model
{
    protected $table = 'loja_licencas';

    protected $fillable = ['user_id', 'loja_id', 'plano_id', 'codigo', 'key', 'descricao', 'validade', 'limite_dispositivos', 'modulos_adicionais', 'data_inativacao_grace_period', 'status'];

    protected $casts = [
        'validade' => 'date',
        'data_inativacao_grace_period' => 'date',
        'modulos_adicionais' => 'array',
    ];

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

    public function plano()
    {
        return $this->belongsTo(SistemaPlano::class, 'plano_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(SistemaPagamento::class, 'licenca_id');
    }

    public function transacoes()
    {
        return $this->hasMany(SistemaTransacao::class, 'licenca_id');
    }

    public function isValid()
    {
        if ($this->status !== 'ativo') {
            return false;
        }

        // Se o grace period estiver preenchido manualmente no BD e for menor que agora
        if ($this->data_inativacao_grace_period && now()->startOfDay()->greaterThan($this->data_inativacao_grace_period)) {
            return false;
        }

        // Verifica a validade somando com a carência de dias padrão do sistema
        if ($this->validade) {
            $config = SistemaConfiguracao::first();
            $diasCarencia = $config ? ($config->carencia_dias ?? 0) : 0;

            $dataLimite = \Carbon\Carbon::parse($this->validade)->addDays($diasCarencia);

            if (now()->startOfDay()->greaterThan($dataLimite)) {
                return false;
            }
        }

        return true;
    }
}
