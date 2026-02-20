<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MaxDivulgaController extends Controller
{
    public function index()
    {
        $campaigns = \App\Models\MaxDivulgaCampaign::orderBy('created_at', 'desc')->get();
        return view('lojista.maxdivulga.index', compact('campaigns'));
    }

    public function create()
    {
        $themes = \App\Models\MaxDivulgaTheme::where('is_active', true)->get();
        return view('lojista.maxdivulga.create', compact('themes'));
    }

    public function store(Request $request)
    {
        $campaign = \App\Models\MaxDivulgaCampaign::create([
            'tenant_id' => auth()->id() ?? null,
            'name' => $request->name ?? 'Campanha ' . now()->format('d/m/Y'),
            'type' => $request->type,
            'channels' => $request->channels,
            'schedule_type' => $request->schedule_type ?? 'unique',
            'product_selection_rule' => $request->product_selection_rule,
            'discount_rules' => $request->discount_rules,
            'theme_id' => $request->theme_id,
            'persona' => $request->persona,
            'format' => $request->format,
        ]);

        \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-01] Iniciando criação da campanha: " . $campaign->name);

        $selectedIds = $request->input('selected_products', []);
        $ruleType = $request->input('product_selection_rule.type');

        $produtos = collect();
        $tenantId = auth()->user()->id;
        $lojaCodigo = session('loja_codigo');
        $loja = \App\Models\Loja::where('codigo', $lojaCodigo)->first() ?? \App\Models\Loja::where('user_id', $tenantId)->first();

        if ($ruleType === 'manual' && count($selectedIds) > 0) {
            \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-02] Regra manual. Buscando produtos selecionados...");
            $produtos = \App\Models\Produto::whereIn('id', $selectedIds)->get();
        } elseif ($loja) {
            if ($ruleType === 'best_sellers') {
                \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-02] Regra best_sellers Automática.");
                $top = \App\Models\LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                    ->where('loja_vendas.loja_id', $loja->id)
                    ->select('loja_vendas_itens.produto_id', \Illuminate\Support\Facades\DB::raw('SUM(loja_vendas_itens.quantidade) as qtd'))
                    ->groupBy('loja_vendas_itens.produto_id')
                    ->orderByDesc('qtd')->limit(10)->get()->pluck('produto_id');
                $produtos = \App\Models\Produto::whereIn('id', $top)->get();
            } elseif ($ruleType === 'category') {
                $cat = $request->input('product_selection_rule.value', '');
                \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-02] Regra category Automática: " . $cat);
                $produtos = \App\Models\Produto::where('loja_id', $loja->id)->where('categoria', 'like', "%{$cat}%")->limit(10)->get();
            }
        }

        \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-03] Produtos carregados na memória: " . $produtos->count());

        $descontoPct = $request->input('discount_rules.percentage', 0);
        $produtosParaCatalogo = [];
        foreach ($produtos as $prod) {
            $preco = floatval($prod->preco);
            if ($descontoPct > 0) {
                $preco = $preco - ($preco * ($descontoPct / 100));
            }
            $produtosParaCatalogo[] = [
                'nome' => $prod->nome,
                'preco_original' => number_format($prod->preco, 2, ',', '.'),
                'preco_novo' => number_format($preco, 2, ',', '.')
            ];
        }

        \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-04] Acionando IA (AiCopyWriterService)");
        $aiService = new \App\Services\AiCopyWriterService();
        $copyText = $aiService->generateCopy($produtosParaCatalogo, $campaign->persona);
        $campaign->update(['copy' => $copyText]);

        if ($campaign->format == 'image' || $campaign->format == 'pdf') {
            \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-06] Iniciando Renderização (CatalogRendererService)");
            $renderService = new \App\Services\CatalogRendererService();
            $filePath = $renderService->render($campaign, $produtosParaCatalogo);
            if ($filePath) {
                $campaign->update(['file_path' => $filePath]);
                \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-09] Campanha finalizada com sucesso. Arquivo em: " . $filePath);
            } else {
                \Illuminate\Support\Facades\Log::error("[MAXDIVULGA-09] Falha ao renderizar imagem ou PDF. O filePath retornou false.");
            }
        }

        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Campanha criada e gerada com sucesso! Clique em Baixar para ver o resultado.');
    }

    public function show(\App\Models\MaxDivulgaCampaign $campaign)
    {
        return view('lojista.maxdivulga.show', compact('campaign'));
    }

    public function edit(\App\Models\MaxDivulgaCampaign $campaign)
    {
        $themes = \App\Models\MaxDivulgaTheme::where('is_active', true)->get();
        return view('lojista.maxdivulga.edit', compact('campaign', 'themes'));
    }

    public function update(Request $request, \App\Models\MaxDivulgaCampaign $campaign)
    {
        $campaign->update([
            'name' => $request->name,
            'status' => $request->status ?? 'active',
        ]);
        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Campanha atualizada!');
    }

    public function destroy(\App\Models\MaxDivulgaCampaign $campaign)
    {
        if ($campaign->file_path && file_exists(public_path($campaign->file_path))) {
            @unlink(public_path($campaign->file_path));
        }
        $campaign->delete();
        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Campanha removida com sucesso!');
    }

    public function download(\App\Models\MaxDivulgaCampaign $campaign)
    {
        if ($campaign->format == 'text') {
            return response()->json([
                'title' => $campaign->name,
                'copy' => $campaign->copy
            ]);
        }

        $caminho_real = public_path($campaign->file_path);
        if ($campaign->file_path && file_exists($caminho_real)) {
            return response()->download($caminho_real);
        }

        return back()->with('success', 'O arquivo está sendo gerado ou ocorreu um problema na renderização.');
    }

    public function apiProducts(Request $request)
    {
        $tenantId = auth()->user()->id;
        $lojaCodigo = session('loja_codigo'); // Assumindo padrão de multi-loja do sistema

        // Pega as lojas deste admin/user
        $loja = \App\Models\Loja::where('codigo', $lojaCodigo)->first() ?? \App\Models\Loja::where('user_id', $tenantId)->first();

        if (!$loja) {
            return response()->json(['error' => 'Nenhuma loja encontrada.'], 404);
        }

        $rule = $request->get('rule', 'search');
        $search = $request->get('search', '');
        $limit = $request->get('limit', 10);

        // Se for best_sellers, usa a loja_vendas_itens
        if ($rule === 'best_sellers') {
            $top = \App\Models\LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                ->where('loja_vendas.loja_id', $loja->id)
                // ->whereMonth('loja_vendas.data_hora', now()->month) // Opcional restringir por mês
                ->select('loja_vendas_itens.produto_id', \Illuminate\Support\Facades\DB::raw('SUM(loja_vendas_itens.quantidade) as qtd'))
                ->groupBy('loja_vendas_itens.produto_id')
                ->orderByDesc('qtd')
                ->limit($limit)
                ->get()
                ->pluck('produto_id');

            $produtos = \App\Models\Produto::whereIn('id', $top)->get();
            return response()->json($produtos);
        }

        // Categoria 
        if ($rule === 'category') {
            $produtos = \App\Models\Produto::where('loja_id', $loja->id)
                ->where('categoria', 'like', "%{$search}%")
                ->limit($limit)
                ->get();
            return response()->json($produtos);
        }

        // Busca manual (search default)
        $produtos = \App\Models\Produto::where('loja_id', $loja->id)
            ->where(function ($query) use ($search) {
                $query->where('nome', 'like', "%{$search}%")
                    ->orWhere('codigo_barra', 'like', "%{$search}%");
            })
            ->limit(10)
            ->get();

        return response()->json($produtos);
    }
}
