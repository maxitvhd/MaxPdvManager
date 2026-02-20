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

        // Se tiver grace period ativado e a data atual for maior que ele = inativo automatizado.
        if ($this->data_inativacao_grace_period && now()->greaterThan($this->data_inativacao_grace_period)) {
            return false;
        }

        // Validade básica 
        if ($this->validade && now()->startOfDay()->greaterThan($this->validade) && !$this->data_inativacao_grace_period) {
            return false;
        }

        return true;
    }
}
