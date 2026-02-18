<?php

namespace App\Http\Controllers;

use App\Models\Produto;
use App\Models\ProdutoFull;
use App\Models\ProdutoLote;
use App\Models\LojaPermissao;
use App\Models\Loja;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use ZipArchive;
use Illuminate\Support\Str;

class ProdutoController extends Controller
{
    /**
     * Helper para verificar se é Admin Global
     */
    private function isSuperAdmin($user)
    {
        return $user->hasRole('admin') || $user->hasRole('super-admin');
    }

    /**
     * Retorna os IDs das lojas que o usuário pode acessar.
     * Se for Admin, retorna TODAS.
     * Se for Mortal, retorna as dele (dono) + as que tem permissão.
     */
    private function getLojasPermitidas()
    {
        $user = Auth::user();

        // 1. MODO DEUS (Admin vê tudo)
        if ($this->isSuperAdmin($user)) {
            return Loja::pluck('id')->toArray();
        }

        // 2. MODO MORTAL (Dono + Funcionário)
        
        // Lojas onde ele é o DONO (criador)
        $lojasDono = Loja::where('user_id', $user->id)->pluck('id')->toArray();

        // Lojas onde ele é FUNCIONÁRIO (tem permissão)
        $lojasFuncionario = LojaPermissao::where('user_id', $user->id)
            ->whereIn('role', ['dono', 'funcionario', 'caixa', 'gerente']) // Adicionei gerente por precaução
            ->pluck('loja_id')
            ->toArray();

        // Une os dois arrays e remove duplicatas
        return array_unique(array_merge($lojasDono, $lojasFuncionario));
    }

    public function create()
    {
        return view('produtos.create');
    }

    public function store(Request $request)
    {
        // --- 1. VALIDAÇÃO E BUSCA DA LOJA ---
        $idsPermitidos = $this->getLojasPermitidas();
        $idLojaRequest = $request->loja_id;

        // Se não enviou ID, mas o usuário só tem acesso a 1 loja, usamos ela automaticamente
        if (!$idLojaRequest && count($idsPermitidos) === 1) {
            $idLojaRequest = $idsPermitidos[0];
        }

        // Verifica se o ID (do request ou automático) é válido para este usuário
        if (!$idLojaRequest || !in_array($idLojaRequest, $idsPermitidos)) {
            return redirect()->back()->with('error', 'Loja inválida ou sem permissão para cadastrar produtos nela.');
        }

        // Busca o Objeto Loja
        $loja = Loja::find($idLojaRequest);
        // ------------------------------------

        // 2. Define o nome da imagem padrão (CODIGOBARRA.jpg)
        $nomeImagem = $request->codigo_barra . '.jpg';
        
        // 3. Busca ou Cria o ProdutoFull (Base Global)
        $produtoFull = ProdutoFull::where('codigo_barra', $request->codigo_barra)->first();

        if (!$produtoFull) {
            $produtoFull = new ProdutoFull();
            $produtoFull->codigo_barra = $request->codigo_barra;
        }

        // Atualiza dados básicos do ProdutoFull
        $produtoFull->fill([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'categoria' => $request->categoria,
            'peso' => $request->peso,
            'tamanho' => $request->tamanho,
            'descricao_ingredientes' => $request->descricao_ingredientes,
            'ingredientes' => $request->ingredientes,
            'embalagem' => $request->embalagem,
            'fabricante' => $request->fabricante,
            'marca' => $request->marca
        ]);

        // ============================================================
        // 4. LÓGICA DE IMAGEM
        // ============================================================
        
        if ($request->hasFile('imagem_personalizada')) {
            $request->file('imagem_personalizada')->storeAs('public/produtos_full', $nomeImagem);
            $produtoFull->imagem = $nomeImagem;
        } 
        else {
            $urlParaBaixar = null;

            if ($request->url_image && filter_var($request->url_image, FILTER_VALIDATE_URL)) {
                $urlParaBaixar = $request->url_image;
            } 
            elseif ($produtoFull->imagem && filter_var($produtoFull->imagem, FILTER_VALIDATE_URL)) {
                $urlParaBaixar = $produtoFull->imagem;
            }

            if ($urlParaBaixar) {
                try {
                    $conteudo = file_get_contents($urlParaBaixar);
                    if ($conteudo) {
                        Storage::put('public/produtos_full/' . $nomeImagem, $conteudo);
                        $produtoFull->imagem = $nomeImagem;
                    }
                } catch (\Exception $e) {
                    // Ignora erro de download
                }
            }
        }
        
        $produtoFull->save();

        // ============================================================
        // 5. Cria o Produto da Loja (Cliente)
        
        $produtoCliente = Produto::where('codigo_barra', $request->codigo_barra)
                        ->where('loja_id', $loja->id)
                        ->first();

        if ($produtoCliente) {
            return redirect()->back()->with('error', 'Você já possui este produto cadastrado nesta loja.');
        }

        $produto = Produto::create([
            'produto_full_id' => $produtoFull->id,
            'user_id' => Auth::id(),
            'loja_id' => $loja->id,
            'codigo_barra' => $request->codigo_barra,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'preco' => $request->preco,
            'preco_compra' => $request->preco_compra,
            'categoria' => $request->categoria,
            'estoque' => 0,
            'custo_medio' => $request->custo_medio ?? 0.00,
            'margem' => $request->margem ?? 0.00,
            'comissao' => $request->comissao ?? 0.00,
            'codigo_fiscal' => $request->codigo_fiscal,
            'habilitar_venda_atacado' => $request->has('habilitar_venda_atacado') ? 1 : 0,
            'quantidade_atacado' => $request->quantidade_atacado ?? 1,
            'preco_atacado' => $request->preco_atacado ?? 0.00,
            'estoque_minimo' => $request->estoque_minimo,
            'estoque_maximo' => $request->estoque_maximo,
            'controlar_estoque' => $request->has('controlar_estoque') ? 1 : 0,
            'balanca_checkout' => $request->has('balanca_checkout') ? 1 : 0,
            'custo_ultima_compra' => $request->custo_ultima_compra,
            'margem_ultima_compra' => $request->margem_ultima_compra
        ]);

        // 6. COPIA A IMAGEM (Do Global para a Loja Local)
        if ($produtoFull->imagem && Storage::exists('public/produtos_full/' . $produtoFull->imagem)) {
            $caminhoOrigem = 'public/produtos_full/' . $produtoFull->imagem;
            $caminhoDestino = 'public/lojas/' . $loja->codigo . '/produtos/' . $produtoFull->imagem;
            
            if (Storage::exists($caminhoDestino)) {
                Storage::delete($caminhoDestino);
            }
            
            $diretorio = dirname($caminhoDestino);
            if(!Storage::exists($diretorio)) { Storage::makeDirectory($diretorio); }

            Storage::copy($caminhoOrigem, $caminhoDestino);
            
            $produto->imagem = $produtoFull->imagem;
            $produto->save();
        }

        // 7. Salva os Lotes
        if ($request->has('lotes')) {
            foreach ($request->lotes as $loteData) {
                if (!empty($loteData['lote'])) {
                    ProdutoLote::create([
                        'produto_id' => $produto->id,
                        'user_id' => Auth::id(),
                        'lote' => $loteData['lote'],
                        'quantidade' => $loteData['quantidade'] ?? 0,
                        'validade' => $loteData['validade'] ?? null,
                        'data_fabricacao' => $loteData['data_fabricacao'] ?? null,
                    ]);
                    
                    $produto->increment('estoque', $loteData['quantidade'] ?? 0);
                }
            }
        }

        return redirect()->route('produtos.index')->with('success', 'Produto cadastrado com sucesso!');
    }

    public function buscarProdutoPorCodigo(Request $request)
    {
        $codigoBarras = $request->codigo_barras;
        $produtoFull = ProdutoFull::where('codigo_barra', $codigoBarras)->first();

        if ($produtoFull) {
            $imagemRetorno = '';
            if ($produtoFull->imagem) {
                if (filter_var($produtoFull->imagem, FILTER_VALIDATE_URL)) {
                    $imagemRetorno = $produtoFull->imagem;
                } else {
                    $imagemRetorno = asset('storage/produtos_full/' . $produtoFull->imagem);
                }
            }

            return response()->json([
                'nome' => $produtoFull->nome,
                'descricao' => $produtoFull->descricao,
                'categoria' => $produtoFull->categoria,
                'peso' => $produtoFull->peso,
                'tamanho' => $produtoFull->tamanho,
                'imagem' => $imagemRetorno, 
                'descricao_ingredientes' => $produtoFull->descricao_ingredientes,
                'ingredientes' => $produtoFull->ingredientes,
                'embalagem' => $produtoFull->embalagem,
                'fabricante' => $produtoFull->fabricante,
                'marca' => $produtoFull->marca
            ]);
        }
        return response()->json(['erro' => 'Produto não encontrado em nossa base de dados.'], 202);
    }

public function index(Request $request)
{
    // 1. Obtém os IDs das lojas permitidas
    // (A função getLojasPermitidas já verifica se é Admin ou Dono)
    $lojasPermitidas = $this->getLojasPermitidas();

    // --- TRAVA DE SEGURANÇA (NO INÍCIO) ---
    // Se a lista veio vazia, o usuário não tem permissão em lugar nenhum.
    if (empty($lojasPermitidas)) {
        return redirect()->route('dashboard')
            ->with('error', 'Acesso Negado: Você não possui permissão para gerenciar produtos.');
    }

    // 2. Monta a Query filtrando por essas lojas
    $query = Produto::with(['produtoFull', 'lotes'])
        ->whereIn('loja_id', $lojasPermitidas);

    // Busca
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('nome', 'like', "%{$search}%")
              ->orWhere('codigo_barra', 'like', "%{$search}%");
        });
    }

    // Ordenação (opcional, mas recomendado)
    $query->orderBy('created_at', 'desc');

    // Paginação
    $produtos = $query->paginate(10);
   
    // Processamento de estoque e validade
    foreach ($produtos as $produto) {
        $quantidadeTotal = 0;
        $validadeStatus = 'gray';
        $dataValidadeMaisProxima = null;

        foreach ($produto->lotes as $lote) {
            $quantidadeTotal += $lote->quantidade;
            if ($lote->validade) {
                $validade = \Carbon\Carbon::parse($lote->validade);
                if (!$dataValidadeMaisProxima || $validade->isBefore($dataValidadeMaisProxima)) {
                    $dataValidadeMaisProxima = $validade;
                }
            }
        }

        if ($dataValidadeMaisProxima) {
            $hoje = now();
            $dias = $hoje->diffInDays($dataValidadeMaisProxima, false);
            $anos = $hoje->diffInYears($dataValidadeMaisProxima, false);

            if ($dias < 0) { $validadeStatus = 'red'; } 
            elseif ($dias <= 20) { $validadeStatus = 'orange'; } 
            elseif ($anos >= 1) { $validadeStatus = 'blue'; } 
            else { $validadeStatus = 'green'; }
        }

        $produto->estoque = $quantidadeTotal;
        $produto->validadeStatus = $validadeStatus;
        $produto->dataValidadeMaisProxima = $dataValidadeMaisProxima;
    }

    // Retorna a view normalmente se passou pela trava de segurança lá em cima
    return view('produtos.index', compact('produtos'));
}
    public function show($id)
    {
        $lojasPermitidas = $this->getLojasPermitidas();

        $produto = Produto::with(['produtoFull', 'lotes'])
            ->where('id', $id)
            ->whereIn('loja_id', $lojasPermitidas)
            ->firstOrFail();
            
        return view('produtos.show', compact('produto'));
    }

    public function edit($id)
    {
        $lojasPermitidas = $this->getLojasPermitidas();

        $produto = Produto::with(['produtoFull', 'lotes'])
            ->where('id', $id)
            ->whereIn('loja_id', $lojasPermitidas)
            ->firstOrFail();

        return view('produtos.edit', compact('produto'));
    }

    public function update(Request $request, $id)
    {
        $lojasPermitidas = $this->getLojasPermitidas();

        // Busca e valida permissão na query
        $produto = Produto::where('id', $id)
            ->whereIn('loja_id', $lojasPermitidas)
            ->firstOrFail();

        $lojaDoProduto = Loja::find($produto->loja_id);

        $produto->update([
            'preco_compra' => $request->preco_compra,
            'preco' => $request->preco,
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'estoque' => $request->estoque,
            'categoria' => $request->categoria,
            'custo_medio' => $request->custo_medio ?? 0.01,
            'margem' => $request->margem ?? 0.00,
            'comissao' => $request->comissao ?? 0.00,
            'habilitar_venda_atacado' => $request->has('habilitar_venda_atacado') ? 1 : 0,
            'quantidade_atacado' => $request->quantidade_atacado ?? 1,
            'preco_atacado' => $request->preco_atacado ?? 0.01,
            'codigo_fiscal' => $request->codigo_fiscal,
            'estoque_minimo' => $request->estoque_minimo,
            'estoque_maximo' => $request->estoque_maximo,
            'controlar_estoque' => $request->has('controlar_estoque') ? 1 : 0,
            'balanca_checkout' => $request->has('balanca_checkout') ? 1 : 0,
            'custo_ultima_compra' => $request->custo_ultima_compra,
            'margem_ultima_compra' => $request->margem_ultima_compra
        ]);

        // --- ATUALIZAÇÃO DA IMAGEM ---
        if ($request->hasFile('imagem_personalizada') && $lojaDoProduto) {
            $diretorioBase = 'public/lojas/' . $lojaDoProduto->codigo . '/produtos';

            if ($produto->imagem) {
                $caminhoImagemAntiga = $diretorioBase . '/' . $produto->imagem;
                if (Storage::exists($caminhoImagemAntiga)) {
                    Storage::delete($caminhoImagemAntiga);
                }
            }

            $nomeImagem = $produto->codigo_barra . '.jpg';
            $request->imagem_personalizada->storeAs($diretorioBase, $nomeImagem);

            $produto->imagem = $nomeImagem;
            $produto->save();
        }

        // Lotes
        $loteIds = []; 
        if ($request->has('lotes')) {
            foreach ($request->lotes as $loteData) {
                if (!empty($loteData['id'])) {
                    ProdutoLote::where('id', $loteData['id'])
                        ->where('produto_id', $produto->id)
                        ->update([
                            'lote' => $loteData['lote'],
                            'quantidade' => $loteData['quantidade'],
                            'validade' => $loteData['validade'],
                            'data_fabricacao' => $loteData['data_fabricacao'],
                        ]);
                    $loteIds[] = $loteData['id']; 
                } else {
                    $novoLote = ProdutoLote::create([
                        'produto_id' => $produto->id,
                        'user_id' => Auth::id(),
                        'lote' => $loteData['lote'],
                        'quantidade' => $loteData['quantidade'],
                        'validade' => $loteData['validade'],
                        'data_fabricacao' => $loteData['data_fabricacao'],
                    ]);
                    $loteIds[] = $novoLote->id; 
                }
            }
        }

        ProdutoLote::where('produto_id', $produto->id)
            ->whereNotIn('id', $loteIds) 
            ->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto atualizado com sucesso!');
    }

    public function destroy($id)
    {
        $lojasPermitidas = $this->getLojasPermitidas();

        $produto = Produto::where('id', $id)
            ->whereIn('loja_id', $lojasPermitidas)
            ->first();

        if (!$produto) {
            return redirect()->route('produtos.index')->with('error', 'Produto não encontrado ou sem permissão.');
        }

        $loja = Loja::find($produto->loja_id);

        if ($produto->imagem && $loja) {
            $caminhoImagem = 'public/lojas/' . $loja->codigo . '/produtos/' . $produto->imagem;
            if (Storage::exists($caminhoImagem)) {
                Storage::delete($caminhoImagem);
            }
        }

        $produto->delete();

        return redirect()->route('produtos.index')->with('success', 'Produto excluído com sucesso.');
    }

    public function AppLeitor(Request $request)
    {
        // 1. Usa a função centralizada para pegar as lojas permitidas
        $lojasPermitidas = $this->getLojasPermitidas();

        $query = Produto::with(['produtoFull', 'lotes'])
            ->whereIn('loja_id', $lojasPermitidas);

        // Se quiser pegar dados da loja para exibir na view (opcional)
        // Pega a primeira loja permitida apenas como referência
        $loja = Loja::whereIn('id', $lojasPermitidas)->first();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nome', 'like', "%{$search}%")
                  ->orWhere('codigo_barra', 'like', "%{$search}%");
            });
        }

        $query->orderBy('created_at', 'desc');
        $produtos = $query->paginate(10);

        foreach ($produtos as $produto) {
            $quantidadeTotal = 0;
            $validadeStatus = 'gray';
            $dataValidadeMaisProxima = null;

            foreach ($produto->lotes as $lote) {
                $quantidadeTotal += $lote->quantidade;
                if ($lote->validade) {
                    $validade = \Carbon\Carbon::parse($lote->validade);
                    if (!$dataValidadeMaisProxima || $validade->isBefore($dataValidadeMaisProxima)) {
                        $dataValidadeMaisProxima = $validade;
                    }
                }
            }

            if ($dataValidadeMaisProxima) {
                $hoje = now();
                $dias = $hoje->diffInDays($dataValidadeMaisProxima, false);
                $anos = $hoje->diffInYears($dataValidadeMaisProxima, false);

                if ($dias < 0) { $validadeStatus = 'red'; } 
                elseif ($dias <= 20) { $validadeStatus = 'orange'; } 
                elseif ($anos >= 1) { $validadeStatus = 'blue'; } 
                else { $validadeStatus = 'green'; }
            }

            $produto->estoque = $quantidadeTotal;
            $produto->validadeStatus = $validadeStatus;
            $produto->dataValidadeMaisProxima = $dataValidadeMaisProxima;
        }

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json($produtos);
        }

        return view('apps.leitor', compact('produtos', 'loja'));
    }

}