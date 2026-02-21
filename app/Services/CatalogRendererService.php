<?php

namespace App\Services;

use App\Models\MaxDivulgaCampaign;
use App\Models\MaxDivulgaConfig;
use Illuminate\Support\Facades\Log;
use Spatie\Browsershot\Browsershot;
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
            Log::info("[MAXDIVULGA-07] Iniciando renderização. Formato: " . $campaign->format);
            switch ($campaign->format) {
                case 'image':
                    return $this->gerarImagem($campaign, $produtos, $dadosLoja);
                case 'pdf':
                    return $this->gerarPdf($campaign, $produtos, $dadosLoja);
                case 'audio':
                    return $this->gerarAudio($campaign, $produtos);
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
        // Pasta: storage/app/public/lojas/{codigo}/campanhas/{tema}_{ddmmyyyy}/
        // Ex: lojas/y0yZBKLa/campanhas/cafe_da_manha_20022026
        $codigoLoja = $dadosLoja['codigo'] ?? 'sem-codigo';
        $tema = $dadosLoja['tema_campanha'] ?? 'catalogo_geral';
        $data = now()->format('dmY');
        $nomePasta = Str::slug("{$tema}_{$data}", '_');

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

    private function configurarBrowsershot(\Spatie\Browsershot\Browsershot $browsershot): \Spatie\Browsershot\Browsershot
    {
        // Caminhos dos binários node/npm
        $nodeBin = $this->encontrarBinario(['node', '/usr/local/bin/node', '/usr/bin/node']);
        $npmBin = $this->encontrarBinario(['npm', '/usr/local/bin/npm', '/usr/bin/npm']);

        if ($nodeBin)
            $browsershot->setNodeBinary($nodeBin);
        if ($npmBin)
            $browsershot->setNpmBinary($npmBin);

        // NODE_PATH apontando para o node_modules LOCAL do projeto (dentro da hospedagem)
        // Isso garante que o puppeteer instalado via npm ci seja encontrado
        $nodeModulesLocal = base_path('node_modules');
        if (is_dir($nodeModulesLocal)) {
            $browsershot->setEnvironmentOptions(['NODE_PATH' => $nodeModulesLocal]);
        }

        // Chromium path (comum em servidores Linux)
        $chromePaths = [
            '/usr/bin/chromium-browser',
            '/usr/bin/chromium',
            '/usr/bin/google-chrome',
            '/usr/bin/google-chrome-stable',
        ];
        foreach ($chromePaths as $path) {
            if (file_exists($path)) {
                $browsershot->setChromePath($path);
                break;
            }
        }

        return $browsershot;
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

    private function chromiumArgs(): array
    {
        // Flags essenciais para ambientes sem display/sandbox (hospedagem compartilhada, VPS, Docker)
        return [
            '--no-sandbox',
            '--disable-setuid-sandbox',
            '--disable-dev-shm-usage',    // usa /tmp em vez de /dev/shm (essencial em hospedagem)
            '--disable-gpu',              // sem aceleração gráfica (servidores sem GPU)
            '--no-zygote',               // não usa processo zygote
            '--disable-crash-reporter',  // desabilita relatório de crash (resolve crashpad)
            '--no-crashpad',             // desativa crashpad handler (resolve o erro atual)
            '--disable-extensions',
            '--disable-background-networking',
            '--disable-sync',
            '--metrics-recording-only',
            '--mute-audio',
        ];
    }

    private function gerarImagem(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        Log::info("[MAXDIVULGA-07A] Preparando HTML para imagem...");
        $html = $this->getHtml($campaign, $produtos, $dadosLoja);
        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);

        // Salva o HTML primeiro (sempre funciona, é o fallback)
        $arquivoHtml = 'campanha_' . Str::random(6) . '.html';
        $caminhoHtml = storage_path("app/public/{$pasta}/{$arquivoHtml}");
        file_put_contents($caminhoHtml, $html);
        @chmod($caminhoHtml, 0664);
        Log::info("[MAXDIVULGA-07A-HTML] HTML salvo: {$caminhoHtml}");

        // Tenta gerar PNG
        $arquivoPng = 'imagem_' . Str::random(6) . '.png';
        $caminhoPng = storage_path("app/public/{$pasta}/{$arquivoPng}");
        Log::info("[MAXDIVULGA-08] Gerando PNG: {$caminhoPng}");

        try {
            $nodeBin = $this->encontrarBinario(['/usr/local/bin/node', '/usr/bin/node', 'node']);
            $npmBin = $this->encontrarBinario(['/usr/local/bin/npm', '/usr/bin/npm', 'npm']);

            $chromePaths = [
                '/usr/bin/chromium',
                '/usr/bin/chromium-browser',
                '/usr/bin/google-chrome-stable',
                '/usr/bin/google-chrome',
            ];
            $chromePath = null;
            foreach ($chromePaths as $p) {
                if (file_exists($p)) {
                    $chromePath = $p;
                    break;
                }
            }

            $browsershot = Browsershot::html($html)
                ->windowSize(1080, 1920)
                ->deviceScaleFactor(2)
                ->setOption('args', $this->chromiumArgs());

            if ($chromePath)
                $browsershot->setChromePath($chromePath);
            if ($nodeBin)
                $browsershot->setNodeBinary($nodeBin);
            if ($npmBin)
                $browsershot->setNpmBinary($npmBin);

            // NODE_PATH apontando para node_modules LOCAL do projeto
            $nodeModules = base_path('node_modules');
            if (is_dir($nodeModules)) {
                $browsershot->setEnvironmentOptions(['NODE_PATH' => $nodeModules]);
            }

            $browsershot->save($caminhoPng);
            @chmod($caminhoPng, 0664);
            Log::info("[MAXDIVULGA-08B] PNG gerado com sucesso!");
            return "storage/{$pasta}/{$arquivoPng}";

        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-08A] Browsershot falhou — usando fallback HTML. Erro: " . $e->getMessage());
            // Retorna o HTML como fallback aplicável
            return "storage/{$pasta}/{$arquivoHtml}";
        }
    }

    private function gerarPdf(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        Log::info("[MAXDIVULGA-07B] Preparando HTML para PDF...");
        $html = $this->getHtml($campaign, $produtos, $dadosLoja);
        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);

        $arquivoHtml = 'campanha_' . Str::random(5) . '_print.html';
        $caminhoHtml = storage_path("app/public/{$pasta}/{$arquivoHtml}");
        file_put_contents($caminhoHtml, $html);
        @chmod($caminhoHtml, 0664);

        $arquivo = 'catalogo_' . Str::random(5) . '.pdf';
        $caminho = storage_path("app/public/{$pasta}/{$arquivo}");

        try {
            $nodeBin = $this->encontrarBinario(['/usr/local/bin/node', '/usr/bin/node', 'node']);
            $npmBin = $this->encontrarBinario(['/usr/local/bin/npm', '/usr/bin/npm', 'npm']);

            $chromePaths = [
                '/usr/bin/chromium',
                '/usr/bin/chromium-browser',
                '/usr/bin/google-chrome-stable',
                '/usr/bin/google-chrome',
            ];
            $chromePath = null;
            foreach ($chromePaths as $p) {
                if (file_exists($p)) {
                    $chromePath = $p;
                    break;
                }
            }

            $browsershot = Browsershot::html($html)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->setOption('args', $this->chromiumArgs());

            if ($chromePath)
                $browsershot->setChromePath($chromePath);
            if ($nodeBin)
                $browsershot->setNodeBinary($nodeBin);
            if ($npmBin)
                $browsershot->setNpmBinary($npmBin);

            $nodeModules = base_path('node_modules');
            if (is_dir($nodeModules)) {
                $browsershot->setEnvironmentOptions(['NODE_PATH' => $nodeModules]);
            }

            $browsershot->save($caminho);
            @chmod($caminho, 0664);
            return "storage/{$pasta}/{$arquivo}";

        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-PDFERR] Erro no Browsershot PDF. Usando fallback HTML alternativo: " . $e->getMessage());
            return "storage/{$pasta}/{$arquivoHtml}";
        }
    }

    private function gerarAudio(MaxDivulgaCampaign $campaign, $produtos): string
    {
        // TODO: Integração TTS
        return 'audio_pendente.mp3';
    }
}
