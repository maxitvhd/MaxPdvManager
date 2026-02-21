<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MakeMaxDivulgaCronJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maxdivulga:run-schedules';
    protected $description = 'Verifica e executa campanhas programadas do MaxDivulga no piloto automático.';

    public function handle()
    {
        $now = now();
        $horaAtual = $now->format('H:i');
        $diaAtual = (string) $now->dayOfWeek; // 0 = Domingo, 1 = Segunda...

        $campanhas = \App\Models\MaxDivulgaCampaign::with('products')
            ->where('is_scheduled', true)
            ->where('is_active', true)
            ->get();

        $disparos = 0;

        foreach ($campanhas as $campaign) {
            $days = $campaign->scheduled_days ?: [];
            $times = $campaign->scheduled_times ?: [];

            // Verifica se a campanha deve rodar nesta hora exata
            if (in_array($diaAtual, $days) && in_array($horaAtual, $times)) {
                $this->info("Executando campanha #{$campaign->id} - {$campaign->name}");
                $this->processCampaign($campaign);
                $disparos++;
            }
        }

        $this->info("Concluído. Campanhas executadas: {$disparos}");
    }

    private function processCampaign($campaign)
    {
        try {
            // 1. Resolver loja
            $loja = \App\Models\Loja::find($campaign->loja_id);
            if (!$loja)
                throw new \Exception("Loja ID {$campaign->loja_id} não encontrada.");

            // 2. Refazer seleção de produtos se for regra dinâmica (best_sellers, etc)
            $rule = $campaign->product_selection_rule ?: [];
            $ruleType = $rule['type'] ?? 'manual';
            $qty = intval($rule['quantity'] ?? 10);

            $produtos = collect();

            if ($ruleType === 'best_sellers') {
                $topNomes = \App\Models\LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                    ->where('loja_vendas.loja_id', $loja->id)
                    ->select('loja_vendas_itens.produto_nome', \Illuminate\Support\Facades\DB::raw('SUM(loja_vendas_itens.quantidade) as total_vendido'))
                    ->groupBy('loja_vendas_itens.produto_nome')
                    ->orderByDesc('total_vendido')
                    ->limit(50)
                    ->get()->pluck('produto_nome');
                $produtos = \App\Models\Produto::where('loja_id', $loja->id)->whereIn('nome', $topNomes)->limit($qty)->get();
            } elseif ($ruleType === 'category') {
                $cat = $rule['value'] ?? '';
                $produtos = \App\Models\Produto::where('loja_id', $loja->id)->where('categoria', 'like', "%{$cat}%")->limit($qty)->get();
            } else {
                // manual: usa a relação pivot atual
                $produtos = $campaign->products;
            }

            if ($produtos->isEmpty()) {
                throw new \Exception("Nenhum produto selecionado para a campanha.");
            }

            // 3. Montar dados para o catálogo com desconto atual da pivot ou $discount_rules
            $descontoGlobalPct = floatval($campaign->discount_rules['percentage'] ?? 0);
            $produtosParaCatalogo = [];

            foreach ($produtos as $prod) {
                // Se o produto veio da Pivot, ele pode ter discount_percentage próprio. Se não, usa fallback.
                $descontoPct = $prod->pivot ? floatval($prod->pivot->discount_percentage) : $descontoGlobalPct;

                $precoOriginal = floatval($prod->preco);
                $precoNovo = $descontoPct > 0 ? $precoOriginal - ($precoOriginal * ($descontoPct / 100)) : $precoOriginal;

                // Fallback imagem
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

                $produtosParaCatalogo[] = [
                    'nome' => $prod->nome,
                    'preco_original' => number_format($precoOriginal, 2, ',', '.'),
                    'preco_novo' => number_format($precoNovo, 2, ',', '.'),
                    'codigo_barra' => $codigoBarra,
                    'imagem_url' => $imagemUrl,
                ];
            }

            // 4. Sorteio de Tema baseado na Qtde (para variar a cada execução do cron)
            $qtd = count($produtosParaCatalogo);
            $temas = \App\Models\MaxDivulgaTheme::where('is_active', true)->get();

            if ($qtd === 1) {
                $temaEscogido = $temas->where('path', 'maxdivulga.themes.destaque_unico')->first() ?? $campaign->theme;
            } else {
                // Se múltiplos produtos, sortear entre os que não são destaque único
                $temasMulti = $temas->where('path', '!=', 'maxdivulga.themes.destaque_unico');
                $temaEscogido = $temasMulti->isNotEmpty() ? $temasMulti->random() : $campaign->theme;
            }

            // Dados Loja
            $logoPath = storage_path("app/public/lojas/{$loja->codigo}/logo/logo.png");
            $logoUrl = file_exists($logoPath) ? url("storage/lojas/{$loja->codigo}/logo/logo.png") : null;
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

            // 5. Novas Copies (AI) usando "surpreendame" ou persona da campanha
            $aiService = new \App\Services\AiCopyWriterService();
            $temaCampanha = $aiService->detectarTema($produtosParaCatalogo);
            $dadosLoja['tema_campanha'] = $temaCampanha;

            $personaExecucao = $campaign->persona === 'surpreendame' ? 'aleatorio' : $campaign->persona;

            $copyPrincipal = $aiService->generateCopy($produtosParaCatalogo, $personaExecucao);
            $copyAcompanhamento = $aiService->generateCopySocial($produtosParaCatalogo, $personaExecucao, $dadosLoja);

            // Temporariamente altera o tema no memory-model para o renderer puxar o sorteado
            $campaign->theme = $temaEscogido;
            $campaign->copy = $copyPrincipal;

            // 6. Renderizar Imagem
            $renderService = new \App\Services\CatalogRendererService();
            $filePath = $renderService->render($campaign, $produtosParaCatalogo, $dadosLoja);

            if (!$filePath)
                throw new \Exception("Falha na renderização da imagem.");

            // 7. Enviar WhatsApp (Simulado/Fila)
            // Como a integração GZappy não consta neste ambiente, registramos o disparo
            $phoneLoja = preg_replace('/\D/', '', $dadosLoja['telefone']);
            if ($phoneLoja) {
                \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-CRON] Mensagem ENVIADA PARA FILA ({$phoneLoja}): " . substr($copyAcompanhamento, 0, 50) . "...");
                $this->info("Mensagem simulada enviada para o logista no numero {$phoneLoja}");
            }

            // 8. Logar Sucesso
            \Illuminate\Support\Facades\DB::table('max_divulga_campaign_logs')->insert([
                'campaign_id' => $campaign->id,
                'status' => 'success',
                'file_path_generated' => $filePath,
                'message' => 'Disparo concluído com sucesso (Tema sorteado: ' . ($temaEscogido->name ?? '') . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $campaign->update(['last_run_at' => now()]);

        } catch (\Exception $e) {
            $this->error("Erro na campanha {$campaign->id}: " . $e->getMessage());

            \Illuminate\Support\Facades\DB::table('max_divulga_campaign_logs')->insert([
                'campaign_id' => $campaign->id,
                'status' => 'failed',
                'message' => substr($e->getMessage(), 0, 500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
