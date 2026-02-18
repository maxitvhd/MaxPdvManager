<?php

namespace App\Http\Controllers;

use App\Models\Loja;
use App\Models\User;
use App\Models\LojaPermissao;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class LojasController extends Controller
{
    /**
     * Helper pra saber se o cara é o Chefe Supremo (Admin)
     */
    private function isSuperAdmin($user)
    {
        // Verifica na tabela 'model_has_roles' se ele tem a role 'admin'
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    // ------------------------------------------------------------------------
    // LISTAR LOJAS (INDEX)
    // ------------------------------------------------------------------------
    public function index()
    {
        $user = Auth::user();

        // Se for o Admin, libera a visão de tudo (Modo Deus)
        if ($this->isSuperAdmin($user)) {
            $lojas = Loja::all();
        } else {
            // Se for mortal, só vê as lojas dele ou onde ele trabalha
            $lojas = Loja::where('user_id', $user->id) 
                ->orWhereHas('permissoes', function ($query) use ($user) {
                    $query->where('user_id', $user->id);
                })
                ->get();
        }

        // Removi o dd() que tava travando a tela, agora vai carregar a view direto
        return view('lojas.index', compact('lojas'));
    }

    public function create()
    {
        return view('lojas.create');
    }

    // ------------------------------------------------------------------------
    // SALVAR NOVA LOJA (STORE)
    // ------------------------------------------------------------------------
    public function store(Request $request)
    {
        // Validação pra não deixar passar lixo
        $request->validate([
            'nome'      => 'required|string|max:255',
            'cnpj'      => 'required|string|max:20', 
            'email'     => 'required|email',
            'telefone'  => 'required|string',
            'endereco'  => 'required|string',
            'bairro'    => 'required|string',
            'cidade'    => 'required|string',
            'estado'    => 'required|string|max:2',
            'cep'       => 'required|string',
            'status'    => 'required', // ativo/inativo
        ]);

        $data = $request->all();
        
        // Gerando aquele código aleatório pra ficar seguro e não usar ID sequencial
        $data['codigo'] = Str::random(8); 
        $data['user_id'] = Auth::id();

        Loja::create($data);

        return redirect()->route('lojas.index')->with('success', 'Loja criada com sucesso!');
    }

    // ------------------------------------------------------------------------
    // EDITAR LOJA (EDIT) - Agora buscando pelo CÓDIGO
    // ------------------------------------------------------------------------
    public function edit($codigo)
    {
        // Busca pelo código pra ninguém ficar chutando ID na URL
        $loja = Loja::where('codigo', $codigo)->firstOrFail();
        $user = Auth::user();
    
        // TRAVA DE SEGURANÇA: Só mexe aqui se for Admin ou o Dono
        if (!$this->isSuperAdmin($user) && $loja->user_id !== $user->id) {
            return redirect()->route('lojas.index')
                ->with('error', 'Sem chance! Só o dono ou admin pode editar essa loja.');
        }

        return view('lojas.edit', compact('loja'));
    }

    // ------------------------------------------------------------------------
    // ATUALIZAR DADOS (UPDATE) - Pelo CÓDIGO
    // ------------------------------------------------------------------------
    public function update(Request $request, $codigo)
    {
        $loja = Loja::where('codigo', $codigo)->firstOrFail();
        $user = Auth::user();
    
        // Conferindo de novo se o cara tem permissão
        if (!$this->isSuperAdmin($user) && $loja->user_id !== $user->id) {
            return redirect()->route('lojas.index')
                ->with('error', 'Você não tem permissão para alterar essa loja.');
        }
    
        $request->validate([
            'nome' => 'required',
            'cnpj' => 'required',
            'email' => 'required|email',
            'status' => 'required', 
        ]);
    
        $loja->update($request->all());
    
        return redirect()->route('lojas.index')->with('success', 'Loja atualizada com sucesso!');
    }

    // ------------------------------------------------------------------------
    // EXCLUIR LOJA (DESTROY) - Pelo CÓDIGO
    // ------------------------------------------------------------------------
    public function destroy($codigo)
    {
        $loja = Loja::where('codigo', $codigo)->firstOrFail();
        $user = Auth::user();

        // Segurança máxima aqui, deletar é coisa séria
        if (!$this->isSuperAdmin($user) && $loja->user_id !== $user->id) {
            return redirect()->route('lojas.index')
                ->with('error', 'Ops! Você não pode excluir essa loja.');
        }

        $loja->delete();
        
        return redirect()->route('lojas.index')->with('success', 'Loja excluída permanentemente.');
    }

   
}