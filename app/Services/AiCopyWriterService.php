<?php

namespace App\Services;

use App\Models\MaxDivulgaConfig;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiCopyWriterService
{
    protected $config;
    protected $resolvedPersona = null;

    public function __construct()
    {
        $this->config = MaxDivulgaConfig::first();
    }

    /**
     * Antiga detecção dura de temas. No novo formato, vamos delegar isso à própria IA 
     * no prompt, garantindo precisão baseada em contexto sem regex falho.
     */
    public function detectarTema(array $products): string
    {
        return 'auto';
    }

    /**
     * Envia prompt para a IA e retorna string.
     */
    private function chamarIA(string $prompt, int $maxTokens = 400): string
    {
        if (!$this->config || !$this->config->api_key_ia) {
            Log::warning('[MAXDIVULGA-IA] API Key não configurada. Modo fallback.');
            return '';
        }

        try {
            if ($this->config->provider_ia === 'gemini') {
                $model = $this->config->model_ia ?? 'gemini-1.5-pro';
                $response = Http::timeout(30)->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->config->api_key_ia}", [
                        'contents' => [['parts' => [['text' => $prompt]]]],
                        'generationConfig' => ['maxOutputTokens' => $maxTokens, 'temperature' => 0.9],
                    ]);
                return $response->json('candidates.0.content.parts.0.text') ?? '';
            } else {
                $response = Http::timeout(30)->withHeaders([
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $this->config->api_key_ia,
                ])->post('https://api.openai.com/v1/chat/completions', [
                            'model' => $this->config->model_ia ?? 'gpt-3.5-turbo',
                            'temperature' => 0.9,
                            'messages' => [
                                ['role' => 'system', 'content' => 'Você é um dos maiores copywriters de marketing varejista do Brasil. Especialista em gatilhos psicológicos, escrita persuasiva e textos que geram desejo imediato de compra. Usa escassez, urgência, prova social, autoridade e reciprocidade.'],
                                ['role' => 'user', 'content' => $prompt],
                            ],
                            'max_tokens' => $maxTokens,
                        ]);
                return $response->json('choices.0.message.content') ?? '';
            }
        } catch (\Exception $e) {
            Log::error('[MAXDIVULGA-IA] Erro na chamada: ' . $e->getMessage());
            return '';
        }
    }

    private function getResolvedPersona(string $persona): string
    {
        if ($this->resolvedPersona)
            return $this->resolvedPersona;
        if (in_array(strtolower($persona), ['surpreendame', 'aleatorio'])) {
            $personas = ['urgencia', 'premium', 'mercado', 'emocional'];
            $this->resolvedPersona = $personas[array_rand($personas)];
        } else {
            $this->resolvedPersona = $persona;
        }
        return $this->resolvedPersona;
    }

    /**
     * Copy PRINCIPAL — headline + subtítulo para a ARTE (dentro da imagem/PDF).
     */
    public function generateCopy(array $products, string $persona): string
    {
        $listaContexto = collect($products)->take(10)->map(
            function ($p) {
                return $p['preco_novo'] !== $p['preco_original']
                    ? "  • {$p['nome']}: R$ {$p['preco_novo']} (era R$ {$p['preco_original']})"
                    : "  • {$p['nome']}: R$ {$p['preco_novo']}";
            }
        )->join("\n");

        $personaResolvida = $this->getResolvedPersona($persona);
        $personaInstrucao = $this->instrucaoPersona($personaResolvida);

        $prompt = <<<PROMPT
Você é um copywriter expert em marketing de varejo brasileiro com a seguinte PERSONA (Tom de Voz):
[ {$personaInstrucao} ]
REGRA #1: É OBRIGATÓRIO encarnar essa persona. O seu tom de voz é a absoluta prioridade!

MISSÃO: Criar UMA HEADLINE poderosa e UM SUBTÍTULO curto para exibir no TOPO de um encarte/catálogo de ofertas.

CONTEXTO DA CAMPANHA:
- ATENÇÃO LLM: Analise cuidadosamente a lista de produtos abaixo. O ramo de atuação da loja ou o segmento/nicho central da oferta DEVE ser DEDUZIDO e COMPREENDIDO exclusivamente a partir dessa listagem (ex: se só tem Queijo, Leite e Cafés, o tema da campanha é Café da Manhã/Padaria. Se há Perfumes e Cremes, o nicho é Farmácia/Perfumaria ou Presentes. Se há Papel Sulfite e Lápis, é Papelaria etc). Molde o texto pautado nesse norte de segmento identificado para se adequar a qualquer ramo empresarial dos nossos lojistas.

LISTA DE PRODUTOS DA OFERTA (Deduza o nicho olhando diretamente para eles):
{$listaContexto}

REGRAS ESTritas:
✅ Headline: impactante, máximo 8 palavras, MAIÚSCULAS onde for estratégico
✅ Subtítulo: complemente a headline reforçando a oportunidade baseada na categoria identificada, máximo 12 palavras
❌ NÃO mencione os nomes dos produtos (pois eles já ocupam a imagem do encarte)
❌ NÃO use hashtags, asteriscos ou formatação markdown (sem ** ** na headline)

Responda SOMENTE neste formato exato (sem chaves ou explicações extras):
HEADLINE: [sua headline aqui]
SUBTITULO: [seu subtítulo aqui]
PROMPT;

        $resultado = $this->chamarIA($prompt, 150);

        if (!$resultado) {
            return "HEADLINE: Ofertas Que Você Não Pode Deixar Passar!\nSUBTITULO: Preços especiais e produtos fresquinhos válidos enquanto durar o estoque.";
        }

        return $resultado;
    }

    /**
     * Copy SOCIAL — texto completo para WhatsApp / Instagram / Facebook.
     * Rico em gatilhos, emojis, CTA e identidade da loja.
     */
    public function generateCopySocial(array $products, string $persona, array $dadosLoja): string
    {
        $listaPrecos = collect($products)->take(8)->map(
            function ($p) {
                return $p['preco_novo'] !== $p['preco_original']
                    ? "{$p['nome']} — ✅ R$ {$p['preco_novo']} ~~de R$ {$p['preco_original']}~~"
                    : "{$p['nome']} — ✅ R$ {$p['preco_novo']}";
            }
        )->join("\n");

        $lojaNome = $dadosLoja['nome'] ?? 'Nossa Loja';
        $contato = $dadosLoja['telefone'] ?? '';
        $endereco = $dadosLoja['endereco'] ?? '';
        $cidade = $dadosLoja['cidade'] ?? '';
        $cnpj = !empty($dadosLoja['cnpj']) ? "CNPJ: {$dadosLoja['cnpj']}" : '';

        $personaResolvida = $this->getResolvedPersona($persona);
        $personaInstrucao = $this->instrucaoPersona($personaResolvida);

        $prompt = <<<PROMPT
Você é um copywriter especialista em marketing digital para varejo brasileiro com a seguinte PERSONA (Tom de Voz):
[ {$personaInstrucao} ]
REGRA #1: É OBRIGATÓRIO que você encarne essa persona em cada palavra do seu texto.

MISSÃO: Escrever o TEXTO LEGENDA (ACOMPANHAMENTO DA IMAGEM), perfeito para WhatsApp Business, Instagram Stories e Feed.

ANALISE O SEGMENTO DA LOJA: 
Abaixo envio a lista de produtos. Você DEVE deduzir qual o ramo de atuação (Padaria, Lanchonete, Mercado, Farmárcia, Casa de Materiais, Informática, etc) e escrever a copy perfeitamente coerente a esse negócio, usando EMOJIS adequados a essa percepção:

ESTRUTURA OBRIGATÓRIA (siga exatamente este esqueleto):
1. 🔥 ABERTURA — 1 linha poderosa com emojis e o gatilho da sua persona (Ex: urgência, empatia, luxo, etc).
2. 💬 CONEXÃO — Frase para ancorar o valor ou a oportunidade única baseada no tom escolhido.
3. 📋 LISTA DE PRODUTOS — Use a lista abaixo mantendo exatamente o formato dos preços.
4. ⏰ ESCASSEZ/FECHAMENTO — Avise do limite ou convide com autoridade.
5. 👉 CTA — Chamada clara e amigável/urgente para ação ("Manda mensagem", "Corre pra cá").
6. 📍 ASSINATURA — Nome da loja e contato.

DADOS DA LOJA (para a assinatura):
- Loja: {$lojaNome}
- WhatsApp/Fone: {$contato}
- Local: {$endereco}, {$cidade}
{$cnpj}

PRODUTOS EM OFERTA (Copiar Exatamente Esta Lista para a seção 3):
{$listaPrecos}

REGRAS:
✅ Mantenha o texto na faixa de 150 a 300 palavras.
✅ Use emojis, mas de forma agradável e visualmente espaçada.
✅ A Persona ({$personaResolvida}) define O JEITO que você escreve e os gatilhos mentais aplicados.
❌ NUNCA use asteriscos duplos (**) ou formatação markdown complexa. Deixe o texto cru, apenas com quebras de linha e emojis.
PROMPT;

        $resultado = $this->chamarIA($prompt, 600);

        if (!$resultado) {
            $lista = collect($products)->map(fn($p) => "▶ {$p['nome']} — R$ {$p['preco_novo']}")->join("\n");
            return "🔥 Chegou o momento das OFERTAS em {$lojaNome}!\n\n" .
                "Não conseguimos segurar mais! Os preços estão tão bons que você precisa ver pra acreditar 😱\n\n" .
                "🛒 CONFIRA AS OFERTAS:\n{$lista}\n\n" .
                "⏰ Estoque LIMITADO! Por ordem de chegada.\n\n" .
                "👉 Venha nos visitar ou chame no WhatsApp!\n" .
                "📍 {$endereco}, {$cidade}\n📞 {$contato}";
        }

        return $resultado;
    }

    /**
     * Copy LOCUÇÃO — roteiro de rádio/som para a API de TTS ler em voz alta.
     * Números e símbolos por extenso, sem emojis, sem CNPJ.
     */
    public function generateCopyLocucao(array $products, string $persona, array $dadosLoja): string
    {
        $listaPrecos = collect($products)->take(8)->map(
            function ($p) {
                return $p['preco_novo'] !== $p['preco_original']
                    ? "Produto: {$p['nome']} (De {$p['preco_original']} reais POR APENAS {$p['preco_novo']} reais)."
                    : "Produto: {$p['nome']} por {$p['preco_novo']} reais.";
            }
        )->join("\n");

        $lojaNome = $dadosLoja['nome'] ?? 'Nossa Loja';

        $personaResolvida = $this->getResolvedPersona($persona);
        $personaInstrucao = $this->instrucaoPersona($personaResolvida);

        $prompt = <<<PROMPT
Você é um LOCUTOR ou ARTISTA DE VAREJO profissional, com a seguinte PERSONA (Tom de Voz):
[ {$personaInstrucao} ]
REGRA #1: É OBRIGATÓRIO encarnar essa persona em cada palavra. O texto DEVE SOAR NATURAL QUANDO LIDO EM VOZ ALTA por um sintatizador neural humano.

MISSÃO: Escrever o ROTEIRO DA GRAVAÇÃO DE ÁUDIO que será narrado pelas caixas de som da rua/shoppings.

CONTEXTO DA CAMPANHA:
- Analise inteligentemente a lista de oferta. Perceba sozinho em qual ramo de negócio o cliente pertence, e inicie/ancore o roteiro de locução compatível a essa vibração (ex: se é pastelaria, fale coisas sobre cheiro agradável, fome rápida etc).
- LOJA DA VEZ: {$lojaNome}

PRODUTOS EM OFERTA E PREÇOS ORAIS (leia e incorpore com maestria oral):
{$listaPrecos}

REGRAS VITAIS DE PRONÚNCIA:
❌ PROIBIDO usar NÚMEROS NUMÉRICOS (ex: 300, 5,96).
✅ OBRIGATÓRIO ESCREVER TODO E QUALQUER NÚMERO OU VALOR POR EXTENSO (ex: "trezentas gramas", "cinco reais e noventa e seis").
❌ PROIBIDO usar símbolos especiais (como R$, kg, %, *, #).
❌ PROIBIDO Emojis.
❌ PROIBIDO usar vocabulário chique, poético ou culto ("encantador"). 
✅ USE linguagem comercial comercial/popular e com gatilhos de rua. Ex: "Tá imperdível", "Corre pra aproveitar"!
✅ Mantenha um texto dinâmico (pausas com vírgulas e pontos).

Escreva apenas o roteiro narrativo final em parágrafo único, sem aspas, sem anotações secundárias de palco ou sonoplastia.
PROMPT;

        $resultado = $this->chamarIA($prompt, 600);

        if (!$resultado) {
            return "Atenção clientes de {$lojaNome}! Chegaram as ofertas do momento. Venham economizar de verdade e aproveitar nossos preços baixos. Corram antes que acabe!";
        }

        // Dupla garantia de limpeza para o TTS não bugar:
        $resultado = strip_tags($resultado);
        $resultado = preg_replace('/[\x{1F600}-\x{1F64F}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1F300}-\x{1F5FF}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1F680}-\x{1F6FF}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1F700}-\x{1F77F}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1F780}-\x{1F7FF}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1F800}-\x{1F8FF}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1F900}-\x{1F9FF}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{1FA00}-\x{1FA6F}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{2600}-\x{26FF}]/u', '', $resultado);
        $resultado = preg_replace('/[\x{2700}-\x{27BF}]/u', '', $resultado);

        return trim($resultado);
    }

    /**
     * Retorna a instrução de persona para o prompt.
     */
    private function instrucaoPersona(string $persona): string
    {
        return match ($persona) {
            'urgencia' => 'Urgência extrema e escassez. Tom imperativo, acelerado, quase gritando. Crie medo imediato de perder a oportunidade.',
            'premium' => 'Sofisticação e exclusividade. Tom elegante, confiante e aspiracional. Faça o cliente sentir que merece o melhor.',
            'mercado' => 'Locutor de varejão popular/calçadão. Tom animado, próximo, quase cantado/falado em voz alta. Use gírias locais e aproximação rápida.',
            'emocional' => 'Gatilho emocional profundo. Conecte os produtos com a dor/sentimento ou desejo familiar que motivou aquela compra.',
            default => 'Tom profissional, caloroso e persuasivo, voltado para negócios locais.',
        };
    }
}
