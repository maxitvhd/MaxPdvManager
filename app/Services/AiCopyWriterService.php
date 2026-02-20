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
            Log::warning('MaxDivulga: API Key da IA não configurada. Usando fallback.');
            return "Aproveite nossas ofertas exclusivas!"; // Fallback
        }

        $produtosText = collect($products)->map(function ($p) {
            return "- {$p['nome']}: R$ {$p['preco_novo']} (De: R$ {$p['preco_original']})";
        })->implode("\n");

        $prompt = "Atue como um copywriter profissional de marketing e crie um texto curto e persuasivo com base nos seguintes produtos:\n\n{$produtosText}\n\nA sua persona/estilo de comunicação deve ser: {$persona}. Seja direto ao ponto, dinâmico e crie escassez/urgência se for o caso.";

        try {
            if ($this->config->provider_ia === 'gemini') {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-pro:generateContent?key=" . $this->config->api_key_ia, [
                            'contents' => [
                                ['parts' => [['text' => $prompt]]]
                            ]
                        ]);
                $data = $response->json();
                return $data['candidates'][0]['content']['parts'][0]['text'] ?? "Ofertas exclusivas para você!";
            } else { // Padrão OpenAI
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->config->api_key_ia
                ])->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->config->model_ia ?? 'gpt-3.5-turbo',
                            'messages' => [
                                ['role' => 'system', 'content' => 'Você é um redator de marketing para e-commerce e lojas físicas.'],
                                ['role' => 'user', 'content' => $prompt]
                            ],
                            'max_tokens' => 200
                        ]);
                $data = $response->json();
                return $data['choices'][0]['message']['content'] ?? "Ofertas exclusivas para você!";
            }
        } catch (\Exception $e) {
            Log::error('Erro ao gerar copy IA: ' . $e->getMessage());
            return "Aproveite nossas super ofertas! Corra antes que acabe.";
        }
    }
}
