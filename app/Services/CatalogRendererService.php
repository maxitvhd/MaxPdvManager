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

    private function gerarImagem(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        Log::info("[MAXDIVULGA-07A] Preparando HTML para imagem...");
        $html = $this->getHtml($campaign, $produtos, $dadosLoja);
        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);
        $arquivo = 'imagem_' . Str::random(5) . '.png';
        $caminho = storage_path("app/public/{$pasta}/{$arquivo}");

        Log::info("[MAXDIVULGA-08] Invocando Browsershot HTML→PNG em: {$caminho}");

        try {
            $browsershot = Browsershot::html($html)
                ->windowSize(1080, 1920)
                ->deviceScaleFactor(2)
                ->noSandbox();

            if (file_exists('/usr/bin/node')) {
                $browsershot->setNodeBinary('/usr/bin/node');
            }
            if (file_exists('/usr/bin/npm')) {
                $browsershot->setNpmBinary('/usr/bin/npm');
            }

            $browsershot->save($caminho);

            @chmod($caminho, 0664);
            Log::info("[MAXDIVULGA-08B] PNG salvo com sucesso!");
        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-08A] Erro no Browsershot: " . $e->getMessage());
            throw $e;
        }

        return "storage/{$pasta}/{$arquivo}";
    }

    private function gerarPdf(MaxDivulgaCampaign $campaign, $produtos, array $dadosLoja): string
    {
        Log::info("[MAXDIVULGA-07B] Preparando HTML para PDF...");
        $html = $this->getHtml($campaign, $produtos, $dadosLoja);
        $pasta = $this->construirPastaSaida($campaign, $dadosLoja);
        $arquivo = 'catalogo_' . Str::random(5) . '.pdf';
        $caminho = storage_path("app/public/{$pasta}/{$arquivo}");

        try {
            $browsershot = Browsershot::html($html)
                ->format('A4')
                ->margins(10, 10, 10, 10)
                ->noSandbox();

            if (file_exists('/usr/bin/node')) {
                $browsershot->setNodeBinary('/usr/bin/node');
            }
            if (file_exists('/usr/bin/npm')) {
                $browsershot->setNpmBinary('/usr/bin/npm');
            }

            $browsershot->save($caminho);
            @chmod($caminho, 0664);
        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-PDFERR] Erro no Browsershot PDF: " . $e->getMessage());
            throw $e;
        }

        return "storage/{$pasta}/{$arquivo}";
    }

    private function gerarAudio(MaxDivulgaCampaign $campaign, $produtos): string
    {
        // TODO: Integração TTS
        return 'audio_pendente.mp3';
    }
}
