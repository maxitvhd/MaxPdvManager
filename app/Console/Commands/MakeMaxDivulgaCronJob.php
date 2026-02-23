<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

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

        // Mapeamento de numérico do Carbon pro string armazenado no BD pelo Alpine
        $mapDias = [
            0 => 'domingo',
            1 => 'segunda',
            2 => 'terca',
            3 => 'quarta',
            4 => 'quinta',
            5 => 'sexta',
            6 => 'sabado'
        ];
        $diaAtualStr = $mapDias[$now->dayOfWeek];

        $campanhas = \App\Models\MaxDivulgaCampaign::where('is_scheduled', true)
            ->where('is_active', true)
            ->get();

        $disparos = 0;

        foreach ($campanhas as $campaign) {
            $timesObj = $campaign->scheduled_times ?: []; // Este agora é o Array Associativo {"sabado":["09:00"]}

            // Corrige DB legado caso is_array = false / ou index list simples
            if (!is_array($timesObj))
                continue;

            $shouldRun = false;

            if (array_key_exists($diaAtualStr, $timesObj)) {
                $lastRun = $campaign->last_run_at;
                $horariosHoje = $timesObj[$diaAtualStr];

                foreach ($horariosHoje as $time) {
                    if ($horaAtual === $time) {
                        $shouldRun = true;
                        break;
                    }

                    // Lógica de Catch-up: tolerância para se o cron falhou no minuto exato
                    $timeCarbon = \Carbon\Carbon::createFromFormat('H:i', $time);
                    if ($now->greaterThan($timeCarbon)) {
                        if (!$lastRun || $lastRun->lessThan($timeCarbon)) {
                            \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-CRON] Catch-up ativado para campanha #{$campaign->id} (Agendada: {$time}, Atual: {$horaAtual})");
                            $shouldRun = true;
                            break;
                        }
                    }
                }
            }

            if ($shouldRun) {
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
            $qty = intval($rule['quantity'] ?? $rule['limit'] ?? 10);

            $produtos = collect();

            if ($ruleType === 'best_sellers') {
                if (env('LOG_MAXDIVULGA', true))
                    \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-CRON] Modo MAIS VENDIDOS: {$qty} itens.");
                $topNomes = \App\Models\LojaVendaItem::join('loja_vendas', 'loja_vendas_itens.loja_venda_id', '=', 'loja_vendas.id')
                    ->where('loja_vendas.loja_id', $loja->id)
                    ->select('loja_vendas_itens.produto_nome', \Illuminate\Support\Facades\DB::raw('SUM(loja_vendas_itens.quantidade) as total_vendido'))
                    ->groupBy('loja_vendas_itens.produto_nome')
                    ->orderByDesc('total_vendido')
                    ->limit(50)
                    ->get()->pluck('produto_nome');

                $produtos = \App\Models\Produto::where('loja_id', $loja->id)->whereIn('nome', $topNomes)->limit($qty)->get();

            } elseif ($ruleType === 'manual') {
                if (env('LOG_MAXDIVULGA', true))
                    \Illuminate\Support\Facades\Log::info("[MAXDIVULGA-CRON] Modo MANUAL de produtos selecionados.");

                $ids = $rule['selected_ids'] ?? [];
                if (count($ids) > 0) {
                    $produtos = \App\Models\Produto::whereIn('id', $ids)->get();
                }

            } elseif ($ruleType === 'category') {
                $cat = $rule['value'] ?? '';
                $produtos = \App\Models\Produto::where('loja_id', $loja->id)->where('categoria', 'like', "%{$cat}%")->limit($qty)->get();
            }

            if ($produtos->isEmpty()) {
                throw new \Exception("Nenhum produto selecionado para a campanha.");
            }

            // 3. Montar dados para o catálogo com desconto atual da pivot ou $discount_rules
            $descontoGlobalPct = floatval($campaign->discount_rules['percentage'] ?? 0);
            $produtosParaCatalogo = [];

            foreach ($produtos as $prod) {
                $precoOriginal = floatval($prod->preco);
                $descontoPct = $descontoGlobalPct;

                // Se a seleção foi manual e gravada na configuração json, sobrepõe com a config individual de cada ID
                if ($ruleType === 'manual' && isset($rule['individual_discounts'][$prod->id])) {
                    $descontoPct = floatval($rule['individual_discounts'][$prod->id]['discount_percentage'] ?? 0);
                }

                $precoNovo = $descontoPct > 0
                    ? $precoOriginal - ($precoOriginal * ($descontoPct / 100))
                    : $precoOriginal;

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

            // 4. Selecionar Tema — respeita a escolha do usuário, sorteia apenas se "auto"
            $qtd = count($produtosParaCatalogo);
            $temas = \App\Models\MaxDivulgaTheme::where('is_active', true)->get();

            if ($qtd === 1) {
                // 1 produto = sempre destaque único (sem escolha)
                $temaEscogido = $temas->where('path', 'maxdivulga.themes.destaque_unico')->first()
                    ?? $campaign->theme;
            } elseif (!empty($campaign->theme_id) && $campaign->theme) {
                // Usuário escolheu um tema específico: respeitar a escolha
                $temaEscogido = $campaign->theme;
            } else {
                // Sem tema definido (auto): sortear entre os temas multi-produto
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
            $copyLocucao = null;
            if (in_array($campaign->format, ['audio', 'full'])) {
                $copyLocucao = $aiService->generateCopyLocucao($produtosParaCatalogo, $personaExecucao, $dadosLoja);
            }

            // Criação da Campanha Filha (instância da programação)
            $novaCampanha = $campaign->replicate();
            $novaCampanha->parent_id = $campaign->id;
            $novaCampanha->theme_id = $temaEscogido->id;
            $novaCampanha->copy = $copyPrincipal;
            $novaCampanha->copy_acompanhamento = $copyAcompanhamento;
            $novaCampanha->copy_locucao = $copyLocucao;
            $novaCampanha->is_scheduled = false; // A filha não é uma programação
            $novaCampanha->status = 'processing';
            $novaCampanha->file_path = null;
            $novaCampanha->audio_file_path = null;
            $novaCampanha->save(); // Salva para ter um ID para o CatalogRendererService

            // 6. Renderizar Imagens e Áudio (compatibilidade formato Full/Audio/Both)
            $renderService = new \App\Services\CatalogRendererService();
            $formatOriginal = $campaign->format;
            $filePath = null;
            $audioPath = null;

            // 'full' e 'both' geram imagem + áudio simultaneamente
            if (in_array($formatOriginal, ['full', 'both'])) {
                $novaCampanha->format = 'image';
                $filePath = $renderService->render($novaCampanha, $produtosParaCatalogo, $dadosLoja);
                $novaCampanha->format = 'audio';
                $audioPath = $renderService->render($novaCampanha, $produtosParaCatalogo, $dadosLoja);
                $novaCampanha->format = $formatOriginal; // restaura o valor original
            } else {
                $pathGerado = $renderService->render($novaCampanha, $produtosParaCatalogo, $dadosLoja);
                if ($formatOriginal === 'audio') {
                    $audioPath = $pathGerado;
                } else {
                    $filePath = $pathGerado;
                }
            }

            if (!$filePath && !in_array($formatOriginal, ['audio']))
                throw new \Exception("Falha na renderização da imagem.");

            // Atualiza e Finaliza a Campanha Filha Gerada
            $novaCampanha->update([
                'file_path' => $filePath,
                'audio_file_path' => $audioPath,
                'status' => 'active',
            ]);

            // Atualiza o Template Pai informando a última vez que rodou o cron
            $campaign->update(['last_run_at' => now(), 'status' => 'active']);

            // 7. Enviar Redes Sociais se houver canais configurados
            $channels = $campaign->channels ?? [];
            if (!empty($channels)) {
                $this->info("Iniciando disparos sociais para " . count($channels) . " canais...");
                foreach ($channels as $channelStr) {
                    try {
                        $parts = explode('|', $channelStr);
                        $provider = $parts[0];
                        $targetType = $parts[1] ?? null;
                        $targetId = $parts[2] ?? null;

                        // Se não tiver ID definido (formato legado/wizard simples), busca todas as contas desse provedor para a loja
                        $query = \App\Models\SocialAccount::where('loja_id', $campaign->loja_id)
                            ->where('provider', $provider);

                        if ($targetId) {
                            $query->where('provider_id', $targetId);
                        }

                        $accounts = $query->get();

                        if ($accounts->isEmpty()) {
                            Log::warning("[MAXDIVULGA-CRON] Nenhuma conta social encontrada para {$provider} na Loja {$campaign->loja_id}");
                            continue;
                        }

                        foreach ($accounts as $account) {
                            $cleanImagePath = str_replace('storage/', '', $filePath);
                            $absImagePath = storage_path('app/public/' . $cleanImagePath);
                            $message = $novaCampanha->copy_acompanhamento ?? $copyAcompanhamento;

                            if ($provider === 'facebook') {
                                $fbService = new \App\Services\FacebookPostService();

                                // Se veio do Wizard sem targetId, tentamos postar na primeira página/grupo disponível
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

                                // Envia Foto
                                $tgService->postToChat($chatId, $account->token, $absImagePath, $message);

                                // Se tiver áudio e for formato 'completo' (implícito se audioPath existir)
                                if ($audioPath && file_exists(storage_path('app/public/' . str_replace('storage/', '', $audioPath)))) {
                                    $absAudioPath = storage_path('app/public/' . str_replace('storage/', '', $audioPath));
                                    $tgService->postAudioToChat($chatId, $account->token, $absAudioPath, "Escute as ofertas de hoje! 🔊");
                                }
                            }
                        }
                    } catch (\Exception $eSocial) {
                        Log::error("[MAXDIVULGA-CRON] Erro ao postar no canal {$channelStr}: " . $eSocial->getMessage());
                    }
                }
            }

            // 8. Logar Sucesso atrelando à campanha filha gerada
            \Illuminate\Support\Facades\DB::table('max_divulga_campaign_logs')->insert([
                'campaign_id' => $novaCampanha->id,
                'status' => 'success',
                'file_path_generated' => $filePath,
                'message' => 'Disparo concluído com sucesso (Tema sorteado: ' . ($temaEscogido->name ?? '') . ')',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        } catch (\Throwable $e) {
            $this->error("Erro na programação {$campaign->id}: " . $e->getMessage());

            \Illuminate\Support\Facades\DB::table('max_divulga_campaign_logs')->insert([
                'campaign_id' => isset($novaCampanha) ? $novaCampanha->id : $campaign->id,
                'status' => 'failed',
                'message' => substr($e->getMessage(), 0, 500),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Marca a template como rodada de qualquer forma pra não ficar em loop
            $campaign->update(['last_run_at' => now()]);
        }
    }
}
