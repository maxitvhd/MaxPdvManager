<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteTransacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BancoClienteController extends Controller
{
    /* ===================== HELPERS ===================== */

    private function getClienteLogado(): ?Cliente
    {
        $codigo = session('cliente_codigo');
        if (!$codigo) return null;
        return Cliente::where('codigo', $codigo)->first();
    }

    /* ===================== AUTENTICAÇÃO ===================== */

    /**
     * Tela de login do portal do cliente
     */
    public function loginForm()
    {
        if (session('cliente_codigo')) {
            return redirect()->route('banco.dashboard');
        }
        return view('bank.portal.login');
    }

    /**
     * Autenticar cliente — dois modos:
     *   modo "usuario": identificacao (codigo/usuario) + PIN
     *   modo "facial":  facial_score >= 0.75 + PIN
     * O PIN é obrigatório nos dois casos.
     */
    public function autenticar(Request $request)
    {
        $request->validate([
            'metodo'        => 'required|in:usuario,facial',
            'pin'           => 'required|string|min:4|max:8',
            'identificacao' => 'required_if:metodo,usuario|nullable|string',
            'facial_score'  => 'required_if:metodo,facial|nullable|numeric',
        ], [
            'pin.required'           => 'O PIN é obrigatório.',
            'pin.min'                => 'O PIN deve ter pelo menos 4 dígitos.',
            'identificacao.required_if' => 'Informe seu código ou usuário.',
        ]);

        $cliente = null;

        /* ---- MODO: USUÁRIO + PIN ---- */
        if ($request->metodo === 'usuario') {
            $id = trim($request->identificacao);
            $cliente = Cliente::where('codigo', $id)
                ->orWhere('usuario', $id)
                ->first();

            if (!$cliente) {
                return redirect()->back()->withInput()
                    ->with('error', 'Usuário ou código não encontrado.');
            }

            // Valida o PIN
            if ($request->pin !== $cliente->pin) {
                return redirect()->back()->withInput()
                    ->with('error', 'PIN incorreto. Verifique e tente novamente.');
            }
        }

        /* ---- MODO: FACIAL + PIN ---- */
        if ($request->metodo === 'facial') {
            $score = (float) $request->get('facial_score', 0);

            if ($score < 0.75) {
                return redirect()->back()->withInput()
                    ->with('error', 'Reconhecimento facial não confirmado. Tente de novo ou use o modo Usuário + PIN.');
            }

            // Busca o cliente pelo código retornado pelo JS (ou por identificação enviada junto)
            $codigoFacial = $request->get('facial_codigo');
            if ($codigoFacial) {
                $cliente = Cliente::where('codigo', $codigoFacial)->first();
            }

            if (!$cliente) {
                return redirect()->back()->withInput()
                    ->with('error', 'Não foi possível identificar o cliente pelo rosto. Use Usuário + PIN.');
            }

            // Valida o PIN de confirmação (obrigatório mesmo no facial)
            if ($request->pin !== $cliente->pin) {
                return redirect()->back()->withInput()
                    ->with('error', 'PIN incorreto. Mesmo com reconhecimento facial o PIN é exigido.');
            }
        }

        /* ---- VERIFICA STATUS DA CONTA ---- */
        if (!in_array($cliente->status, ['ativo', 'pag_atrasado', 'cobranca'])) {
            $label = $cliente->statusLabel();
            return redirect()->back()->withInput()
                ->with('error', "Conta não disponível para acesso (status: {$label}). Contate sua loja.");
        }

        /* ---- LOGIN BEM-SUCEDIDO ---- */
        session([
            'cliente_codigo'  => $cliente->codigo,
            'cliente_loja_id' => $cliente->loja_id,
        ]);

        return redirect()->route('banco.dashboard');
    }


    /**
     * Dashboard principal do cliente
     */
    public function dashboard()
    {
        $cliente = $this->getClienteLogado();

        $transacoesRecentes = $cliente->transacoes()
            ->orderBy('data_hora', 'desc')
            ->limit(5)
            ->get();

        // Faturas em aberto (débitos não pagos)
        $totalDebito = $cliente->transacoes()
            ->where('tipo', 'debito')
            ->sum('valor');

        $totalPago = $cliente->transacoes()
            ->whereIn('tipo', ['pagamento', 'estorno'])
            ->sum('valor');

        $saldoDevedor = max(0, $totalDebito - $totalPago);

        return view('bank.portal.dashboard', compact(
            'cliente', 'transacoesRecentes', 'saldoDevedor'
        ));
    }

    /**
     * Faturas (pagas e devedoras)
     */
    public function faturas(Request $request)
    {
        $cliente = $this->getClienteLogado();

        $aba = $request->get('aba', 'abertas');

        if ($aba === 'pagas') {
            $transacoes = $cliente->transacoes()
                ->whereIn('tipo', ['pagamento', 'estorno'])
                ->orderBy('data_hora', 'desc')
                ->paginate(20);
        } else {
            // Débitos não pagos agrupados por data
            $transacoes = $cliente->transacoes()
                ->where('tipo', 'debito')
                ->orderBy('data_hora', 'desc')
                ->paginate(20);
        }

        $diasAtraso = $cliente->dias_atraso;

        return view('bank.portal.faturas', compact('cliente', 'transacoes', 'aba', 'diasAtraso'));
    }

    /**
     * Pagar saldo devedor via portal
     */
    public function pagarSaldo(Request $request)
    {
        $cliente = $this->getClienteLogado();

        $request->validate([
            'valor'      => 'required|numeric|min:0.01',
            'pin_confirm'=> 'required|string',
        ], [
            'valor.min'         => 'O valor deve ser maior que zero.',
            'pin_confirm.required' => 'Confirme com seu PIN para pagar.',
        ]);

        // Confirmar PIN para pagamento
        if ($request->pin_confirm !== $cliente->pin) {
            return redirect()->back()->with('error', 'PIN incorreto. Pagamento não realizado.');
        }

        $valor = min((float) $request->valor, $cliente->credito_usado);

        if ($valor <= 0) {
            return redirect()->back()->with('error', 'Não há saldo devedor para pagar.');
        }

        // Atualiza crédito usado
        $novoCreditoUsado = max(0, $cliente->credito_usado - $valor);
        $novoSaldo        = $cliente->saldo + $valor;
        $cliente->update(['credito_usado' => $novoCreditoUsado, 'saldo' => $novoSaldo]);

        // Se zerou, retorna a ativo
        if ($novoCreditoUsado <= 0 && $cliente->status === 'pag_atrasado') {
            $cliente->update(['status' => 'ativo']);
        }

        // Registra transação
        ClienteTransacao::create([
            'loja_id'        => $cliente->loja_id,
            'uuid'           => (string) Str::uuid(),
            'cliente_codigo' => $cliente->codigo,
            'tipo'           => 'pagamento',
            'valor'          => $valor,
            'data_hora'      => now(),
            'usuario_codigo' => $cliente->codigo,
            'descricao'      => 'Pagamento realizado pelo cliente via portal',
        ]);

        return redirect()->route('banco.faturas')
            ->with('success', 'Pagamento de R$ ' . number_format($valor, 2, ',', '.') . ' registrado!');
    }

    /**
     * Perfil do cliente
     */
    public function perfil()
    {
        $cliente = $this->getClienteLogado();
        return view('bank.portal.perfil', compact('cliente'));
    }

    /**
     * Atualizar dados pessoais (email, telefone)
     */
    public function atualizarPerfil(Request $request)
    {
        $cliente = $this->getClienteLogado();

        $request->validate([
            'email'       => 'nullable|email|max:255',
            'telefone'    => 'nullable|string|max:20',
            'pin_atual'   => 'required|string',
            'pin_novo'    => 'nullable|string|min:4|max:8',
            'pin_confirm' => 'nullable|string|same:pin_novo',
        ], [
            'pin_confirm.same' => 'Os PINs não coincidem.',
            'pin_atual.required' => 'Confirme com seu PIN atual.',
        ]);

        // Verificar PIN atual
        if ($request->pin_atual !== $cliente->pin) {
            return redirect()->back()->with('error', 'PIN atual incorreto.');
        }

        $dados = [
            'email'    => $request->email,
            'telefone' => $request->telefone,
        ];

        if ($request->filled('pin_novo')) {
            $dados['pin'] = $request->pin_novo;
        }

        $cliente->update($dados);

        return redirect()->route('banco.perfil')
            ->with('success', 'Dados atualizados com sucesso!');
    }

    /**
     * Upload de documentos pelo cliente
     */
    public function uploadDocumentos(Request $request)
    {
        $cliente = $this->getClienteLogado();

        $request->validate([
            'foto_perfil'        => 'nullable|image|max:5120',
            'foto_cpf'           => 'nullable|image|max:5120',
            'foto_habilitacao'   => 'nullable|image|max:5120',
            'foto_comprovante'   => 'nullable|image|max:5120',
        ], [
            'max' => 'A foto não pode ultrapassar 5MB.',
        ]);

        $loja   = $cliente->loja;
        $basePath = "lojas/{$loja->codigo}/clientes/{$cliente->codigo}";

        $campos = ['foto_perfil', 'foto_cpf', 'foto_habilitacao', 'foto_comprovante'];
        $nomes  = ['perfil.jpg', 'cpf.jpg', 'habilitacao.jpg', 'comprovante_residencia.jpg'];
        $dados  = [];

        foreach ($campos as $i => $campo) {
            if ($request->hasFile($campo)) {
                $arquivo = $request->file($campo);
                $path    = "{$basePath}/{$nomes[$i]}";
                Storage::disk('local')->put($path, file_get_contents($arquivo->getRealPath()));
                $dados[$campo] = $path;
            }
        }

        if (empty($dados)) {
            return redirect()->back()->with('error', 'Nenhum arquivo enviado.');
        }

        $cliente->update($dados);

        // Avança status se ainda estava esperando documentos
        if ($cliente->status === 'esp_documentos') {
            $cliente->update(['status' => 'processando']);
        }

        return redirect()->route('banco.perfil')
            ->with('success', 'Documentos enviados! Aguarde a aprovação da loja.');
    }

    /**
     * Logout do portal do cliente
     */
    public function logout()
    {
        session()->forget(['cliente_codigo', 'cliente_loja_id']);
        return redirect()->route('banco.login')
            ->with('success', 'Você saiu com segurança.');
    }
}
