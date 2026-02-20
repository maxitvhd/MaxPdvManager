<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\Loja;
use App\Models\Produto;
use App\Models\LojaVendaItem;
use App\Models\MaxDivulgaCampaign;
use App\Models\MaxDivulgaTheme;

class MaxDivulgaController extends Controller
{
    /** Detecta a loja do usuário logado.
     * Admin pode ver todas ou a loja passada por parâmetro.
     */
    private function resolverLoja(?int $lojaId = null): ?\App\Models\Loja
    {
        $user = auth()->user();

        // Admin / Super-Admin: usa qualquer loja
        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            if ($lojaId) {
                return Loja::find($lojaId);
            }
            // Se não especificou, retorna a primeira disponível
            return Loja::first();
        }

        // Lojista normal: tenta pela sessão e depois pela FK user_id
        $lojaCodigo = session('loja_codigo');
        if ($lojaCodigo) {
            $loja = Loja::where('codigo', $lojaCodigo)->first();
            if ($loja)
                return $loja;
        }
        return Loja::where('user_id', $user->id)->first();
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            $campaigns = MaxDivulgaCampaign::orderBy('created_at', 'desc')->get();
        } else {
            $loja = $this->resolverLoja();
            $lojaId = $loja ? $loja->id : null;
            $campaigns = MaxDivulgaCampaign::where('loja_id', $lojaId)
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('lojista.maxdivulga.index', compact('campaigns'));
    }

    public function create()
    {
        $themes = MaxDivulgaTheme::where('is_active', true)->get();
        $loja = $this->resolverLoja();
        return view('lojista.maxdivulga.create', compact('themes', 'loja'));
    }

    public function store(Request $request)
    {
        $loja = $this->resolverLoja($request->loja_id);

        if (!$loja) {
            return back()->withErrors(['loja' => 'Nenhuma loja encontrada para gerar campanha.']);
        }

        $campaign = MaxDivulgaCampaign::create([
            'tenant_id' => auth()->id() ?? null,
            'loja_id' => $loja->id,
            'name' => $request->name ?? 'Campanha ' . now()->format('d/m/Y'),
            'type' => $request->type,
            'channels' => $request->channels,
            'schedule_type' => $request->schedule_type ?? 'unique',
            'product_selection_rule' => $request->product_selection_rule,
            'discount_rules' => $request->discount_rules,
            'theme_id' => $request->theme_id,
            'persona' => $request->persona,
            'format' => $request->format,
            'status' => 'active',
        ]);

        Log::info("[MAXDIVULGA-01] Campanha #{$campaign->id} '{$campaign->name}' criada para loja: {$loja->nome} ({$loja->codigo})");

        $selectedIds = $request->input('selected_products', []);
        $ruleType = $request->input('product_selection_rule.type');

        $produtos = collect();

        if ($ruleType === 'manual' && count($selectedIds) > 0) {
            Log::info("[MAXDIVULGA-02] Modo MANUAL. Qtde selecionados: " . count($selectedIds));
            $produtos = Produto::whereIn('id', $selectedIds)->get();

        } elseif ($ruleType === 'best_sellers') {
            Log::info("[MAXDIVULGA-02] Modo MAIS VENDIDOS (igual ao dashboard).");
            // Mesmo método/JOIN que o dashboard usa para rankear
            $topIds = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                ->join('produtos', 'loja_vendas_itens.produto_id', '=', 'produtos.id')
                ->where('loja_vendas.loja_id', $loja->id)
                ->select(
                    'loja_vendas_itens.produto_id',
                    DB::raw('SUM(loja_vendas_itens.quantidade) as total_vendido')
                )
                ->groupBy('loja_vendas_itens.produto_id')
                ->orderByDesc('total_vendido')
                ->limit(10)
                ->get()
                ->pluck('produto_id');

            $produtos = Produto::whereIn('id', $topIds)->get();

        } elseif ($ruleType === 'category') {
            $cat = $request->input('product_selection_rule.value', '');
            Log::info("[MAXDIVULGA-02] Modo CATEGORIA: {$cat}");
            $produtos = Produto::where('loja_id', $loja->id)
                ->where('categoria', 'like', "%{$cat}%")
                ->limit(10)
                ->get();
        }

        Log::info("[MAXDIVULGA-03] Total de produtos para o catálogo: " . $produtos->count());

        $descontoPct = floatval($request->input('discount_rules.percentage', 0));
        $produtosParaCatalogo = [];

        foreach ($produtos as $prod) {
            $precoOriginal = floatval($prod->preco);
            $precoNovo = $descontoPct > 0
                ? $precoOriginal - ($precoOriginal * ($descontoPct / 100))
                : $precoOriginal;

            $produtosParaCatalogo[] = [
                'nome' => $prod->nome,
                'preco_original' => number_format($precoOriginal, 2, ',', '.'),
                'preco_novo' => number_format($precoNovo, 2, ',', '.'),
            ];
        }

        // Dados da loja para os templates
        $dadosLoja = [
            'nome' => $loja->nome ?? '',
            'telefone' => $loja->telefone ?? '',
            'endereco' => trim(($loja->endereco ?? '') . ', ' . ($loja->bairro ?? '')),
            'cidade' => ($loja->cidade ?? '') . '/' . ($loja->estado ?? ''),
            'cep' => $loja->cep ?? '',
            'cnpj' => $loja->cnpj ?? '',
            'codigo' => $loja->codigo ?? '',
        ];

        Log::info("[MAXDIVULGA-04] Acionando IA para gerar 2 versões de copy...");
        $aiService = new \App\Services\AiCopyWriterService();
        $copyPrincipal = $aiService->generateCopy($produtosParaCatalogo, $campaign->persona);
        $copyAcompanhamento = $aiService->generateCopySocial($produtosParaCatalogo, $campaign->persona, $dadosLoja);
        $campaign->update([
            'copy' => $copyPrincipal,
            'copy_acompanhamento' => $copyAcompanhamento,
        ]);

        if (in_array($campaign->format, ['image', 'pdf'])) {
            Log::info("[MAXDIVULGA-06] Iniciando Renderização (CatalogRendererService)");
            $renderService = new \App\Services\CatalogRendererService();
            // Passa também os dados da loja
            $filePath = $renderService->render($campaign, $produtosParaCatalogo, $dadosLoja);
            if ($filePath) {
                $campaign->update(['file_path' => $filePath]);
                Log::info("[MAXDIVULGA-09] Sucesso! Arquivo: {$filePath}");
            } else {
                Log::error("[MAXDIVULGA-09] FALHA na renderização! Verifique os logs do CatalogRendererService.");
            }
        }

        return redirect()->route('lojista.maxdivulga.index')
            ->with('success', '✅ Campanha criada! A IA gerou sua arte e o texto de acompanhamento. Clique em "Ver" para conferir.');
    }

    public function show(MaxDivulgaCampaign $campaign)
    {
        return view('lojista.maxdivulga.show', compact('campaign'));
    }

    public function edit(MaxDivulgaCampaign $campaign)
    {
        $themes = MaxDivulgaTheme::where('is_active', true)->get();
        return view('lojista.maxdivulga.edit', compact('campaign', 'themes'));
    }

    public function update(Request $request, MaxDivulgaCampaign $campaign)
    {
        $campaign->update([
            'name' => $request->name,
            'status' => $request->status ?? 'active',
        ]);
        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Campanha atualizada!');
    }

    public function destroy(MaxDivulgaCampaign $campaign)
    {
        if ($campaign->file_path && file_exists(storage_path('app/public/' . str_replace('storage/', '', $campaign->file_path)))) {
            @unlink(storage_path('app/public/' . str_replace('storage/', '', $campaign->file_path)));
        }
        $campaign->delete();
        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Campanha removida!');
    }

    public function download(MaxDivulgaCampaign $campaign)
    {
        if ($campaign->format == 'text') {
            return response()->json([
                'titulo' => $campaign->name,
                'copy_principal' => $campaign->copy,
                'copy_acompanhamento' => $campaign->copy_acompanhamento,
            ]);
        }

        $caminho_real = storage_path('app/public/' . str_replace('storage/', '', $campaign->file_path ?? ''));
        if ($campaign->file_path && file_exists($caminho_real)) {
            return response()->download($caminho_real);
        }

        return back()->with('error', 'O arquivo não foi encontrado. Verifique se a campanha foi processada.');
    }

    public function apiProducts(Request $request)
    {
        $loja = $this->resolverLoja($request->get('loja_id'));

        if (!$loja) {
            return response()->json(['error' => 'Nenhuma loja encontrada.'], 404);
        }

        $rule = $request->get('rule', 'search');
        $search = $request->get('search', '');
        $limit = $request->get('limit', 10);

        if ($rule === 'best_sellers') {
            $topIds = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                ->join('produtos', 'loja_vendas_itens.produto_id', '=', 'produtos.id')
                ->where('loja_vendas.loja_id', $loja->id)
                ->select(
                    'loja_vendas_itens.produto_id',
                    DB::raw('SUM(loja_vendas_itens.quantidade) as total_vendido')
                )
                ->groupBy('loja_vendas_itens.produto_id')
                ->orderByDesc('total_vendido')
                ->limit($limit)
                ->get()
                ->pluck('produto_id');

            return response()->json(Produto::whereIn('id', $topIds)->get());
        }

        if ($rule === 'category') {
            return response()->json(
                Produto::where('loja_id', $loja->id)
                    ->where('categoria', 'like', "%{$search}%")
                    ->limit($limit)
                    ->get()
            );
        }

        // Busca manual (padrão)
        return response()->json(
            Produto::where('loja_id', $loja->id)
                ->where(function ($q) use ($search) {
                    $q->where('nome', 'like', "%{$search}%")
                        ->orWhere('codigo_barra', 'like', "%{$search}%");
                })
                ->limit(10)
                ->get()
        );
    }
}
