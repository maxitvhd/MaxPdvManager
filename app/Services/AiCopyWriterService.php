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
     * Envia um prompt para a IA e retorna sempre uma string.
     */
    private function chamarIA(string $prompt): string
    {
        if (!$this->config || !$this->config->api_key_ia) {
            Log::warning('[MAXDIVULGA-IA] API Key não configurada. Modo fallback.');
            return '';
        }

        try {
            if ($this->config->provider_ia === 'gemini') {
                $response = Http::withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$this->config->model_ia}:generateContent?key={$this->config->api_key_ia}", [
                        'contents' => [['parts' => [['text' => $prompt]]]]
                    ]);
                return $response->json('candidates.0.content.parts.0.text') ?? '';
            } else {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->config->api_key_ia,
                ])->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->config->model_ia ?? 'gpt-3.5-turbo',
                            'messages' => [
                                ['role' => 'system', 'content' => 'Você é um redator de marketing para e-commerce e lojas físicas brasileiras.'],
                                ['role' => 'user', 'content' => $prompt]
                            ],
                            'max_tokens' => 300,
                        ]);
                return $response->json('choices.0.message.content') ?? '';
            }
        } catch (\Exception $e) {
            Log::error('[MAXDIVULGA-IA] Erro na chamada: ' . $e->getMessage());
            return '';
        }
    }

    /**
     * Gera o texto de COPYWRITING PRINCIPAL que vai dentro da arte (imagem/PDF).
     * É curto, direto, para caber no layout gráfico.
     * NÃO lista os produtos — eles já aparecem no layout.
     */
    public function generateCopy(array $products, string $persona): string
    {
        $listaPrecos = collect($products)->map(
            fn($p) =>
            "- {$p['nome']}: R$ {$p['preco_novo']} (de R$ {$p['preco_original']})"
        )->join("\n");

        $prompt = "Você é um copywriter profissional de marketing direto.
Crie UMA FRASE DE CHAMADA IMPACTANTE (headline) e UM SUBTÍTULO CURTO para ser exibido em destaque no topo de um encarte/catálogo de ofertas.
Esses textos devem aparecer ANTES da lista de produtos.
NÃO liste os produtos no texto.
Persona/estilo: {$persona}.

Produtos do catálogo (escondam dos textos, são apenas contexto):
{$listaPrecos}

Retorne SOMENTE no formato:
HEADLINE: ...
SUBTITULO: ...";

        $resultado = $this->chamarIA($prompt);

        if (!$resultado) {
            return "HEADLINE: Ofertas Imperdíveis da Semana!\nSUBTITULO: Corra, são por tempo limitado!";
        }

        return $resultado;
    }

    /**
     * Gera o texto de ACOMPANHAMENTO SOCIAL — para ser colado no WhatsApp, Instagram, etc
     * junto com a imagem. É mais longo, com emojis, CTA, endereço e dados do mercado.
     */
    public function generateCopySocial(array $products, string $persona, array $dadosLoja): string
    {
        $listaPrecos = collect($products)->map(
            fn($p) =>
            "• {$p['nome']}: R$ {$p['preco_novo']}"
        )->join("\n");

        $lojaNome = $dadosLoja['nome'] ?? 'Nossa Loja';
        $contato = $dadosLoja['telefone'] ?? '';
        $endereco = $dadosLoja['endereco'] ?? '';

        $prompt = "Você é um assistente de marketing de uma loja física.
Escreva um texto de ACOMPANHAMENTO da imagem, para enviar no WhatsApp e Instagram Stories.
Persona/Estilo: {$persona}.
Deve ser animado, com emojis, urgência, lista curta de destaques e finalizar com o CTA e contato da loja.

Dados da loja:
- Nome: {$lojaNome}
- Contato: {$contato}
- Endereço: {$endereco}

Produtos em oferta:
{$listaPrecos}

Retorne apenas o texto formatado, pronto para copiar e colar.";

        $resultado = $this->chamarIA($prompt);

        if (!$resultado) {
            $lista = collect($products)->map(fn($p) => "• {$p['nome']} ~ R\$ {$p['preco_novo']}")->join("\n");
            return "🔥 OFERTAS DA SEMANA em {$lojaNome}! 🔥\n\n{$lista}\n\n📍 {$endereco}\n📞 {$contato}\n\n👉 Corra enquanto durar o estoque!";
        }

        return $resultado;
    }
}
