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
            Log::info("[MAXDIVULGA-07] Iniciando renderização Playwright. Formato: " . $campaign->format);
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
            Log::info("[MAXDIVULGA-08] Invocando Python (WeasyPrint+PyMuPDF) HTML→PNG...");

            $pythonBin = $this->encontrarBinario(['/usr/bin/python3', '/usr/bin/python', '/usr/local/bin/python3', 'python3', 'python']);
            if (!$pythonBin)
                throw new \Exception("Binário do Python não encontrado");

            $script = base_path('render_weasyprint.py');
            if (!file_exists($script))
                throw new \Exception("Script {$script} ausente");

            $cmd = "cd " . escapeshellarg(base_path()) . " && {$pythonBin} " . escapeshellarg($script) . " " . escapeshellarg($caminhoHtml) . " " . escapeshellarg($caminhoPng) . " image 2>&1";

            Log::info("[MAXDIVULGA-08-CMD] Executando: {$cmd}");
            $saida = shell_exec($cmd);
            Log::info("[MAXDIVULGA-08-OUTPUT] {$saida}");

            if (!file_exists($caminhoPng)) {
                throw new \Exception("Arquivo final não foi gerado. Saida: {$saida}");
            }

            @chmod($caminhoPng, 0664);
            Log::info("[MAXDIVULGA-08B] PNG gerado com sucesso pelo Python!");
            return "storage/{$pasta}/{$arquivoPng}";

        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-08A] Playwright falhou — usando fallback HTML. Erro: " . $e->getMessage());
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

        $arquivoPdf = 'catalogo_' . Str::random(5) . '.pdf';
        $caminhoPdf = storage_path("app/public/{$pasta}/{$arquivoPdf}");

        try {
            Log::info("[MAXDIVULGA-08-PDF] Invocando Python (WeasyPrint) HTML→PDF...");
            $pythonBin = $this->encontrarBinario(['/usr/bin/python3', '/usr/bin/python', '/usr/local/bin/python3', 'python3', 'python']);
            if (!$pythonBin)
                throw new \Exception("Binário Python ausente");

            $script = base_path('render_weasyprint.py');
            $cmd = "cd " . escapeshellarg(base_path()) . " && {$pythonBin} " . escapeshellarg($script) . " " . escapeshellarg($caminhoHtml) . " " . escapeshellarg($caminhoPdf) . " pdf 2>&1";

            $saida = shell_exec($cmd);
            Log::info("[MAXDIVULGA-08-OUTPUT] {$saida}");
            if (!file_exists($caminhoPdf)) {
                throw new \Exception("Falha na geração: {$saida}");
            }

            @chmod($caminhoPdf, 0664);
            Log::info("[MAXDIVULGA-08B-PDF] PDF gerado com sucesso pelo Python!");
            return "storage/{$pasta}/{$arquivoPdf}";

        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-PDFERR] Playwright PDF falhou. Usando fallback HTML alternativo: " . $e->getMessage());
            return "storage/{$pasta}/{$arquivoHtml}";
        }
    }

    private function gerarAudio(MaxDivulgaCampaign $campaign, $produtos): string
    {
        // TODO: Integração TTS
        return 'audio_pendente.mp3';
    }
}
