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
     * Detecta o tema da campanha com base nos nomes dos produtos.
     */
    public function detectarTema(array $products): string
    {
        $nomes = strtolower(implode(' ', array_column($products, 'nome')));

        $temas = [
            'acougue_frios' => ['carne', 'bife', 'suíno', 'suino', 'peixe', 'linguiça', 'linguica', 'salsicha', 'mortadela', 'presunto', 'coxão', 'patinho', 'músculo', 'costela', 'frango', 'coxa', 'sobrecoxa', 'bacon'],
            'hortifruti' => ['fruta', 'verdura', 'legume', 'tomate', 'batata', 'cebola', 'alface', 'cenoura', 'beterraba', 'banana', 'maçã', 'maca', 'laranja', 'limão', 'limao', 'uva', 'manga', 'abacate', 'mamão', 'mamao', 'melancia', 'melão'],
            'cafe_da_manha' => ['café', 'cafe', 'pão', 'pao', 'bolo', 'manteiga', 'queijo', 'leite', 'requeijão', 'requeijao', 'achocolatado', 'sucrilhos', 'granola', 'iogurte', 'tapioca', 'nescafe', 'capuccino', 'biscoito', 'bolacha'],
            'churrasco' => ['picanha', 'alcatra', 'contrafile', 'contrafilé', 'carvão', 'carvao', 'churrasqueira', 'espetinho', 'maminha', 'file', 'filé'],
            'almoco' => ['arroz', 'feijão', 'feijao', 'macarrão', 'macarrao', 'farofa', 'molho', 'azeite', 'mandioca', 'macaxeira', 'inhame', 'caldo', 'óleo', 'oleo'],
            'bebidas' => ['cerveja', 'refrigerante', 'suco', 'água', 'agua', 'vinho', 'vodka', 'whisky', 'dose', 'energético', 'energetico', 'isotônico', 'isotonco', 'kombucha'],
            'limpeza' => ['detergente', 'sabão', 'sabao', 'desinfetante', 'amaciante', 'alvejante', 'esponja', 'vassoura', 'balde', 'rodo', 'pano', 'água sanitária'],
            'padaria' => ['broa', 'baguete', 'ciabatta', 'croissant', 'bisnaguinha', 'forma', 'integral', 'brioche', 'salgado', 'coxinha', 'empada'],
        ];

        $pontuacao = [];
        foreach ($temas as $tema => $palavras) {
            $pontuacao[$tema] = 0;
            foreach ($palavras as $palavra) {
                if (str_contains($nomes, $palavra)) {
                    $pontuacao[$tema]++;
                }
            }
        }

        arsort($pontuacao);
        $melhor = array_key_first($pontuacao);

        // Só usa tema detectado se tiver pelo menos 2 matches
        return ($pontuacao[$melhor] >= 2) ? $melhor : 'catalogo_geral';
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
            fn($p) =>
            "  • {$p['nome']}: R$ {$p['preco_novo']} (era R$ {$p['preco_original']})"
        )->join("\n");

        $tema = $this->detectarTema($products);
        $contextoTema = $this->textoTema($tema);

        $personaResolvida = $this->getResolvedPersona($persona);
        $personaInstrucao = $this->instrucaoPersona($personaResolvida);

        $prompt = <<<PROMPT
Você é um copywriter expert em marketing de varejo brasileiro com a seguinte PERSONA (Tom de Voz):
[ {$personaInstrucao} ]
REGRA #1: É OBRIGATÓRIO que você encarne essa persona em cada palavra. O seu tom de voz é a prioridade absoluta.

MISSÃO: Criar UMA HEADLINE poderosa e UM SUBTÍTULO curto para exibir no TOPO de um encarte/catálogo de ofertas.

CONTEXTO DA CAMPANHA:
- Tema detectado da lista: {$contextoTema}
- Produtos em destaque (apenas para contexto, NÃO cite os nomes deles):
{$listaContexto}

REGRAS ESTritas:
✅ Headline: impactante, máximo 8 palavras, MAIÚSCULAS onde for estratégico
✅ Subtítulo: complemente a headline, máximo 12 palavras
❌ NÃO mencione os nomes dos produtos (pois eles já ocupam a imagem do encarte)
❌ NÃO use hashtags, asteriscos ou formatação markdown (sem ** ** na headline)

Responda SOMENTE neste formato exato (sem chaves ou explicações extras):
HEADLINE: [sua headline aqui]
SUBTITULO: [seu subtítulo aqui]
PROMPT;

        $resultado = $this->chamarIA($prompt, 150);

        if (!$resultado) {
            $fallbacks = [
                'cafe_da_manha' => "HEADLINE: O Café da Manhã Mais Gostoso da Cidade!\nSUBTITULO: Tudo fresquinho para começar seu dia com energia.",
                'churrasco' => "HEADLINE: CHURRASCO INESQUECÍVEL Te Espera!\nSUBTITULO: As melhores carnes, preços que cabem no bolso.",
                'bebidas' => "HEADLINE: Geladeira CHEIA Por Menos!\nSUBTITULO: Bebidas geladas com desconto imperdível.",
                'acougue_frios' => "HEADLINE: O Melhor do Açougue Especial Para Você!\nSUBTITULO: Carnes nobres e cortes frescos com ofertas imbatíveis.",
                'hortifruti' => "HEADLINE: Da Roça Direto Para Sua Mesa!\nSUBTITULO: Qualidade, sabor e saúde no nosso Hortifruti fresquinho.",
                'default' => "HEADLINE: Ofertas Que Você Não Pode Deixar Passar!\nSUBTITULO: Preços válidos enquanto durar o estoque.",
            ];
            return $fallbacks[$tema] ?? $fallbacks['default'];
        }

        return $resultado;
    }

    /**
     * Copy SOCIAL — texto completo para WhatsApp / Instagram / Facebook.
     * Rico em gatilhos, emojis, CTA e identidade da loja.
     */
    public function generateCopySocial(array $products, string $persona, array $dadosLoja): string
    {
        $tema = $this->detectarTema($products);
        $contextoTema = $this->textoTema($tema);
        $emojiTema = $this->emojiTema($tema);

        $listaPrecos = collect($products)->take(8)->map(
            fn($p) =>
            "{$emojiTema} {$p['nome']} — ✅ R$ {$p['preco_novo']} ~~de R$ {$p['preco_original']}~~"
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

TEMA DA CAMPANHA DE OFERTAS: {$contextoTema}

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
     * Retorna a instrução de persona para o prompt.
     */
    private function instrucaoPersona(string $persona): string
    {
        return match ($persona) {
            'urgencia' => 'Urgência extrema e escassez. Tom imperativo, acelerado, quase gritando. Crie medo imediato de perder a oportunidade.',
            'premium' => 'Sofisticação e exclusividade. Tom elegante, confiante e aspiracional. Faça o cliente sentir que merece o melhor.',
            'mercado' => 'Locutor de varejão popular. Tom animado, próximo, quase falado em voz alta. Use expressões do cotidiano.',
            'emocional' => 'Gatilho emocional profundo. Conecte os produtos com família, lar, economia do mês, momentos especiais e realização.',
            default => 'Tom profissional, caloroso e persuasivo, voltado para o varejo brasileiro.',
        };
    }

    /**
     * Descrição textual do tema detectado para enriquecer os prompts.
     */
    private function textoTema(string $tema): string
    {
        return match ($tema) {
            'cafe_da_manha' => 'Café da Manhã — produtos para um começo de dia especial e gostoso',
            'churrasco' => 'Churrasco / Almoço em Família — carnes, temperos e tudo para o churrasquinho',
            'acougue_frios' => 'Açougue e Frios — as melhores carnes, aves e cortes nobres para o dia a dia',
            'almoco' => 'Almoço do Dia a Dia — itens essenciais da mesa brasileira',
            'bebidas' => 'Bebidas — refrigerantes, cervejas e sucos para refrescar',
            'hortifruti' => 'Hortifruti — saúde, frutas, legumes e verduras fresquinhos que acabaram de chegar',
            'limpeza' => 'Limpeza e Higiene — produtos para deixar o lar brilhando',
            'padaria' => 'Padaria — pães, bolos e delícias artesanais',
            default => 'Catálogo Geral — grande variedade e ofertas pesadas para a família inteira',
        };
    }

    /**
     * Emoji representativo do tema.
     */
    private function emojiTema(string $tema): string
    {
        return match ($tema) {
            'cafe_da_manha' => '☕',
            'churrasco' => '🔥',
            'acougue_frios' => '🥩',
            'almoco' => '🍽️',
            'bebidas' => '🥤',
            'hortifruti' => '🥦',
            'limpeza' => '✨',
            'padaria' => '🍞',
            default => '🛒',
        };
    }
}
