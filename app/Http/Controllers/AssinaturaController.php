<?php

namespace App\Http\Controllers;

use App\Models\Licenca;
use App\Models\SistemaPlano;
use App\Models\SistemaAdicional;
use App\Models\SistemaPagamento;
use App\Services\MercadoPagoService;
use Illuminate\Http\Request;

class AssinaturaController extends Controller
{
    public function index(Licenca $licenca)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $lojasIds = \App\Models\Loja::where('user_id', $user->id)
                ->orWhereHas('permissoes', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->pluck('id')->toArray();

            if (!in_array($licenca->loja_id, $lojasIds)) {
                abort(403, 'Acesso não autorizado para visualizar esta assinatura.');
            }
        }

        $planos = SistemaPlano::all();
        $adicionais = SistemaAdicional::where('status', true)->get();

        return view('pagamentos.assinatura.index', compact('licenca', 'planos', 'adicionais'));
    }

    public function checkout(Request $request, Licenca $licenca)
    {
        $user = auth()->user();
        if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
            $lojasIds = \App\Models\Loja::where('user_id', $user->id)
                ->orWhereHas('permissoes', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->pluck('id')->toArray();

            if (!in_array($licenca->loja_id, $lojasIds)) {
                abort(403, 'Acesso não autorizado realizar checkout para esta licença.');
            }
        }

        $request->validate([
            'plano_id' => 'required|exists:sistema_planos,id',
            'adicionais' => 'nullable|array',
            'adicionais.*.id' => 'exists:sistema_adicionais,id',
        ]);

        $plano = SistemaPlano::find($request->plano_id);
        $total = $plano->valor;

        $carrinhoInfo = [
            'plano_id' => $plano->id,
            'plano_nome' => $plano->nome,
            'plano_valor' => $plano->valor,
            'meses_validade' => $plano->meses_validade,
            'limite_dispositivos_base' => $plano->limite_dispositivos,
            'extras' => []
        ];

        if ($request->has('adicionais')) {
            foreach ($request->adicionais as $extraItemId => $extraFormData) {
                if (!isset($extraFormData['ativo']))
                    continue;

                $adicional = SistemaAdicional::find($extraItemId);
                if ($adicional) {
                    $qtd = (isset($extraFormData['qtd']) && (int) $extraFormData['qtd'] > 0) ? (int) $extraFormData['qtd'] : 1;
                    $valorExtraItem = $adicional->valor * $qtd;
                    $total += $valorExtraItem;

                    $carrinhoInfo['extras'][] = [
                        'id' => $adicional->id,
                        'nome' => $adicional->nome,
                        'tipo' => $adicional->tipo,
                        'qtd' => $qtd,
                        'valor_unitario' => $adicional->valor,
                        'valor_total' => $valorExtraItem
                    ];
                }
            }
        }

        // Gera a Intenção de Pagamento (Fatura)
        $pagamento = SistemaPagamento::create([
            'licenca_id' => $licenca->id,
            'dia_vencimento' => now()->day,
            'valor' => $total,
            'status' => 'pendente',
            'data_proximo_pagamento' => now()->addMonths($plano->meses_validade), // Previsão
            'dados_assinatura' => $carrinhoInfo
        ]);

        return redirect()->route('pagamentos.gerar', $pagamento->id)
            ->with('success', 'Fatura gerada com sucesso! Conclua o pagamento para renovar a licença e expandir sua loja.');
    }
}
