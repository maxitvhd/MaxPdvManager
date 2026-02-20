<?php

namespace App\Services;

use App\Models\MaxDivulgaConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiCopyWriterService
{
    protected $config;

    public function __construct()
    {
        $this->config = MaxDivulgaConfig::first();
    }

    /**
     * Gera o texto persuasivo baseado na persona e nos produtos.
     */
    public function generateCopy(array $products, string $persona)
    {
        if (!$this->config || !$this->config->api_key_ia) {
            Log::warning('MaxDivulga: API Key da IA não configurada.');
            return "Aproveite nossas ofertas exclusivas!"; // Fallback
        }

        // Simulação de chamada para OpenAI ou Gemini
        try {
            // $response = Http::withHeaders([...])->post('...', [...]);
            // return $response->json('choices.0.message.content');

            return "Oferta Imperdível! Especial para você com base no seu perfil ($persona). Corra antes que acabe!";
        } catch (\Exception $e) {
            Log::error('Erro ao gerar copy IA: ' . $e->getMessage());
            return "Aproveite nossas ofertas!";
        }
    }
}
