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

    public function render(MaxDivulgaCampaign $campaign, $produtos)
    {
        try {
            Log::info("[MAXDIVULGA-07] CatalogRendererService: Iniciando conversão de formato: " . $campaign->format);
            switch ($campaign->format) {
                case 'image':
                    return $this->generateImage($campaign, $produtos);
                case 'pdf':
                    return $this->generatePdf($campaign, $produtos);
                case 'audio':
                    return $this->generateAudio($campaign, $produtos);
                default:
                    return $this->generateText($campaign, $produtos);
            }
        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-ERROR] Erro fatal CatalogRendererService (Campanha: {$campaign->id}): " . $e->getMessage());
            return false;
        }
    }

    protected function getHtml(MaxDivulgaCampaign $campaign, $produtos)
    {
        $theme = $campaign->theme;
        if (!$theme || !view()->exists($theme->path)) {
            throw new \Exception('Tema não encontrado: ' . ($theme->path ?? 'nenhum'));
        }
        return View::make($theme->path, ['campaign' => $campaign, 'produtos' => $produtos])->render();
    }

    protected function generateImage(MaxDivulgaCampaign $campaign, $produtos)
    {
        Log::info("[MAXDIVULGA-07A] Analisando Tema para Imagem...");
        $html = $this->getHtml($campaign, $produtos);
        $fileName = 'maxdivulga/campaigns/image_' . $campaign->id . '_' . Str::random(5) . '.png';
        $fullPath = storage_path('app/public/' . $fileName);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        Log::info("[MAXDIVULGA-08] Invocando pacote Spatie Browsershot para HTML->PNG...");
        try {
            $browsershot = Browsershot::html($html)
                ->windowSize(1080, 1920) // tamanho ideal para stories
                ->deviceScaleFactor(1.5)
                ->noSandbox();

            // Tenta detectar node e npm path se estiver em ambiente comum de servidor
            if (file_exists('/usr/bin/node')) {
                $browsershot->setNodeBinary('/usr/bin/node');
            }
            if (file_exists('/usr/bin/npm')) {
                $browsershot->setNpmBinary('/usr/bin/npm');
            }

            $browsershot->save($fullPath);
        } catch (\Exception $e) {
            Log::error("[MAXDIVULGA-08A] Erro ao executar Browsershot: " . $e->getMessage());
            throw $e;
        }

        Log::info("[MAXDIVULGA-08B] Imagem PNG salva com sucesso!");
        return 'storage/' . $fileName;
    }

    protected function generatePdf(MaxDivulgaCampaign $campaign, $produtos)
    {
        $html = $this->getHtml($campaign, $produtos);
        $fileName = 'maxdivulga/campaigns/pdf_' . $campaign->id . '_' . Str::random(5) . '.pdf';
        $fullPath = storage_path('app/public/' . $fileName);

        if (!is_dir(dirname($fullPath))) {
            mkdir(dirname($fullPath), 0755, true);
        }

        Browsershot::html($html)
            ->format('A4')
            ->margins(10, 10, 10, 10)
            ->noSandbox()
            ->save($fullPath);

        return 'storage/' . $fileName;
    }

    protected function generateAudio(MaxDivulgaCampaign $campaign, $produtos)
    {
        return 'audio_mock_para_implementar.mp3';
    }

    protected function generateText(MaxDivulgaCampaign $campaign, $produtos)
    {
        return $campaign->copy;
    }
}
