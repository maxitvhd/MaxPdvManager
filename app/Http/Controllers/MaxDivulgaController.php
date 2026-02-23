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

use App\Traits\ResolvesLoja;

class MaxDivulgaController extends Controller
{
    use ResolvesLoja;

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('admin') || $user->hasRole('super-admin')) {
            // Admin vê TODAS as campanhas originais (ignora as filhas do cron na listagem)
            $campaigns = MaxDivulgaCampaign::whereNull('parent_id')
                ->orderBy('created_at', 'desc')->get();
        } else {
            $loja = $this->resolverLoja();
            $lojaId = $loja ? $loja->id : null;

            // Exibe campanhas originais desta loja (sem ser as geradas repetidas do cronJob)
            $campaigns = MaxDivulgaCampaign::whereNull('parent_id')
                ->where(function ($q) use ($lojaId) {
                    $q->where('loja_id', $lojaId)
                        ->orWhereNull('loja_id');  // compatibilidade retroativa
                })
                ->orderBy('created_at', 'desc')
                ->get();
        }

        // Lê os mp3 da pasta de fundos musicais para editar no Modal
        $bgAudios = collect(\Illuminate\Support\Facades\Storage::disk('public')->files('audio/fundo'))->map(function ($file) {
            return basename($file);
        })->toArray();

        return view('lojista.maxdivulga.index', compact('campaigns', 'bgAudios'));

    }

    public function create()
    {
        $themes = MaxDivulgaTheme::where('is_active', true)->get();
        $loja = $this->resolverLoja();

        $fundos = [];
        $dir = storage_path('app/public/audio/fundo');
        if (is_dir($dir)) {
            $files = array_diff(scandir($dir), ['.', '..']);
            foreach ($files as $file) {
                if (strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'mp3') {
                    $fundos[] = $file;
                }
            }
        }

        return view('lojista.maxdivulga.create', compact('themes', 'loja', 'fundos'));
    }

    public function store(Request $request)
    {
        $loja = $this->resolverLoja($request->loja_id);

        if (!$loja) {
            return back()->withErrors(['loja' => 'Nenhuma loja encontrada para gerar campanha.']);
        }

        $timesJson = $request->input('scheduled_times_json', '{}');
        $times = json_decode($timesJson, true);
        if (!is_array($times) || (count($times) > 0 && isset($times[0]))) {
            $times = []; // Reseta se for inválido ou se vier num formato legado acidental (Array Simples)
        }
        $days = array_keys($times);

        $tenantId = auth()->id() ?? null;
        $qty = max(1, intval($request->input('product_quantity', 10)));

        $campaign = MaxDivulgaCampaign::create([
            'tenant_id' => $tenantId,
            'loja_id' => $loja->id,
            'name' => $request->name ?? 'Campanha ' . now()->format('d/m/Y'),
            'type' => $request->type,
            'format' => $request->input('format'),
            'channels' => $request->input('channels', []),
            'product_selection_rule' => [
                'type' => $request->input('product_selection_rule.type', 'best_sellers'),
                'limit' => $qty,
                'category' => $request->input('product_selection_rule.category', null),
                'discount_percentage' => floatval($request->input('discount_rules.percentage', 0))
            ],
            'persona' => $request->persona,
            'status' => 'active',
            'is_scheduled' => $request->boolean('is_scheduled', false),
            'scheduled_days' => $days,
            'scheduled_times' => $times,
            'is_active' => true,
            'bg_audio' => $request->bg_audio,
            'bg_volume' => $request->bg_volume ?? 0.20,
            'theme_id' => $request->theme_id, // Kept this as it's used later for theme detection logic
            'voice' => $request->voice,
            'audio_speed' => $request->audio_speed,
            'noise_scale' => $request->noise_scale,
            'noise_w' => $request->noise_w,
        ]);

        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-01] Campanha #{$campaign->id} '{$campaign->name}' criada para loja: {$loja->nome} ({$loja->codigo})");

        $selectedIds = $request->input('selected_products', []);
        $ruleType = $request->input('product_selection_rule.type');
        $qty = max(1, intval($request->input('product_quantity', 10)));

        // Se for modo manual, salva os IDs dos produtos selecionados e os descontos individuais dentro da própria regra salva em DB
        if ($ruleType === 'manual' && count($selectedIds) > 0) {
            $descontoGlobalPct = floatval($request->input('discount_rules.percentage', 0));
            $descontosIndividuais = $request->input('discount_products', []);
            $syncData = [];
            foreach ($selectedIds as $pid) {
                $discount = isset($descontosIndividuais[$pid]) ? floatval($descontosIndividuais[$pid]) : $descontoGlobalPct;
                $syncData[$pid] = ['discount_percentage' => $discount];
            }

            $currentRule = $campaign->product_selection_rule ?: [];
            $currentRule['selected_ids'] = $selectedIds;
            $currentRule['individual_discounts'] = $syncData;
            $campaign->product_selection_rule = $currentRule;
            $campaign->save();
        }

        // Se a campanha for agendada, não aciona a rotina pesada com IA, render Playwright nem Voice API. Apenas sai.
        if ($campaign->is_scheduled) {
            $campaign->update(['status' => 'pending']);
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-01] Campanha #{$campaign->id} Agendada criada. Será renderizada na hora exata pelo Cron Job.");
            return redirect()->route('lojista.maxdivulga.index')
                ->with('success', '✅ Campanha agendada com sucesso! A IA gerará o catálogo, a locução e os textos no horário programado.');
        }

        $produtos = collect();

        if ($ruleType === 'manual' && count($selectedIds) > 0) {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-02] Modo MANUAL. Qtde selecionados: " . count($selectedIds));
            $produtos = Produto::whereIn('id', $selectedIds)->limit($qty)->get();

        } elseif ($ruleType === 'best_sellers') {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-02] Modo MAIS VENDIDOS (por produto_nome, limit={$qty}).");
            $topNomes = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                ->where('loja_vendas.loja_id', $loja->id)
                ->select('loja_vendas_itens.produto_nome', DB::raw('SUM(loja_vendas_itens.quantidade) as total_vendido'))
                ->groupBy('loja_vendas_itens.produto_nome')
                ->orderByDesc('total_vendido')
                ->limit(50) // busca mais pra filtrar depois
                ->get()->pluck('produto_nome');

            $query = Produto::where('loja_id', $loja->id)->whereIn('nome', $topNomes);
            // Se o usuário desmarcou produtos, respeitar a seleção
            if (count($selectedIds) > 0) {
                $query->whereIn('id', $selectedIds);
            }
            $produtos = $query->limit($qty)->get();

        } elseif ($ruleType === 'category') {
            $cat = $request->input('product_selection_rule.value', '');
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-02] Modo CATEGORIA: {$cat}, limit={$qty}");
            $query = Produto::where('loja_id', $loja->id)->where('categoria', 'like', "%{$cat}%");
            if (count($selectedIds) > 0) {
                $query->whereIn('id', $selectedIds);
            }
            $produtos = $query->limit($qty)->get();
        }

        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-03] Total de produtos para o catálogo: " . $produtos->count());

        $descontoGlobalPct = floatval($request->input('discount_rules.percentage', 0));
        $descontosIndividuais = $request->input('discount_products', []); // Array com o formato [product_id => discount_percentage]
        $produtosParaCatalogo = [];

        foreach ($produtos as $prod) {
            $precoOriginal = floatval($prod->preco);
            // Verifica se o produto tem desconto individual setado (sobrepõe o global)
            $descontoPct = isset($descontosIndividuais[$prod->id]) ? floatval($descontosIndividuais[$prod->id]) : $descontoGlobalPct;

            $precoNovo = $descontoPct > 0
                ? $precoOriginal - ($precoOriginal * ($descontoPct / 100))
                : $precoOriginal;

            // Busca imagem pelo código de barras (padrão do sistema)
            $codigoBarra = trim($prod->codigo_barra ?? '');
            $imagemUrl = null;
            foreach (['.jpg', '.jpeg', '.png', '.webp'] as $ext) {
                $testPath = storage_path("app/public/lojas/{$loja->codigo}/produtos/{$codigoBarra}{$ext}");
                if ($codigoBarra && file_exists($testPath)) {
                    $imagemUrl = url("storage/lojas/{$loja->codigo}/produtos/{$codigoBarra}{$ext}");
                    break;
                }
            }
            // Fallback: campo imagem do cadastro
            if (!$imagemUrl && !empty($prod->imagem) && file_exists(storage_path('app/public/' . ltrim($prod->imagem, '/')))) {
                $imagemUrl = url('storage/' . ltrim($prod->imagem, '/'));
            }

            $produtosParaCatalogo[] = [
                'nome' => $prod->nome,
                'preco_original' => number_format($precoOriginal, 2, ',', '.'),
                'preco_novo' => number_format($precoNovo, 2, ',', '.'),
                'codigo_barra' => $codigoBarra,
                'imagem_url' => $imagemUrl,
            ];
        }

        // Dados da loja para os templates (inclui logo)
        $logoPath = storage_path("app/public/lojas/{$loja->codigo}/logo/logo.png");
        $logoUrl = file_exists($logoPath) ? url("storage/lojas/{$loja->codigo}/logo/logo.png") : null;
        // Tenta .jpg também
        if (!$logoUrl) {
            $logoPathJpg = storage_path("app/public/lojas/{$loja->codigo}/logo/logo.jpg");
            if (file_exists($logoPathJpg))
                $logoUrl = url("storage/lojas/{$loja->codigo}/logo/logo.jpg");
        }

        $dadosLoja = [
            'nome' => $loja->nome ?? '',
            'telefone' => $loja->telefone ?? '',
            'endereco' => trim(($loja->endereco ?? '') . ', ' . ($loja->bairro ?? '')),
            'cidade' => ($loja->cidade ?? '') . '/' . ($loja->estado ?? ''),
            'cep' => $loja->cep ?? '',
            'cnpj' => $loja->cnpj ?? '',
            'codigo' => $loja->codigo ?? '',
            'logo_url' => $logoUrl,
        ];

        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-04] Acionando IA para gerar versões de copy e locução...");
        $aiService = new \App\Services\AiCopyWriterService();

        // Detecta o tema com base nos produtos (antes da copy, para usar no folder e no prompt)
        $temaCampanha = $aiService->detectarTema($produtosParaCatalogo);
        $dadosLoja['tema_campanha'] = $temaCampanha;
        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-04A] Tema detectado: {$temaCampanha}");

        $copyPrincipal = $aiService->generateCopy($produtosParaCatalogo, $campaign->persona);
        $copyAcompanhamento = $aiService->generateCopySocial($produtosParaCatalogo, $campaign->persona, $dadosLoja);

        $copyLocucao = null;
        if (in_array($campaign->format, ['audio', 'full'])) {
            $copyLocucao = $aiService->generateCopyLocucao($produtosParaCatalogo, $campaign->persona, $dadosLoja);
        }

        // Lógica para rotear automaticamente o Renderizador para o template adequado (1 vs Múltiplos)
        $themeId = $campaign->theme_id;
        $qtdProdutos = count($produtosParaCatalogo);
        $temaOriginal = \App\Models\MaxDivulgaTheme::find($themeId);

        if ($qtdProdutos === 1) {
            $temaDestaque = \App\Models\MaxDivulgaTheme::where('path', 'maxdivulga.themes.destaque_unico')->first();
            if ($temaDestaque)
                $themeId = $temaDestaque->id;
        } elseif ($qtdProdutos > 1 && $temaOriginal && $temaOriginal->path === 'maxdivulga.themes.destaque_unico') {
            $temaClassico = \App\Models\MaxDivulgaTheme::where('path', 'maxdivulga.themes.classico_ofertas')->first();
            if ($temaClassico)
                $themeId = $temaClassico->id;
        }

        $campaign->update([
            'theme_id' => $themeId,
            'copy' => $copyPrincipal,
            'copy_acompanhamento' => $copyAcompanhamento,
            'copy_locucao' => $copyLocucao,
        ]);

        if (in_array($campaign->format, ['image', 'pdf', 'audio', 'full'])) {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-06] Iniciando Renderização (CatalogRendererService)");
            $renderService = new \App\Services\CatalogRendererService();
            $formatOriginal = $campaign->format;

            if ($formatOriginal === 'full') {
                // 1) Renderiza a Imagem (PNG)
                $campaign->format = 'image';
                $filePath = $renderService->render($campaign, $produtosParaCatalogo, $dadosLoja);

                // 2) Renderiza o Áudio (MP3)
                $campaign->format = 'audio';
                $audioPath = $renderService->render($campaign, $produtosParaCatalogo, $dadosLoja);

                // 3) Retorna ao normal e salva tudo de uma vez para não sobrescrever o DB format com audio
                $campaign->format = 'full';
                $campaign->update([
                    'file_path' => $filePath ?: $campaign->file_path,
                    'audio_file_path' => $audioPath,
                    // Garante que o formato permaneceu original
                    'format' => 'full'
                ]);

                if (env('LOG_MAXDIVULGA', true))
                    Log::info("[MAXDIVULGA-09] Sucesso FULL! Imagem: {$filePath} | Audio: {$audioPath}");
            } else {
                $filePath = $renderService->render($campaign, $produtosParaCatalogo, $dadosLoja);
                if ($filePath) {
                    if ($formatOriginal === 'audio') {
                        $campaign->update(['audio_file_path' => $filePath]);
                    } else {
                        $campaign->update(['file_path' => $filePath]);
                    }
                    if (env('LOG_MAXDIVULGA', true))
                        Log::info("[MAXDIVULGA-09] Sucesso! Arquivo: {$filePath}");
                } else {
                    Log::error("[MAXDIVULGA-09] FALHA na renderização! Verifique os logs do CatalogRendererService.");
                }
            }
        }

        // 7. Enviar Redes Sociais IMEDIATAMENTE se houver canais configurados (Campanha Direta)
        $channels = $campaign->channels ?? [];
        if (!empty($channels)) {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-SOCIAL-DIRETO] Iniciando disparos para " . count($channels) . " canais...");

            foreach ($channels as $channelStr) {
                try {
                    $parts = explode('|', $channelStr);
                    $provider = $parts[0];
                    $targetType = $parts[1] ?? null;
                    $targetId = $parts[2] ?? null;

                    // Busca contas sociais (mesma lógica inteligente do Cron)
                    $query = \App\Models\SocialAccount::where('loja_id', $campaign->loja_id)
                        ->where('provider', $provider);

                    if ($targetId) {
                        $query->where('provider_id', $targetId);
                    }

                    $accounts = $query->get();

                    foreach ($accounts as $account) {
                        $absImagePath = $filePath ? storage_path('app/public/' . str_replace('storage/', '', $filePath)) : null;
                        $message = $campaign->copy_acompanhamento ?: $copyAcompanhamento;

                        if ($provider === 'facebook' && $absImagePath) {
                            $fbService = new \App\Services\FacebookPostService();
                            $finalTargetId = $targetId;
                            $finalTargetType = $targetType;
                            $finalToken = $account->token;

                            if (!$finalTargetId && !empty($account->meta_data['pages'])) {
                                $page = $account->meta_data['pages'][0];
                                $finalTargetId = $page['id'];
                                $finalTargetType = 'page';
                                $finalToken = $page['access_token'] ?? $account->token;
                            }

                            if ($finalTargetId) {
                                if ($finalTargetType === 'page') {
                                    $fbService->postToPage($finalTargetId, $finalToken, $absImagePath, $message);
                                } else {
                                    $fbService->postToGroup($finalTargetId, $finalToken, $absImagePath, $message);
                                }
                            }
                        }

                        if ($provider === 'telegram') {
                            $tgService = new \App\Services\TelegramPostService();
                            $chatId = $account->provider_id;

                            // Envia Foto (se existir)
                            if ($absImagePath && file_exists($absImagePath)) {
                                $tgService->postToChat($chatId, $account->token, $absImagePath, $message);
                            }

                            // Envia Áudio (se existir)
                            if (!empty($audioPath)) {
                                $absAudioPath = storage_path('app/public/' . str_replace('storage/', '', $audioPath));
                                if (file_exists($absAudioPath)) {
                                    $tgService->postAudioToChat($chatId, $account->token, $absAudioPath, "Confira a locução desta oferta! 🔊");
                                }
                            }
                        }
                    }
                } catch (\Exception $eSocial) {
                    Log::error("[MAXDIVULGA-SOCIAL-DIRETO] Erro ao postar no canal {$channelStr}: " . $eSocial->getMessage());
                }
            }
        }

        return redirect()->route('lojista.maxdivulga.index')
            ->with('success', '✅ Campanha criada! A IA gerou sua arte e o texto de acompanhamento. Clique em "Ver" para conferir.');
    }

    public function show(MaxDivulgaCampaign $campaign)
    {
        $loja = $this->resolverLoja();
        $lojaId = $loja->id ?? null;
        $socialAccounts = \App\Models\SocialAccount::where('loja_id', $lojaId)->get();

        return view('lojista.maxdivulga.show', compact('campaign', 'socialAccounts'));
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

    public function toggleActive(MaxDivulgaCampaign $campaign)
    {
        $campaign->is_active = !$campaign->is_active;
        $campaign->save();
        return response()->json(['success' => true, 'is_active' => $campaign->is_active]);
    }

    public function destroy(MaxDivulgaCampaign $campaign)
    {
        $loja = \App\Models\Loja::find($campaign->loja_id);

        if ($loja) {
            // Se for uma Matriz (Pai) ou Campanha Avulsa, exclui a pasta limpa que contém tudo
            if (is_null($campaign->parent_id)) {
                $pasta_campanha = storage_path("app/public/lojas/{$loja->codigo}/campanhas/campanha_{$campaign->id}");
                if (\Illuminate\Support\Facades\File::exists($pasta_campanha)) {
                    \Illuminate\Support\Facades\File::deleteDirectory($pasta_campanha);
                }
            }

            // Fallback para limpar arquivos individuais (Mídia da filha, ou campanhas antigas do diretório 'catalogo_geral_x')
            if ($campaign->file_path) {
                $caminho_arte = storage_path('app/public/' . str_replace('storage/', '', $campaign->file_path));
                if (file_exists($caminho_arte)) {
                    @unlink($caminho_arte);
                }
            }

            if ($campaign->audio_file_path) {
                $caminho_audio = storage_path('app/public/' . str_replace('storage/', '', $campaign->audio_file_path));
                if (file_exists($caminho_audio)) {
                    @unlink($caminho_audio);
                }
            }

            // Tenta achar restos de gravação HTML atrelados a id na pasta genérica antiga
            $pasta_base = storage_path("app/public/lojas/{$loja->codigo}/campanhas");
            $html_potencial = "{$pasta_base}/*_{$campaign->id}.html";
            foreach (glob($html_potencial) as $file) {
                @unlink($file);
            }
        }

        // A exclusão do Pai irá limpar o BD dos Filhos em Cascata (Foreign Key OnDelete Cascade)
        $campaign->delete();

        // Se excluiu a partir de uma listagem de filhas de uma matriz, retorna pra matriz. Senão, vai pra index.
        if (!is_null($campaign->parent_id)) {
            return redirect()->route('lojista.maxdivulga.show', $campaign->parent_id)->with('success', 'Disparo removido com sucesso!');
        }

        return redirect()->route('lojista.maxdivulga.index')->with('success', 'Programação e todos os arquivos removidos!');
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
            // Busca mais nomes que o necessário para garantir que o limite seja preenchido
            $buscaNomes = max($limit * 3, 60);
            $topNomes = LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                ->where('loja_vendas.loja_id', $loja->id)
                ->select(
                    'loja_vendas_itens.produto_nome',
                    DB::raw('SUM(loja_vendas_itens.quantidade) as total_vendido')
                )
                ->groupBy('loja_vendas_itens.produto_nome')
                ->orderByDesc('total_vendido')
                ->limit($buscaNomes)
                ->get()
                ->pluck('produto_nome');

            return response()->json(
                Produto::where('loja_id', $loja->id)
                    ->whereIn('nome', $topNomes)
                    ->limit($limit)
                    ->get()
            );
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

    public function updateSchedule(Request $request, $id)
    {
        $tenantId = auth()->id();
        $camp = MaxDivulgaCampaign::where('id', $id)->where('tenant_id', $tenantId)->firstOrFail();

        $timesJson = $request->input('scheduled_times_json', '{}');
        $times = json_decode($timesJson, true);

        // Garante coerência pra evitar erro de casting do DB se der erro de decodificação
        if (!is_array($times)) {
            $times = [];
        }

        // Se por ventura o input vier como array simples (vinda de um cache antigo do form), zera pra migrar limpo
        if (count($times) > 0 && isset($times[0])) {
            $times = [];
        }

        // O Days é preenchido de forma limpa extraindo as chaves do objeto json enviado pelo FrontEnd.
        $days = array_keys($times);

        $camp->scheduled_days = $days;
        $camp->scheduled_times = $times;

        // Novas Configs de Piloto de Mídia (Baseado nas Tabs de Edição Plena)
        if ($request->has('format'))
            $camp->format = $request->input('format');
        if ($request->has('voice'))
            $camp->voice = $request->input('voice');
        if ($request->has('bg_audio'))
            $camp->bg_audio = $request->input('bg_audio') !== 'none' ? $request->input('bg_audio') : null;
        if ($request->has('bg_volume'))
            $camp->bg_volume = floatval($request->input('bg_volume', 0.20));
        if ($request->has('audio_speed'))
            $camp->audio_speed = floatval($request->input('audio_speed', 1.0));
        if ($request->has('noise_scale'))
            $camp->noise_scale = floatval($request->input('noise_scale', 0.667));
        if ($request->has('noise_w'))
            $camp->noise_w = floatval($request->input('noise_w', 0.8));

        $camp->save();

        return redirect()->back()->with('success', 'Configurações do Piloto Automático atualizadas com sucesso!');
    }

    /**
     * Listagem de canais sociais do lojista
     */
    public function canaisIndex()
    {
        $loja = $this->resolverLoja();
        $lojaId = $loja->id ?? null;
        $socialAccounts = \App\Models\SocialAccount::where('loja_id', $lojaId)->get();

        return view('lojista.maxdivulga.canais.index', compact('socialAccounts'));
    }

    /* ═══════════════════════════════════════════
     *  THEME STUDIO — Previewer & Editor
     * ═══════════════════════════════════════════ */

    /**
     * Painel principal do Theme Studio
     */
    public function themeStudio()
    {
        $loja = $this->resolverLoja();
        $themes = MaxDivulgaTheme::where('is_active', true)->get();
        $produtos = $loja
            ? Produto::where('loja_id', $loja->id)->orderBy('nome')->get()
            : collect();

        return view('lojista.maxdivulga.theme_studio', compact('themes', 'produtos', 'loja'));
    }

    /**
     * Renderiza o HTML puro do tema (usado pelo iframe)
     */
    public function themePreview(Request $request)
    {
        $themeId = $request->get('theme_id');
        $productIds = $request->get('products', []);
        $qty = max(1, intval($request->get('qty', 10)));
        $discount = floatval($request->get('discount', 0));

        $theme = MaxDivulgaTheme::find($themeId);
        if (!$theme) {
            return response('<h2 style="font-family:sans-serif;color:red;padding:40px">Tema não encontrado</h2>', 404);
        }

        $loja = $this->resolverLoja();
        if (!$loja) {
            return response('<h2 style="font-family:sans-serif;color:red;padding:40px">Loja não encontrada</h2>', 404);
        }

        // Busca produtos
        $query = Produto::where('loja_id', $loja->id);
        if (!empty($productIds)) {
            $query->whereIn('id', $productIds);
        }
        $rawProdutos = $query->limit($qty)->get();

        // Monta array de produtos no formato esperado pelos templates
        $produtos = [];
        foreach ($rawProdutos as $prod) {
            $preco = floatval($prod->preco);
            $precoNovo = $discount > 0 ? $preco - ($preco * ($discount / 100)) : $preco;

            $codigoBarra = trim($prod->codigo_barra ?? '');
            $imagemUrl = null;
            foreach (['.jpg', '.jpeg', '.png', '.webp'] as $ext) {
                $testPath = storage_path("app/public/lojas/{$loja->codigo}/produtos/{$codigoBarra}{$ext}");
                if ($codigoBarra && file_exists($testPath)) {
                    $imagemUrl = url("storage/lojas/{$loja->codigo}/produtos/{$codigoBarra}{$ext}");
                    break;
                }
            }
            if (!$imagemUrl && !empty($prod->imagem) && file_exists(storage_path('app/public/' . ltrim($prod->imagem, '/')))) {
                $imagemUrl = url('storage/' . ltrim($prod->imagem, '/'));
            }

            $produtos[] = [
                'nome' => $prod->nome,
                'preco_original' => number_format($preco, 2, ',', '.'),
                'preco_novo' => number_format($precoNovo, 2, ',', '.'),
                'codigo_barra' => $codigoBarra,
                'imagem_url' => $imagemUrl,
            ];
        }

        // Dados da loja
        $logoPath = storage_path("app/public/lojas/{$loja->codigo}/logo/logo.png");
        $logoUrl = file_exists($logoPath) ? url("storage/lojas/{$loja->codigo}/logo/logo.png") : null;
        if (!$logoUrl) {
            $logoPathJpg = storage_path("app/public/lojas/{$loja->codigo}/logo/logo.jpg");
            if (file_exists($logoPathJpg))
                $logoUrl = url("storage/lojas/{$loja->codigo}/logo/logo.jpg");
        }
        $dadosLoja = [
            'nome' => $loja->nome ?? 'Seu Mercado',
            'telefone' => $loja->telefone ?? '(00) 0000-0000',
            'endereco' => trim(($loja->endereco ?? '') . ', ' . ($loja->bairro ?? '')),
            'cidade' => ($loja->cidade ?? '') . '/' . ($loja->estado ?? ''),
            'cep' => $loja->cep ?? '',
            'cnpj' => $loja->cnpj ?? '',
            'codigo' => $loja->codigo ?? '',
            'logo_url' => $logoUrl,
        ];

        // Campanha fictícia para o template
        $campaign = (object) ['id' => 'preview', 'copy' => null, 'copy_acompanhamento' => null];

        try {
            $html = view($theme->path, [
                'produtos' => $produtos,
                'loja' => $dadosLoja,
                'campaign' => $campaign,
                'copyTexto' => null,
            ])->render();
        } catch (\Throwable $e) {
            return response('<pre style="font-family:monospace;padding:30px;color:red">Erro ao renderizar tema:<br>' . htmlspecialchars($e->getMessage()) . '</pre>', 500);
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Abre o editor de código do arquivo Blade do tema
     */
    public function themeEditor(MaxDivulgaTheme $theme)
    {
        $viewPath = resource_path('views/' . str_replace('.', '/', $theme->path) . '.blade.php');

        if (!file_exists($viewPath)) {
            return back()->with('error', "Arquivo do tema não encontrado: {$viewPath}");
        }

        $code = file_get_contents($viewPath);
        return view('lojista.maxdivulga.theme_editor', compact('theme', 'code'));
    }

    /**
     * Salva o código editado no arquivo Blade do tema
     */
    public function themeEditorSave(Request $request, MaxDivulgaTheme $theme)
    {
        $viewPath = resource_path('views/' . str_replace('.', '/', $theme->path) . '.blade.php');

        if (!file_exists($viewPath)) {
            return response()->json(['success' => false, 'message' => 'Arquivo não encontrado.'], 404);
        }

        $code = $request->input('code', '');
        if (empty(trim($code))) {
            return response()->json(['success' => false, 'message' => 'Código vazio não permitido.'], 422);
        }

        // Backup antes de salvar
        $backupPath = $viewPath . '.bak.' . date('YmdHis');
        copy($viewPath, $backupPath);

        file_put_contents($viewPath, $code);

        // Limpa cache de views
        try {
            \Artisan::call('view:clear');
        } catch (\Throwable $e) {
        }

        return response()->json(['success' => true, 'message' => 'Tema salvo com sucesso!']);
    }

    /**
     * Renderiza código Blade bruto para o live preview do editor
     */
    public function themeRenderCode(Request $request)
    {
        $code = $request->input('code', '');
        $themeId = $request->input('theme_id');
        $qty = max(1, intval($request->input('qty', 6)));

        if (empty(trim($code))) {
            return response('<p style="font:14px sans-serif;color:#999;padding:30px">Código vazio</p>');
        }

        $loja = $this->resolverLoja();
        if (!$loja) {
            return response('<p style="font:14px sans-serif;color:red;padding:30px">Loja não encontrada</p>', 404);
        }

        // Produtos de amostra
        $rawProdutos = Produto::where('loja_id', $loja->id)->limit($qty)->get();
        $produtos = [];
        foreach ($rawProdutos as $prod) {
            $preco = floatval($prod->preco);
            $codigoBarra = trim($prod->codigo_barra ?? '');
            $imagemUrl = null;
            foreach (['.jpg', '.jpeg', '.png', '.webp'] as $ext) {
                $testPath = storage_path("app/public/lojas/{$loja->codigo}/produtos/{$codigoBarra}{$ext}");
                if ($codigoBarra && file_exists($testPath)) {
                    $imagemUrl = url("storage/lojas/{$loja->codigo}/produtos/{$codigoBarra}{$ext}");
                    break;
                }
            }
            $produtos[] = [
                'nome' => $prod->nome,
                'preco_original' => number_format($preco, 2, ',', '.'),
                'preco_novo' => number_format($preco * 0.9, 2, ',', '.'),
                'codigo_barra' => $codigoBarra,
                'imagem_url' => $imagemUrl,
            ];
        }

        // Logo da loja
        $logoUrl = null;
        foreach (['png', 'jpg', 'jpeg', 'webp'] as $ext) {
            $p = storage_path("app/public/lojas/{$loja->codigo}/logo/logo.{$ext}");
            if (file_exists($p)) {
                $logoUrl = url("storage/lojas/{$loja->codigo}/logo/logo.{$ext}");
                break;
            }
        }
        $dadosLoja = [
            'nome' => $loja->nome ?? 'Seu Mercado',
            'telefone' => $loja->telefone ?? '(00) 0000-0000',
            'endereco' => trim(($loja->endereco ?? '') . ', ' . ($loja->bairro ?? '')),
            'cidade' => ($loja->cidade ?? '') . '/' . ($loja->estado ?? ''),
            'cep' => $loja->cep ?? '',
            'cnpj' => $loja->cnpj ?? '',
            'codigo' => $loja->codigo ?? '',
            'logo_url' => $logoUrl,
        ];
        $campaign = (object) ['id' => 'preview', 'copy' => null];

        try {
            // Renderiza o código Blade em memória usando um arquivo temporário
            $tmpFile = tempnam(sys_get_temp_dir(), 'blade_') . '.blade.php';
            file_put_contents($tmpFile, $code);

            // Cria uma view a partir de string usando eval do Blade engine
            $blade = app('blade.compiler');
            $compiled = $blade->compileString($code);

            $html = (function () use ($compiled, $produtos, $dadosLoja, $campaign) {
                extract(['produtos' => $produtos, 'loja' => $dadosLoja, 'campaign' => $campaign, 'copyTexto' => null]);
                ob_start();
                eval ('?>' . $compiled);
                return ob_get_clean();
            })();

            @unlink($tmpFile);
        } catch (\Throwable $e) {
            return response(
                '<pre style="font:12px monospace;padding:20px;color:red;white-space:pre-wrap">'
                . 'Erro no template:<br>' . htmlspecialchars($e->getMessage()) . '</pre>'
            );
        }

        return response($html)->header('Content-Type', 'text/html; charset=UTF-8');
    }

    /**
     * Abre o visual builder GrapeJS
     */
    public function themeBuilder(MaxDivulgaTheme $theme)
    {
        $loja = $this->resolverLoja();
        $produtos = $loja ? Produto::where('loja_id', $loja->id)->limit(6)->get() : collect();
        $previewUrl = route('lojista.maxdivulga.theme_preview', []) . '?theme_id=' . $theme->id . '&qty=6';
        return view('lojista.maxdivulga.theme_builder', compact('theme', 'loja', 'produtos', 'previewUrl'));
    }

    /**
     * Salva o HTML do GrapeJS convertido para Blade
     */
    public function themeBuilderSave(Request $request, MaxDivulgaTheme $theme)
    {
        $html = $request->input('html', '');
        $css = $request->input('css', '');

        if (empty(trim($html))) {
            return response()->json(['success' => false, 'message' => 'HTML vazio.'], 422);
        }

        $viewPath = resource_path('views/' . str_replace('.', '/', $theme->path) . '.blade.php');

        // Backup
        if (file_exists($viewPath)) {
            copy($viewPath, $viewPath . '.bak.' . date('YmdHis'));
        }

        // Converte HTML com data-* para Blade
        $blade = $this->convertBuilderHtmlToBlade($html, $css);

        file_put_contents($viewPath, $blade);
        try {
            \Artisan::call('view:clear');
        } catch (\Throwable $e) {
        }

        return response()->json(['success' => true, 'message' => 'Tema salvo com sucesso!']);
    }

    /**
     * Converte HTML do GrapeJS (com marcadores data-*) para template Blade
     */
    private function convertBuilderHtmlToBlade(string $html, string $css): string
    {
        // Injeta PHP dinâmico no topo
        $phpBlock = <<<'PHP'
@php
    $qty  = count($produtos);
    if ($qty <= 2)      { $cols = $qty; } elseif ($qty <= 4) { $cols = 2; }
    elseif ($qty <= 9)  { $cols = 3;   } else                { $cols = 4; }
    $rows = ceil($qty / $cols);
    $headerH = 260; $copyH = 90; $footerH = 150; $rodapeH = 44;
    $gapPx = 10; $padTop = 14;
    $gridH     = 1920 - $headerH - $copyH - $footerH - $rodapeH;
    $rowsOnlyH = $gridH - ($rows - 1) * $gapPx - 2 * $padTop;
    $rowH      = max(80, floor($rowsOnlyH / $rows));
    $nomeFs    = round(max(0.72, min(1.25, $rowH / 200)) * 10) / 10;
    $precoFs   = round(max(1.10, min(2.70, $rowH / 120)) * 10) / 10;
    $imgMaxH   = max(50, min(170, intval($rowH * 0.45)));
    $pad       = max(7,  min(18,  intval($rowH * 0.058)));
    @php
    \Carbon\Carbon::setLocale('pt_BR');
@endphp

PHP;

        // Substitui marcadores de dados da loja
        $html = preg_replace('/<([^>]+)data-loja="nome"([^>]*)>.*?<\/\1>/s', "<$1data-loja=\"nome\"$2>{{ \$loja['nome'] ?? 'Seu Mercado' }}</$1>", $html);
        $html = str_replace('data-loja="nome"', '', $html);

        // Substituições simples
        $replacements = [
            // IA copy
            '{{AI_HEADLINE}}' => "{{ \$headline ?? 'Ofertas da Semana!' }}",
            '{{AI_SUBTITLE}}' => "{{ \$subtitulo ?? 'Preços imbatíveis para você.' }}",
            // Loja
            '{{LOJA_NOME}}' => "{{ \$loja['nome'] ?? 'Seu Mercado' }}",
            '{{LOJA_FONE}}' => "{{ \$loja['telefone'] ?? '' }}",
            '{{LOJA_ENDE}}' => "{{ \$loja['endereco'] ?? '' }}",
            '{{LOJA_CNPJ}}' => "@if(!empty(\$loja['cnpj'])) CNPJ: {{ \$loja['cnpj'] }} @endif",
            '{{LOJA_CIDADE}}' => "{{ \$loja['cidade'] ?? '' }}",
            '{{DATA_VALIDADE}}' => "{{ \\Carbon\\Carbon::now()->addDays(7)->translatedFormat('d \\d\\e F') }}",
            '{{CAMPAIGN_ID}}' => "{{ \$campaign->id ?? '000' }}",
            // Loop de produtos
            '{{PRODUCT_LOOP_START}}' => "@forelse(\$produtos as \$prod)",
            '{{PRODUCT_LOOP_END}}' => "@empty\n<div style=\"grid-column:1/-1;text-align:center;padding:30px;color:#999\">Nenhum produto.</div>\n@endforelse",
            // Dados do produto
            '{{PROD_NOME}}' => "{{ \$prod['nome'] }}",
            '{{PROD_PRECO}}' => "{{ \$prod['preco_novo'] }}",
            '{{PROD_PRECO_DE}}' => "{{ \$prod['preco_original'] }}",
            '{{PROD_IMAGEM}}' => "@if(!empty(\$prod['imagem_url']))<img src=\"{{ \$prod['imagem_url'] }}\" alt=\"{{ \$prod['nome'] }}\">@else<img src=\"https://placehold.co/200x200/e0e0e0/999?text=Produto\" style=\"opacity:.3\">@endif",
            // Grid dinâmico
            '{{GRID_COLS}}' => "{{ \$cols }}",
            '{{ROW_H}}' => "{{ \$rowH }}",
            '{{PAD}}' => "{{ \$pad }}",
            '{{IMG_MAX_H}}' => "{{ \$imgMaxH }}",
            '{{NOME_FS}}' => "{{ \$nomeFs }}",
            '{{PRECO_FS}}' => "{{ \$precoFs }}",
            // Logo com fallback
            '{{LOJA_LOGO}}' => "@if(!empty(\$loja['logo_url']))<img src=\"{{ \$loja['logo_url'] }}\" alt=\"{{ \$loja['nome'] }}\" style=\"max-width:100%;max-height:160px;object-fit:contain\">@else<div class=\"nome-loja\">{{ \$loja['nome'] ?? 'Seu Mercado' }}</div>@endif",
        ];

        foreach ($replacements as $placeholder => $blade) {
            $html = str_replace($placeholder, $blade, $html);
        }

        // Copy block padrão
        $copyBlock = <<<'COPY'
@php
    $linhasCopy = [];
    if (!empty($copyTexto)) {
        preg_match('/HEADLINE:\s*(.+)/i', $copyTexto, $h);
        preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto, $s);
        $linhasCopy['headline']  = trim($h[1] ?? '');
        $linhasCopy['subtitulo'] = trim($s[1] ?? '');
    }
    $headline  = $linhasCopy['headline']  ?? 'Economize de verdade!';
    $subtitulo = $linhasCopy['subtitulo'] ?? 'Preços imbatíveis para você.';
@endphp
COPY;

        $blade = "<!DOCTYPE html>\n<html lang=\"pt-br\">\n<head>\n<meta charset=\"UTF-8\">\n<title>Folheto de Ofertas</title>\n{$phpBlock}\n<style>\n{$css}\n</style>\n</head>\n<body>\n{$copyBlock}\n{$html}\n</body>\n</html>";
        return $blade;
    }
}


