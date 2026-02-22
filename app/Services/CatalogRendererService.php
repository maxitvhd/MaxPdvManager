<?php

namespace App\Services;

use App\Models\MaxDivulgaCampaign;
use App\Models\MaxDivulgaConfig;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Str;

class CatalogRendererService
{
    protected $config;

    public function __construct()
    {
        $this->config = MaxDivulgaConfig::first();
    }

    public function render(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja = [])
    {
        try {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-07] Iniciando renderização Playwright. Formato: " . $campaign->format);
            switch ($campaign->format) {
                case 'image':
                    return $this->gerarImagem($campaign, $produtos, $dadosLoja);
                case 'pdf':
                    return $this->gerarPdf($campaign, $produtos, $dadosLoja);
                case 'audio':
                    return $this->gerarAudio($campaign, $produtos, $dadosLoja);
                default:
                    return $campaign->copy;
            }
        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-ERROR] Erro fatal CatalogRendererService: " . $e->getMessage());
            Log::error($e->getTraceAsString());
            return false;
        }
    }

    private function construirPastaSaida(MaxDivulgaCampaign $campaign, array $dadosLoja): string
    {
        $codigoLoja = $dadosLoja['codigo'] ?? 'sem-codigo';
        $tema = $dadosLoja['tema_campanha'] ?? 'catalogo_geral';

        // Pasta exclusiva: Usa o ID do "Pai" (Programação) para agrupar, ou o próprio ID se for avulsa.
        $idPasta = $campaign->parent_id ?? $campaign->id;
        $nomePasta = "campanha_{$idPasta}";

        $pasta = "lojas/{$codigoLoja}/campanhas/{$nomePasta}";
        $caminhoCompleto = storage_path("app/public/{$pasta}");

        if (!is_dir($caminhoCompleto)) {
            mkdir($caminhoCompleto, 0775, true);
        }
        @chmod($caminhoCompleto, 0775);

        return $pasta;
    }

    private function getHtml(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        $theme = $campaign->theme;
        if (!$theme || !view()->exists($theme->path)) {
            throw new \Exception('Tema não encontrado: ' . ($theme->path ?? 'nenhum'));
        }

        return View::make($theme->path, [
            'campaign' => $campaign,
            'produtos' => $produtos,
            'loja' => $dadosLoja,
            'copyTexto' => $campaign->copy,
        ])->render();
    }

    private function encontrarBinario(array $caminhos): ?string
    {
        foreach ($caminhos as $caminho) {
            $found = trim(shell_exec("which {$caminho} 2>/dev/null") ?? '');
            if ($found && file_exists($found))
                return $found;
            if (file_exists($caminho))
                return $caminho;
        }
        return null;
    }

    private function gerarImagem(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-07A] Preparando HTML para imagem...");
        $html = $this->getHtml($campaign, $produtos, $dadosLoja);
        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);

        $arquivoHtml = 'campanha_' . Str::random(6) . '.html';
        $caminhoHtml = storage_path("app/public/{$pasta}/{$arquivoHtml}");
        file_put_contents($caminhoHtml, $html);
        @chmod($caminhoHtml, 0664);

        $arquivoPng = 'imagem_' . Str::random(6) . '.png';
        $caminhoPng = storage_path("app/public/{$pasta}/{$arquivoPng}");


        try {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08] Invocando Python (WeasyPrint+PyMuPDF) HTML→PNG...");

            $pythonBin = $this->encontrarBinario(['/usr/bin/python3', '/usr/bin/python', '/usr/local/bin/python3', 'python3', 'python']);
            if (!$pythonBin)
                throw new \Exception("Binário do Python não encontrado");

            $script = base_path('render_weasyprint.py');
            if (!file_exists($script))
                throw new \Exception("Script {$script} ausente");

            $cmd = "cd " . escapeshellarg(base_path()) . " && {$pythonBin} " . escapeshellarg($script) . " " . escapeshellarg($caminhoHtml) . " " . escapeshellarg($caminhoPng) . " image 2>&1";

            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08-CMD] Executando: {$cmd}");
            $saida = shell_exec($cmd);
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08-OUTPUT] {$saida}");

            if (!file_exists($caminhoPng)) {
                throw new \Exception("Arquivo final não foi gerado. Saida: {$saida}");
            }

            @chmod($caminhoPng, 0664);
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08B] PNG gerado com sucesso pelo Python!");
            return "storage/{$pasta}/{$arquivoPng}";

        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-08A] Playwright falhou — usando fallback HTML. Erro: " . $e->getMessage());
            return "storage/{$pasta}/{$arquivoHtml}";
        }
    }

    private function gerarPdf(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-07B] Preparando HTML para PDF...");
        $html = $this->getHtml($campaign, $produtos, $dadosLoja);
        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);

        $arquivoHtml = 'campanha_' . Str::random(5) . '_print.html';
        $caminhoHtml = storage_path("app/public/{$pasta}/{$arquivoHtml}");
        file_put_contents($caminhoHtml, $html);
        @chmod($caminhoHtml, 0664);

        $arquivoPdf = 'catalogo_' . Str::random(5) . '.pdf';
        $caminhoPdf = storage_path("app/public/{$pasta}/{$arquivoPdf}");

        try {
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08-PDF] Invocando Python (WeasyPrint) HTML→PDF...");
            $pythonBin = $this->encontrarBinario(['/usr/bin/python3', '/usr/bin/python', '/usr/local/bin/python3', 'python3', 'python']);
            if (!$pythonBin)
                throw new \Exception("Binário Python ausente");

            $script = base_path('render_weasyprint.py');
            $cmd = "cd " . escapeshellarg(base_path()) . " && {$pythonBin} " . escapeshellarg($script) . " " . escapeshellarg($caminhoHtml) . " " . escapeshellarg($caminhoPdf) . " pdf 2>&1";

            $saida = shell_exec($cmd);
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08-OUTPUT] {$saida}");
            if (!file_exists($caminhoPdf)) {
                throw new \Exception("Falha na geração: {$saida}");
            }

            @chmod($caminhoPdf, 0664);
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-08B-PDF] PDF gerado com sucesso pelo Python!");
            return "storage/{$pasta}/{$arquivoPdf}";

        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-PDFERR] Playwright PDF falhou. Usando fallback HTML alternativo: " . $e->getMessage());
            return "storage/{$pasta}/{$arquivoHtml}";
        }
    }

    private function gerarAudio(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        if (env('LOG_MAXDIVULGA', true))
            Log::info("[MAXDIVULGA-TTS] Iniciando geração de áudio via AIconect API...");

        // Prioriza a copy de locução que contém narração limpa sem símbolos. Fallback para social ou principal.
        $textoBase = $campaign->copy_locucao ?: ($campaign->copy_acompanhamento ?: ($campaign->copy ?? 'Olá! Confira as nossas novidades imperdíveis.'));

        // Limpar emojis e caracteres extras para ajudar a API de voz
        $textoLimpo = strip_tags($textoBase);
        $textoLimpo = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $textoLimpo); // Emoticons
        $textoLimpo = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $textoLimpo); // Simbolos e Pctograficos
        $textoLimpo = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $textoLimpo); // Transportes
        $textoLimpo = preg_replace('/[\x{1F700}-\x{1F77F}]/u', '', $textoLimpo); // Alquimicos
        $textoLimpo = preg_replace('/[\x{1F780}-\x{1F7FF}]/u', '', $textoLimpo); // Geometricos
        $textoLimpo = preg_replace('/[\x{1F800}-\x{1F8FF}]/u', '', $textoLimpo); // Setas
        $textoLimpo = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $textoLimpo); // Simbolos e Pctograficos Suplemento
        $textoLimpo = preg_replace('/[\x{1FA00}-\x{1FA6F}]/u', '', $textoLimpo); // Chess
        $textoLimpo = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $textoLimpo); // Misc symbols
        $textoLimpo = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $textoLimpo); // Dingbats

        // Remove cabeçalhos HEADLINE / SUBTITULO
        $textoLimpo = preg_replace('/HEADLINE:.*?\n/i', '', $textoLimpo);
        $textoLimpo = preg_replace('/SUBTITULO:.*?\n/i', '', $textoLimpo);
        $textoLimpo = trim($textoLimpo);

        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);
        $arquivoMp3 = 'locucao_' . Str::random(6) . '.mp3';
        $caminhoMp3 = storage_path("app/public/{$pasta}/{$arquivoMp3}");

        $voice = $campaign->voice ?? $this->config->tts_voice ?? 'pt-BR-FabioNeural';
        $speed = $campaign->audio_speed ?? $this->config->tts_default_speed ?? 1.25;
        $noiseScale = $campaign->noise_scale ?? $this->config->tts_default_noise_scale ?? 0.750;
        $noiseW = $campaign->noise_w ?? $this->config->tts_default_noise_w ?? 0.850;

        $host = trim($this->config->tts_host ?? 'http://192.168.1.13:5000/tts');
        // Sanitizar a URL garantindo que tenha rota (tts ou tts_mix)
        if (!Str::endsWith($host, 'tts') && !Str::endsWith($host, 'tts/') && !Str::endsWith($host, 'tts_mix')) {
            $host = rtrim($host, '/') . '/tts';
        }
        $key = $this->config->tts_api_key ?? 'A9fK3M7Q2Z8LxPR';

        $hasFundo = !empty($campaign->bg_audio);
        $bgPath = $hasFundo ? storage_path("app/public/audio/fundo/{$campaign->bg_audio}") : '';
        if ($hasFundo && !file_exists($bgPath)) {
            Log::warning("[MAXDIVULGA-TTS] Arquivo de fundo não encontrado: {$bgPath}. Gerando sem fundo.");
            $hasFundo = false;
        }

        try {
            if ($hasFundo) {
                // Geração Mixada (POST Multipart)
                // Se a URL original tiver "/tts", a trocamos logicamente por "/tts_mix"
                $hostMix = str_replace('/tts', '/tts_mix', $host);
                $urlFinal = $hostMix . '?key=' . urlencode($key);

                if (env('LOG_MAXDIVULGA', true))
                    Log::info("[MAXDIVULGA-TTS] Requisitando MIX (Com fundo): " . $urlFinal . " | Arquivo: " . $campaign->bg_audio);

                $response = \Illuminate\Support\Facades\Http::timeout(180)
                    ->attach('bg_audio', fopen($bgPath, 'r'), $campaign->bg_audio)
                    ->post($urlFinal, [
                        'voice' => $voice,
                        'speed' => $speed,
                        'noise_scale' => $noiseScale,
                        'noise_w' => $noiseW,
                        'bg_volume' => $campaign->bg_volume ?? 0.20,
                        'text' => $textoLimpo, // a rota de mix costuma ler 'text' 
                    ]);
            } else {
                // Geração Seca (GET Parametrizado)
                $params = [
                    'q' => $textoLimpo,
                    'voice' => $voice,
                    'speed' => $speed,
                    'noise_scale' => $noiseScale,
                    'noise_w' => $noiseW,
                    'key' => $key,
                    'format' => 'mp3',
                    'play' => 0
                ];
                $fullUrl = $host . '?' . http_build_query($params);

                if (env('LOG_MAXDIVULGA', true))
                    Log::info("[MAXDIVULGA-TTS] Requisitando GET (Voz limpa): " . $fullUrl);

                $response = \Illuminate\Support\Facades\Http::timeout(120)->get($host, $params);
            }

            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-TTS-RESP] Status: " . $response->status() . " | Content-Type: " . $response->header('Content-Type'));
            if (env('LOG_MAXDIVULGA', true))
                Log::info("[MAXDIVULGA-TTS-RESP] Body (primeiros 50 chars): " . substr($response->body(), 0, 50));

            if ($response->successful()) {
                file_put_contents($caminhoMp3, $response->body());
                @chmod($caminhoMp3, 0664);

                if (env('LOG_MAXDIVULGA', true))
                    Log::info("[MAXDIVULGA-TTS-OK] Áudio salvo em: {$caminhoMp3}");
                return "storage/{$pasta}/{$arquivoMp3}";
            } else {
                throw new \Exception("A API Voice retornou status " . $response->status());
            }
        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-TTS-ERR] Falha ao gerar áudio final: " . $e->getMessage());
            return "erro_audio.mp3";
        }
    }
}
