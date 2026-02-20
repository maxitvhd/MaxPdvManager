<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SistemaConfiguracao;
use App\Models\SistemaPagamento;
use App\Models\SistemaTransacao;
use App\Models\Licenca;
use App\Services\MercadoPagoService;
use Exception;
use Illuminate\Support\Facades\Log;

class PagamentosController extends Controller
{
    /**
     * Configurações (Admin)
     */
    public function configuracoesAdmin()
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $config = SistemaConfiguracao::first() ?? new SistemaConfiguracao();
        return view('admin.pagamentos.configuracoes', compact('config'));
    }

    public function salvarConfiguracoes(Request $request)
    {
        if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
            abort(403, 'Acesso restrito a administradores.');
        }

        $data = $request->validate([
            'mercadopago_public_key' => 'nullable|string',
            'mercadopago_access_token' => 'nullable|string',
            'email_recebimento' => 'nullable|email',
            'dias_vencimento_permitidos' => 'nullable|array',
            'carencia_dias' => 'required|integer|min:0'
        ]);

        $config = SistemaConfiguracao::first() ?? new SistemaConfiguracao();
        $config->fill($data);
        $config->save();

        return redirect()->back()->with('success', 'Configurações de pagamento atualizadas!');
    }

    /**
     * Helper de Segurança para consultar lojas
     */
    private function getLojasPermitidas($user)
    {
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return \App\Models\Loja::orderBy('nome')->get();
        }
        return \App\Models\Loja::where('user_id', $user->id)
            ->orWhereHas('permissoes', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->orderBy('nome')->get();
    }

    /**
     * Lojista / Admin - Listagem de Faturas
     */
    public function indexFaturas(Request $request)
    {
        $user = auth()->user();
        $query = SistemaPagamento::with('licenca.loja')->latest();
        $lojasPermitidas = $this->getLojasPermitidas($user);
        $loja = null;

        // Filtro de Loja via loja_codigo (Seguro)
        if ($request->has('loja_codigo') && !empty($request->loja_codigo)) {
            $loja = $lojasPermitidas->firstWhere('codigo', $request->loja_codigo);
            if (!$loja) {
                abort(403, 'Acesso não autorizado a esta loja.');
            }
            $licencas = Licenca::where('loja_id', $loja->id)->pluck('id');
            $query->whereIn('licenca_id', $licencas);
        } else {
            // Se for Lojista comum e não passou filtro, só mostra das lojas dele
            if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
                $lojasIds = $lojasPermitidas->pluck('id');
                $licencas = Licenca::whereIn('loja_id', $lojasIds)->pluck('id');
                $query->whereIn('licenca_id', $licencas);
            }
        }

        $pagamentos = $query->paginate(15);

        // Se for admin, roteia para tela de admin, senão a tela de lojista
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            return view('admin.pagamentos.index', compact('pagamentos', 'lojasPermitidas', 'loja'));
        }

        return view('pagamentos.index', compact('pagamentos', 'lojasPermitidas', 'loja'));
    }

    /**
     * Lojista - Gerar Fatura / Escolher plano
     */
    public function gerarFatura(SistemaPagamento $pagamento, MercadoPagoService $mp)
    {
        try {
            $user = auth()->user();

            // Segurança: Somente admins ou donos da loja podem gerar/ver a fatura
            if (!$user->hasRole('admin') && !$user->hasRole('super-admin')) {
                $lojasIds = $this->getLojasPermitidas($user)->pluck('id')->toArray();
                if (!in_array($pagamento->licenca->loja_id ?? 0, $lojasIds)) {
                    abort(403, 'Acesso não autorizado a esta fatura.');
                }
            }

            if ($pagamento->status === 'pago') {
                return redirect()->back()->with('error', 'Esta fatura já foi paga.');
            }

            // Exemplo criar PIX (em view real, o usuario escolhe Cartao vs Pix)
            $pix = $mp->gerarPix(
                $pagamento->valor,
                "Fatura da Licença #{$pagamento->licenca_id}",
                auth()->user()->email,
                "PGTO_" . $pagamento->id
            );

            // Redireciona para exibir o QRCode ou Copia e Cola
            return view('pagamentos.checkout', compact('pagamento', 'pix'));

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Admin - Realizar Estorno
     */
    public function reembolsar(SistemaTransacao $transacao, MercadoPagoService $mp)
    {
        try {
            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('super-admin')) {
                abort(403, 'Acesso restrito a administradores.');
            }

            if ($transacao->tipo === 'estorno') {
                return redirect()->back()->with('error', 'Transação já consta como estornada estornar novamente.');
            }

            // Precisamos do ID real do MP (guardado no dados_pagamento)
            $mp_id = $transacao->dados_pagamento['id'] ?? null;
            if (!$mp_id)
                throw new Exception("ID de transação Mercado Pago não encontrado.");

            $mp->estornarPagamento($mp_id);

            $transacao->update(['tipo' => 'estorno']);
            SistemaTransacao::create([
                'user_id' => auth()->id(),
                'loja_id' => $transacao->loja_id,
                'licenca_id' => $transacao->licenca_id,
                'valor' => -$transacao->valor,
                'metodo_pagamento' => $transacao->metodo_pagamento,
                'tipo' => 'estorno_reverso',
                'data_transacao' => now()
            ]);

            return redirect()->back()->with('success', 'Pagamento reembolsado com sucesso.');

        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Rota de Callback das Views
     */
    public function sucesso()
    {
        return view('pagamentos.status', ['status' => 'sucesso']);
    }
    public function falha()
    {
        return view('pagamentos.status', ['status' => 'falha']);
    }
    public function pendente()
    {
        return view('pagamentos.status', ['status' => 'pendente']);
    }

    /**
     * Webhook de Transações do MP
     */
    public function webhook(Request $request, MercadoPagoService $mp)
    {
        try {
            // Webhook/IPN dispara type=payment e id
            if ($request->input('type') === 'payment' || $request->has('data.id')) {
                $id = $request->input('data.id') ?? $request->input('id');

                $pagamentoMP = $mp->consultarPagamento($id);
                if (!$pagamentoMP)
                    return response()->json(['error' => 'Not found'], 404);

                $ref = $pagamentoMP['external_reference'] ?? null;
                $status = $pagamentoMP['status'] ?? null;
                $valor = $pagamentoMP['transaction_amount'] ?? 0;
                $metodo = $pagamentoMP['payment_method_id'] ?? 'unknown';

                if ($ref && str_starts_with($ref, 'PGTO_')) {
                    $idFatura = str_replace('PGTO_', '', $ref);
                    $fatura = SistemaPagamento::find($idFatura);

                    if ($fatura && $fatura->status !== 'pago') {
                        if ($status === 'approved') {
                            $fatura->status = 'pago';
                            $fatura->save();

                            // Cria log de transação
                            SistemaTransacao::create([
                                'user_id' => $fatura->licenca->user_id ?? null,
                                'loja_id' => $fatura->licenca->loja_id ?? null,
                                'licenca_id' => $fatura->licenca_id,
                                'valor' => $valor,
                                'metodo_pagamento' => $metodo,
                                'tipo' => 'pagamento',
                                'dados_pagamento' => $pagamentoMP,
                                'data_transacao' => now()
                            ]);

                            // Renova a licença conforme o plano
                            $licenca = $fatura->licenca;
                            if ($licenca) {
                                $mesesValid = $licenca->plano ? $licenca->plano->meses_validade : 1;
                                $licenca->validade = now()->addMonths($mesesValid);
                                $licenca->status = 'ativo';
                                $licenca->save();
                            }
                        }
                    }
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            Log::error('Erro Webhook MP: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
