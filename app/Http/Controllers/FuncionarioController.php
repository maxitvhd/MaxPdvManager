<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loja;
use App\Models\User;
use App\Models\LojaPermissao;
use Illuminate\Support\Facades\Auth;

class FuncionarioController extends Controller
{
    /**
     * Helper pra saber se o usuário é "O Cara" (Admin Global)
     * Centraliza a lógica do Spatie aqui.
     */
    private function isSuperAdmin($user)
    {
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    /**
     * INDEX: Listagem de funcionários da loja
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        if (!$user) return redirect('/login');

        // 1. CARREGAR A LISTA PARA O DROPDOWN
        // Se for Admin, carrega tudo. Se não, só as que ele tem acesso.
        if ($this->isSuperAdmin($user)) {
            $allLojas = Loja::orderBy('nome')->get();
        } else {
            $allLojas = Loja::where('user_id', $user->id)
                ->orWhereHas('permissoes', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orderBy('nome')
                ->get();
        }

        // 2. SELEÇÃO DA LOJA (Lógica do Filtro)
        // Se veio na URL, usa. Se não, pega a primeira da lista pra não mostrar tela em branco.
        $codigoAlvo = $request->get('loja_codigo') ?? ($allLojas->first() ? $allLojas->first()->codigo : null);

        // Se o cara não tem loja nenhuma, retorna vazio e segura o erro na view
        if (!$codigoAlvo) {
            return view('funcionarios.index', [
                'lojas' => collect([]), 
                'allLojas' => collect([]), 
                'codigoSelecionado' => null
            ]);
        }

        // 3. SEGURANÇA (A Trava Principal)
        // Verifica se a loja que ele quer ver está na lista de lojas permitidas dele ($allLojas)
        // O firstWhere aqui é ótimo porque ele busca na coleção que já carregamos, sem ir no banco de novo.
        $lojaPermitida = $allLojas->firstWhere('codigo', $codigoAlvo);

        if (!$lojaPermitida) {
            return redirect()->route('dashboard')
                ->with('error', 'Ops! Você não tem permissão para acessar essa loja.');
        }

        // 4. CARREGAR DADOS COMPLETOS
        // Agora sim, buscamos os relacionamentos (funcionários) apenas dessa loja específica
        $lojaExibicao = Loja::where('id', $lojaPermitida->id)
                            ->with(['permissoes.user', 'user'])
                            ->first();

        return view('funcionarios.index', [
            'lojas' => collect([$lojaExibicao]), // Mando como coleção pro foreach da view não quebrar
            'allLojas' => $allLojas,
            'codigoSelecionado' => $codigoAlvo
        ]);
    }

    // Redirecionamento simples se alguém acessar a URL antiga
    public function lojaFuncionarios($lojaCodigo)
    {
        return redirect()->route('funcionarios.index', ['loja_codigo' => $lojaCodigo]);
    }

    /**
     * STORE: Adicionar Funcionário
     */
    public function store(Request $request, $lojaCodigo)
    {
        // Busca a loja pelo código (Segurança: não usamos ID na URL)
        $loja = Loja::where('codigo', $lojaCodigo)->first();
        
        if (!$loja) {
            return redirect()->back()->with('error', 'Loja não encontrada.');
        }

        $user = auth()->user();

        // TRAVA DE SEGURANÇA:
        // Só passa se for o ADMIN ou o DONO da loja.
        if (!$this->isSuperAdmin($user) && $loja->user_id != $user->id) {
            return redirect()->back()->with('error', 'Sem chance. Apenas o dono ou Admin adiciona gente na equipe.');
        }

        // Validação rigorosa
        $request->validate([
            'email' => 'required|email|exists:users,email',
            // Aqui eu travei os cargos pra ninguém injetar string aleatória
            'role' => 'required|in:Vendedor,Gerente,Estoquista,Caixa,Contador' 
        ], [
            'email.exists' => 'Esse e-mail não está cadastrado no sistema.',
            'role.in' => 'Cargo inválido selecionado.'
        ]);

        $userAlvo = User::where('email', $request->email)->first();

        // Validação de Negócio: Não duplicar funcionário
        if (LojaPermissao::where('loja_id', $loja->id)->where('user_id', $userAlvo->id)->exists()) {
            return redirect()->back()->with('error', 'Esse usuário já faz parte da equipe.');
        }

        // Validação de Negócio: Não adicionar o dono como funcionário
        if ($loja->user_id == $userAlvo->id) {
            return redirect()->back()->with('error', 'O dono já tem acesso total, não precisa adicionar.');
        }

        // Tudo certo? Cria!
        LojaPermissao::create([
            'loja_id' => $loja->id,
            'user_id' => $userAlvo->id,
            'role' => $request->role
        ]);

        return redirect()->back()->with('status', 'Funcionário adicionado com sucesso!');
    }

    /**
     * UPDATE: Atualizar Cargo
     */
    public function updatePermissao(Request $request, $lojaCodigo, $permissaoId)
    {
        // Carrega a permissão já trazendo a loja junto pra checar integridade
        $per = LojaPermissao::with('loja')->find($permissaoId);

        // Segurança de Integridade:
        // Verifica se a permissão que estamos editando REALMENTE pertence à loja do código na URL.
        if (!$per || $per->loja->codigo !== $lojaCodigo) {
            return redirect()->back()->with('error', 'Erro de integridade. Essa permissão não pertence a essa loja.');
        }

        $user = auth()->user();

        // TRAVA DE SEGURANÇA (Admin ou Dono)
        if (!$this->isSuperAdmin($user) && $per->loja->user_id != $user->id) {
            return redirect()->back()->with('error', 'Você não tem permissão para alterar cargos.');
        }

        $per->role = $request->input('role');
        $per->save();

        return redirect()->back()->with('status', 'Cargo atualizado com sucesso!');
    }

    /**
     * DESTROY: Remover Funcionário
     */
    public function destroyPermissao(Request $request, $lojaCodigo, $permissaoId)
    {
        $per = LojaPermissao::with('loja')->find($permissaoId);

        // Segurança de Integridade
        if (!$per || $per->loja->codigo !== $lojaCodigo) {
            return redirect()->back()->with('error', 'Erro ao localizar o funcionário.');
        }

        $user = auth()->user();

        // TRAVA DE SEGURANÇA (Admin ou Dono)
        if (!$this->isSuperAdmin($user) && $per->loja->user_id != $user->id) {
            return redirect()->back()->with('error', 'Você não tem permissão para remover funcionários.');
        }

        $per->delete();
        
        return redirect()->back()->with('status', 'Funcionário removido da equipe!');
    }
}