<?php

namespace App\Services;

use App\Models\MaxDivulgaCampaign;
use App\Models\MaxDivulgaConfig;
use Illuminate\Support\Facades\Log;
// use Spatie\Browsershot\Browsershot; // Exemplo para futuro

class CatalogRendererService
{
    protected $config;

    public function __construct()
    {
        $this->config = MaxDivulgaConfig::first();
    }

    /**
     * Renderiza e gera o arquivo final dependendo do formato escolhido.
     */
    public function render(MaxDivulgaCampaign $campaign)
    {
        try {
            switch ($campaign->format) {
                case 'image':
                    return $this->generateImage($campaign);
                case 'pdf':
                    return $this->generatePdf($campaign);
                case 'audio':
                    return $this->generateAudio($campaign);
                default:
                    return $this->generateText($campaign);
            }
        } catch (\Exception $e) {
            Log::error("Erro ao renderizar campanha {$campaign->id}: " . $e->getMessage());
            return false;
        }
    }

    protected function generateImage(MaxDivulgaCampaign $campaign)
    {
        // View to HTML -> HTML to Image
        // Exemplo: Browsershot::html("view('theme')")->save('path/to/image.png');
        return 'public/maxdivulga/campaigns/image_' . $campaign->id . '.png';
    }

    protected function generatePdf(MaxDivulgaCampaign $campaign)
    {
        // View to PDF
        return 'public/maxdivulga/campaigns/pdf_' . $campaign->id . '.pdf';
    }

    protected function generateAudio(MaxDivulgaCampaign $campaign)
    {
        // Send generated copy to TTS Service
        return 'public/maxdivulga/campaigns/audio_' . $campaign->id . '.mp3';
    }

    protected function generateText(MaxDivulgaCampaign $campaign)
    {
        // Retornar próprio texto gerado.
        return 'Texto Promocional: ...';
    }
}
