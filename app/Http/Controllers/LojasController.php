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

        try {
            \Illuminate\Support\Facades\DB::beginTransaction();

            // 1. Transferir clientes para a tabela de leads
            $clientes = \App\Models\Cliente::where('loja_id', $loja->id)->get();
            
            $leadsData = [];
            foreach ($clientes as $cliente) {
                $leadsData[] = [
                    'antiga_loja_id' => $loja->id,
                    'nome' => $cliente->nome,
                    'email' => $cliente->email,
                    'telefone' => $cliente->telefone,
                    'codigo_original' => $cliente->codigo,
                    'dados_completos' => json_encode($cliente->toArray()), // Guarda tudo como JSON
                    'motivo_transferencia' => 'Exclusão de Loja',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            // Inserir os leads em lote
            if (!empty($leadsData)) {
                \Illuminate\Support\Facades\DB::table('clientes_leads')->insert($leadsData);
            }

            // 2. Limpeza Manual em Cascata para evitar "Constraint Violation" do MariaDB
            
            // 2A. Clientes e Financeiro do PDV
            \Illuminate\Support\Facades\DB::table('clientes_transacoes')->where('loja_id', $loja->id)->delete();
            \App\Models\Cliente::where('loja_id', $loja->id)->delete();

            // 2B. Registros de Caixa e Vendas (PDV)
            \Illuminate\Support\Facades\DB::table('loja_vendas_itens')->whereIn('loja_venda_id', function ($query) use ($loja) {
                $query->select('id')->from('loja_vendas')->where('loja_id', $loja->id);
            })->delete();
            \Illuminate\Support\Facades\DB::table('loja_vendas')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('loja_caixa_sessoes')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('loja_movimentacoes')->where('loja_id', $loja->id)->delete();
            
            // 2C. Sistema, Lojas e TVDoor
            \Illuminate\Support\Facades\DB::table('sistema_transacoes')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('social_accounts')->where('loja_id', $loja->id)->delete();
            
            \Illuminate\Support\Facades\DB::table('tv_door_categories')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('tv_door_layouts')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('tv_door_media')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('tv_door_schedules')->whereIn('player_id', function ($query) use ($loja) {
                $query->select('id')->from('tv_door_players')->where('loja_id', $loja->id);
            })->delete();
            \Illuminate\Support\Facades\DB::table('tv_door_players')->where('loja_id', $loja->id)->delete();
            
            // 2D. Produtos e Checkouts
            \Illuminate\Support\Facades\DB::table('produto_lotes')->whereIn('produto_id', function ($query) use ($loja) {
                $query->select('id')->from('produtos')->where('loja_id', $loja->id);
            })->delete();
            \Illuminate\Support\Facades\DB::table('produtos')->where('loja_id', $loja->id)->delete();
            
            \Illuminate\Support\Facades\DB::table('loja_permissao')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('loja_cancelamento_key')->where('loja_id', $loja->id)->delete();
            \Illuminate\Support\Facades\DB::table('loja_cancelamento')->where('loja_id', $loja->id)->delete();
            
            \Illuminate\Support\Facades\DB::table('loja_checkout')->whereIn('licenca_id', function ($query) use ($loja) {
                $query->select('id')->from('loja_licencas')->where('loja_id', $loja->id);
            })->delete();
            \Illuminate\Support\Facades\DB::table('loja_licencas')->where('loja_id', $loja->id)->delete();

            // 3. Pode deletar a loja finalmente
            $loja->delete();

            \Illuminate\Support\Facades\DB::commit();

            return redirect()->route('lojas.index')->with('success', 'Loja excluída permanentemente e clientes foram arquivados com sucesso.');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            \Illuminate\Support\Facades\Log::error('Erro ao excluir loja: ' . $e->getMessage());
            return redirect()->route('lojas.index')->with('error', 'Erro ao excluir a loja: ' . $e->getMessage());
        }
    }

   
}