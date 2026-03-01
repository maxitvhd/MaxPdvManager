<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ClienteTransacao;
use App\Models\Loja;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ClienteController extends Controller
{
    /* ===================== HELPERS ===================== */

    private function isSuperAdmin($user): bool
    {
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    /**
     * Retorna as lojas que o usuário pode gerenciar
     */
    private function lojasPermitidas($user)
    {
        if ($this->isSuperAdmin($user)) {
            return Loja::orderBy('nome')->get();
        }

        return Loja::where('user_id', $user->id)
            ->orWhereHas('permissoes', function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->whereIn('role', ['Gerente', 'Administrador']);
            })
            ->orderBy('nome')
            ->get();
    }

    /**
     * Verifica se o usuário pode gerenciar a loja específica
     */
    private function podeGerenciarLoja($user, Loja $loja): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if ($loja->user_id === $user->id) return true;

        return $loja->permissoes()
            ->where('user_id', $user->id)
            ->whereIn('role', ['Gerente', 'Administrador'])
            ->exists();
    }

    /**
     * Verifica se o usuário pode alterar dados sensíveis do cliente
     */
    private function podeAlterarDadosSensiveis($user, Loja $loja): bool
    {
        if ($this->isSuperAdmin($user)) return true;
        if ($loja->user_id === $user->id) return true;

        return $loja->permissoes()
            ->where('user_id', $user->id)
            ->where('role', 'Gerente')
            ->exists();
    }

    /**
     * Gera o código único do cliente (ex.: MNL-0000001-MAX)
     */
    private function gerarCodigo(Loja $loja): string
    {
        $prefixo = strtoupper(substr(preg_replace('/[^A-Z0-9]/i', '', $loja->nome), 0, 3));
        $prefixo = str_pad($prefixo, 3, 'X');
        $total   = Cliente::where('loja_id', $loja->id)->count() + 1;
        $seq     = str_pad($total, 7, '0', STR_PAD_LEFT);
        $sufixo  = strtoupper(substr($loja->codigo, -3));
        return "{$prefixo}-{$seq}-{$sufixo}";
    }

    /* ===================== AÇÕES ===================== */

    /**
     * INDEX: Lista clientes da(s) loja(s) do usuário
     */
    public function index(Request $request)
    {
        $user  = auth()->user();
        $lojas = $this->lojasPermitidas($user);

        if ($lojas->isEmpty()) {
            return view('bank.clientes.index', [
                'clientes' => collect([]),
                'lojas'    => $lojas,
                'lojaAtual'=> null,
            ]);
        }

        $lojaCodigoSelecionado = $request->get('loja_codigo') ?? $lojas->first()->codigo;
        $lojaAtual = $lojas->firstWhere('codigo', $lojaCodigoSelecionado);

        if (!$lojaAtual) {
            return redirect()->route('bank.clientes.index')
                ->with('error', 'Loja não encontrada ou sem permissão.');
        }

        $query = Cliente::where('loja_id', $lojaAtual->id);

        // Filtros
        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        if ($busca = $request->get('busca')) {
            $query->where(function ($q) use ($busca) {
                $q->where('nome', 'like', "%{$busca}%")
                  ->orWhere('codigo', 'like', "%{$busca}%")
                  ->orWhere('usuario', 'like', "%{$busca}%")
                  ->orWhere('telefone', 'like', "%{$busca}%");
            });
        }

        $clientes = $query->orderBy('nome')->paginate(20)->withQueryString();

        return view('bank.clientes.index', compact('clientes', 'lojas', 'lojaAtual'));
    }

    /**
     * CREATE: Formulário de cadastro de cliente
     */
    public function create(Request $request)
    {
        $user  = auth()->user();
        $lojas = $this->lojasPermitidas($user);

        if ($lojas->isEmpty()) {
            return redirect()->route('bank.clientes.index')
                ->with('error', 'Você não tem lojas para gerenciar.');
        }

        $lojaPreSelecionada = $lojas->first();
        if ($lojaCodigo = $request->get('loja_codigo')) {
            $loja = $lojas->firstWhere('codigo', $lojaCodigo);
            if ($loja) $lojaPreSelecionada = $loja;
        }

        return view('bank.clientes.create', [
            'lojas'    => $lojas,
            'lojaAtual'=> $lojaPreSelecionada,
        ]);
    }

    /**
     * STORE: Salva novo cliente e gera link de ativação
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'loja_codigo'    => 'required|string|exists:lojas,codigo',
            'nome'           => 'required|string|max:255',
            'email'          => 'nullable|email|max:255',
            'telefone'       => 'nullable|string|max:20',
            'limite_credito' => 'required|numeric|min:0',
            'dia_fechamento' => 'required|integer|between:1,28',
            'cpf'            => 'nullable|string|max:20',
            'endereco'       => 'nullable|string|max:255',
            'bairro'         => 'nullable|string|max:100',
            'cidade'         => 'nullable|string|max:100',
            'estado'         => 'nullable|string|max:2',
            'cep'            => 'nullable|string|max:10',
        ], [
            'nome.required'           => 'O nome é obrigatório.',
            'limite_credito.required' => 'Informe o limite de crédito.',
            'limite_credito.min'      => 'O limite não pode ser negativo.',
        ]);

        $loja = Loja::where('codigo', $request->loja_codigo)->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $loja)) {
            return redirect()->back()->with('error', 'Sem permissão para criar clientes nessa loja.');
        }

        // Gerar código e token de ativação
        $codigo       = $this->gerarCodigo($loja);
        $linkToken    = Str::random(64);
        $linkExpira   = now()->addHours(72); // 72h para ativar

        $cliente = Cliente::create([
            'loja_id'        => $loja->id,
            'gerente'        => $user->id,
            'nome'           => $request->nome,
            'email'          => $request->email,
            'telefone'       => $request->telefone,
            'codigo'         => $codigo,
            'usuario'        => $request->usuario ?? Str::slug($request->nome . '-' . rand(100, 999)),
            'saldo'          => 0,
            'limite_credito' => $request->limite_credito,
            'credito_usado'  => 0,
            'status'         => 'esp_facial',
            'tipo'           => 'cliente',
            'dia_fechamento' => $request->dia_fechamento,
            'cpf'            => $request->cpf,
            'endereco'       => $request->endereco,
            'bairro'         => $request->bairro,
            'cidade'         => $request->cidade,
            'estado'         => $request->estado,
            'cep'            => $request->cep,
            'pin'            => '', // será definido no link de ativação
            'facial_vector'  => '[]', // será capturado no link de ativação
            'link_ativacao'  => $linkToken,
            'link_expires_at'=> $linkExpira,
        ]);

        // Criar link de ativação
        $linkAtivacao = route('banco.ativar', $linkToken);

        return redirect()
            ->route('bank.clientes.show', $codigo)
            ->with('success', "Cliente {$cliente->nome} criado com sucesso!")
            ->with('link_ativacao', $linkAtivacao);
    }

    /**
     * SHOW: Perfil completo do cliente
     */
    public function show(string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with(['loja', 'gerenteUsuario'])->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->route('bank.clientes.index')
                ->with('error', 'Sem permissão para ver esse cliente.');
        }

        $transacoesRecentes = $cliente->transacoes()
            ->orderBy('data_hora', 'desc')
            ->limit(10)
            ->get();

        $podeAlterarSensiveis = $this->podeAlterarDadosSensiveis($user, $cliente->loja);
        $linkAtivacao         = $cliente->link_ativacao
            ? route('banco.ativar', $cliente->link_ativacao)
            : null;

        return view('bank.clientes.show', compact(
            'cliente', 'transacoesRecentes', 'podeAlterarSensiveis', 'linkAtivacao'
        ));
    }

    /**
     * EDIT: Formulário de edição
     */
    public function edit(string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->route('bank.clientes.index')
                ->with('error', 'Sem permissão para editar esse cliente.');
        }

        $podeAlterarSensiveis = $this->podeAlterarDadosSensiveis($user, $cliente->loja);
        $lojas = $this->lojasPermitidas($user);

        return view('bank.clientes.edit', compact('cliente', 'podeAlterarSensiveis', 'lojas'));
    }

    /**
     * UPDATE: Salva alterações (com restrições por nível de acesso)
     */
    public function update(Request $request, string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->back()->with('error', 'Sem permissão.');
        }

        $rules = [
            'nome'     => 'required|string|max:255',
            'email'    => 'nullable|email|max:255',
            'telefone' => 'nullable|string|max:20',
        ];

        $podeAlterarSensiveis = $this->podeAlterarDadosSensiveis($user, $cliente->loja);

        if ($podeAlterarSensiveis) {
            $rules['limite_credito'] = 'nullable|numeric|min:0';
            $rules['dia_fechamento'] = 'nullable|integer|between:1,28';
            $rules['status']         = 'nullable|in:esp_facial,esp_documentos,esp_dados,processando,ativo,bloqueado,pag_atrasado,cobranca,juridico,cancelado';
            $rules['cpf']            = 'nullable|string|max:20';
            $rules['endereco']       = 'nullable|string|max:255';
            $rules['bairro']         = 'nullable|string|max:100';
            $rules['cidade']         = 'nullable|string|max:100';
            $rules['estado']         = 'nullable|string|max:2';
            $rules['cep']            = 'nullable|string|max:10';
        }

        $request->validate($rules);

        $dados = [
            'nome'     => $request->nome,
            'email'    => $request->email,
            'telefone' => $request->telefone,
        ];

        if ($podeAlterarSensiveis) {
            if ($request->filled('limite_credito')) $dados['limite_credito'] = $request->limite_credito;
            if ($request->filled('dia_fechamento')) $dados['dia_fechamento'] = $request->dia_fechamento;
            if ($request->filled('status'))         $dados['status']         = $request->status;
            if ($request->has('cpf'))               $dados['cpf']            = $request->cpf;
            if ($request->has('endereco'))          $dados['endereco']       = $request->endereco;
            if ($request->has('bairro'))            $dados['bairro']         = $request->bairro;
            if ($request->has('cidade'))            $dados['cidade']         = $request->cidade;
            if ($request->has('estado'))            $dados['estado']         = $request->estado;
            if ($request->has('cep'))               $dados['cep']            = $request->cep;
        }

        $cliente->update($dados);

        return redirect()->route('bank.clientes.show', $codigo)
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    /**
     * APROVAR: Aprova conta do cliente (gerente/dono)
     */
    public function aprovar(string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeAlterarDadosSensiveis($user, $cliente->loja)) {
            return redirect()->back()->with('error', 'Apenas gerente ou dono pode aprovar contas.');
        }

        // Verifica se tem os requisitos mínimos
        if ($cliente->status === 'esp_facial') {
            return redirect()->back()->with('error', 'Cliente ainda precisa configurar facial e PIN antes de ser aprovado.');
        }

        $cliente->update(['status' => 'ativo']);

        return redirect()->back()->with('success', "Conta de {$cliente->nome} aprovada com sucesso!");
    }

    /**
     * ADICIONAR CRÉDITO: Add crédito na carteira do cliente
     */
    public function adicionarCredito(Request $request, string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->back()->with('error', 'Sem permissão.');
        }

        $request->validate([
            'valor'    => 'required|numeric|min:0.01',
            'tipo'     => 'required|in:credito,pagamento,ajuste',
            'descricao'=> 'nullable|string|max:255',
        ], [
            'valor.min' => 'O valor deve ser maior que zero.',
        ]);

        $valor = (float) $request->valor;
        $tipo  = $request->tipo;

        // Atualiza saldo / crédito usado conforme o tipo
        if ($tipo === 'credito') {
            $novoLimite = $cliente->limite_credito + $valor;
            $cliente->update(['limite_credito' => $novoLimite]);
        } elseif ($tipo === 'pagamento' || $tipo === 'estorno') {
            $novoCreditoUsado = max(0, $cliente->credito_usado - $valor);
            $novoSaldo = $cliente->saldo + $valor;
            $cliente->update(['credito_usado' => $novoCreditoUsado, 'saldo' => $novoSaldo]);

            // Se tinha pagamento atrasado e zerou, retorna a ativo
            if ($cliente->status === 'pag_atrasado' && $novoCreditoUsado <= 0) {
                $cliente->update(['status' => 'ativo']);
            }
        } elseif ($tipo === 'ajuste') {
            // Ajuste manual no saldo direto
            $novoSaldo = max(0, $cliente->saldo + $valor);
            $cliente->update(['saldo' => $novoSaldo]);
        }

        // Registra a transação
        ClienteTransacao::create([
            'loja_id'        => $cliente->loja_id,
            'uuid'           => (string) Str::uuid(),
            'cliente_codigo' => $cliente->codigo,
            'tipo'           => $tipo,
            'valor'          => $valor,
            'data_hora'      => now(),
            'usuario_codigo' => $user->email,
            'descricao'      => $request->descricao ?? $this->descricaoDefault($tipo),
        ]);

        return redirect()->back()->with('success', 'Operação registrada com sucesso!');
    }

    private function descricaoDefault(string $tipo): string
    {
        return match ($tipo) {
            'credito'   => 'Crédito adicionado pelo gerente',
            'pagamento' => 'Pagamento de débito registrado',
            'ajuste'    => 'Ajuste manual de saldo',
            default     => 'Operação registrada',
        };
    }

    /**
     * ALTERAR STATUS: Muda o status do cliente
     */
    public function alterarStatus(Request $request, string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeAlterarDadosSensiveis($user, $cliente->loja)) {
            return redirect()->back()->with('error', 'Sem permissão para alterar status.');
        }

        $request->validate([
            'status' => 'required|in:esp_facial,esp_documentos,esp_dados,processando,ativo,bloqueado,pag_atrasado,cobranca,juridico,cancelado',
        ]);

        $cliente->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status atualizado!');
    }

    /**
     * TRANSAÇÕES: Histórico de crédito do cliente
     */
    public function transacoes(string $codigo, Request $request)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->route('bank.clientes.index')
                ->with('error', 'Sem permissão.');
        }

        $query = $cliente->transacoes()->orderBy('data_hora', 'desc');

        if ($tipo = $request->get('tipo')) {
            $query->where('tipo', $tipo);
        }

        $transacoes = $query->paginate(25)->withQueryString();

        return view('bank.clientes.transacoes', compact('cliente', 'transacoes'));
    }

    /**
     * DOCUMENTOS: Visualizar documentos enviados pelo cliente
     */
    public function documentos(string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->route('bank.clientes.index')
                ->with('error', 'Sem permissão.');
        }

        return view('bank.clientes.documentos', compact('cliente'));
    }

    /**
     * REENVIAR LINK: Regenera o link de ativação
     */
    public function reenviarLink(string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeGerenciarLoja($user, $cliente->loja)) {
            return redirect()->back()->with('error', 'Sem permissão.');
        }

        $linkToken  = Str::random(64);
        $linkExpira = now()->addHours(72);

        $cliente->update([
            'link_ativacao'  => $linkToken,
            'link_expires_at'=> $linkExpira,
        ]);

        $linkAtivacao = route('banco.ativar', $linkToken);

        return redirect()
            ->route('bank.clientes.show', $codigo)
            ->with('success', 'Link de ativação renovado!')
            ->with('link_ativacao', $linkAtivacao);
    }

    /**
     * DESTROY: Cancelar/excluir cliente
     */
    public function destroy(string $codigo)
    {
        $user    = auth()->user();
        $cliente = Cliente::where('codigo', $codigo)->with('loja')->firstOrFail();

        if (!$this->podeAlterarDadosSensiveis($user, $cliente->loja)) {
            return redirect()->back()->with('error', 'Apenas gerente ou dono pode cancelar contas.');
        }

        // Soft-cancel: muda status para cancelado
        $cliente->update(['status' => 'cancelado']);

        return redirect()->route('bank.clientes.index')
            ->with('success', 'Conta do cliente cancelada.');
    }
}
