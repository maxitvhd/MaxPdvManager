<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folheto de Ofertas Vibrante</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Cores vibrantes baseadas nas referências */
            --vermelho-vivo: #E60012;
            --vermelho-escuro: #b30000;
            --amarelo-ouro: #FFD700;
            --amarelo-escuro: #FFC107;
            --branco: #ffffff;
            --cinza-texto: #333333;
            --borda-tracejada: #cccccc;
        }

        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--vermelho-vivo);
            width: 1080px;
            /* Altura fixa 1920px — todo conteúdo deve se encaixar exatamente aqui */
            height: 1920px;
            overflow: hidden;
            /* Garante que nada ultrapasse */
            display: flex;
            flex-direction: column;
            color: var(--cinza-texto);
            padding: 20px;
            /* Borda vermelha lateral */
        }

        /* Agrupa todo o conteúdo branco numa caixa unificada que preenche o espaço */
        .conteudo-principal {
            background: var(--branco);
            display: flex;
            flex-direction: column;
            flex: 1;
            /* Ocupa todo espaço disponível entre o padding */
            min-height: 0;
            overflow: hidden;
        }


        /* ===== HEADER IMPACTANTE ===== */
        .header {
            background: var(--vermelho-vivo);
            color: var(--amarelo-ouro);
            display: flex;
            align-items: center;
            min-height: 220px;
            padding: 20px;
            border-bottom: 4px solid var(--amarelo-ouro);
            /* Fundo decorativo sutil */
            background-image: radial-gradient(circle at 10% 20%, rgba(255, 215, 0, 0.2) 10px, transparent 11px),
                radial-gradient(circle at 90% 80%, rgba(255, 215, 0, 0.2) 20px, transparent 21px);
        }

        .header-logo {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            border-right: 2px dashed rgba(255, 215, 0, 0.3);
        }

        .header-logo .nome-loja {
            background: var(--vermelho-escuro);
            padding: 10px 25px;
            border-radius: 30px;
            font-size: 1.4rem;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            display: inline-block;
            box-shadow: 3px 3px 0px rgba(0, 0, 0, 0.2);
        }

        .header-logo .tagline {
            font-size: 1rem;
            color: var(--amarelo-ouro);
            margin-top: 12px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .header-info {
            flex: 2;
            padding: 20px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }

        .header-badge {
            display: inline-block;
            color: var(--branco);
            font-weight: 900;
            font-size: 3.8rem;
            /* Fonte bem grande */
            line-height: 1;
            text-transform: uppercase;
            text-shadow: 3px 3px 0px var(--vermelho-escuro);
            margin-bottom: 10px;
        }

        .header-sub {
            font-size: 2rem;
            font-weight: 900;
            color: var(--amarelo-ouro);
            text-transform: uppercase;
            text-shadow: 2px 2px 0px var(--vermelho-escuro);
            margin-bottom: 10px;
        }

        .header-data {
            font-size: 1.1rem;
            color: var(--branco);
            font-weight: 700;
            background: var(--vermelho-escuro);
            padding: 5px 15px;
            border-radius: 4px;
        }

        /* ===== FAIXA COPY (Amarela) ===== */
        .faixa-copy {
            background: var(--amarelo-ouro);
            color: var(--vermelho-vivo);
            padding: 20px 36px;
            text-align: center;
            border-bottom: 2px dashed var(--amarelo-escuro);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 2;
        }

        .faixa-copy .headline {
            font-size: 2rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.5);
        }

        .faixa-copy .subtitulo {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--vermelho-escuro);
            margin-top: 5px;
        }

        .label-secao {
            display: none;
            /* Ocultando pois não combina com o estilo vibrante */
        }

        /* ===== GRID PRODUTOS — totalmente dinâmico para 1080x1920 ===== */
        .grid-produtos {
            display: grid;
            gap: 14px;
            padding: 14px 20px;
            background: var(--branco);
            /* Flex grow ocupa o espaço restante entre header/copy/footer */
            flex: 1;
            min-height: 0;
            /* Alinha cards ao topo, não esticando */
            align-content: start;
        }

        /* 1 produto: 1 coluna centralizada — produto destaque grande */
        .grid-produtos.qty-1 {
            grid-template-columns: repeat(1, 1fr);
            align-content: center;
            padding: 30px 140px;
            /* Muito espaço nas laterais para produto grande */
        }

        /* 2 produtos: 2 colunas com produto grande */
        .grid-produtos.qty-2 {
            grid-template-columns: repeat(2, 1fr);
            align-content: center;
            padding: 30px 40px;
        }

        /* 3 produtos: 3 colunas */
        .grid-produtos.qty-3 {
            grid-template-columns: repeat(3, 1fr);
            align-content: center;
        }

        /* 4 produtos: 2x2 */
        .grid-produtos.qty-4 {
            grid-template-columns: repeat(2, 1fr);
            align-content: center;
        }

        /* 5-6 produtos: 3 colunas */
        .grid-produtos.qty-5,
        .grid-produtos.qty-6 {
            grid-template-columns: repeat(3, 1fr);
            align-content: start;
        }

        /* 7-9 produtos: 3 colunas, 3 linhas */
        .grid-produtos.qty-7,
        .grid-produtos.qty-8,
        .grid-produtos.qty-9 {
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            padding: 10px 16px;
            align-content: start;
        }

        /* 10-12 produtos: 4 colunas / 3 linhas */
        .grid-produtos.qty-10,
        .grid-produtos.qty-11,
        .grid-produtos.qty-12 {
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
            padding: 8px 12px;
            align-content: start;
        }

        /* 13+ (many): 4 colunas compactas */
        .grid-produtos.qty-many {
            grid-template-columns: repeat(4, 1fr);
            gap: 6px;
            padding: 6px 10px;
            align-content: start;
        }

        /* ===== CARD ===== */
        .card {
            background: var(--branco);
            border: 2px dashed var(--borda-tracejada);
            border-radius: 12px;
            overflow: visible;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 14px 10px 12px;
            box-shadow: none;
        }

        .card-destaque {
            background: var(--branco);
        }

        .card-topo {
            width: 100%;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            /* Alturas adaptáveis por quantidade */
            flex: 1;
            min-height: 80px;
        }

        /* Imagem maior com poucos produtos */
        .qty-1 .card-topo {
            min-height: 300px;
        }

        .qty-2 .card-topo {
            min-height: 220px;
        }

        .qty-3 .card-topo {
            min-height: 180px;
        }

        .qty-4 .card-topo {
            min-height: 150px;
        }

        .qty-5 .card-topo,
        .qty-6 .card-topo {
            min-height: 130px;
        }

        .qty-7 .card-topo,
        .qty-8 .card-topo,
        .qty-9 .card-topo {
            min-height: 100px;
        }

        .qty-10 .card-topo,
        .qty-11 .card-topo,
        .qty-12 .card-topo,
        .qty-many .card-topo {
            min-height: 80px;
        }

        .card-destaque .tag-oferta {
            background: var(--vermelho-vivo);
            color: var(--branco);
        }


        /* Etiqueta de Oferta Inclinada */
        .tag-oferta {
            position: absolute;
            top: -10px;
            right: -10px;
            width: auto;
            background: var(--vermelho-vivo);
            color: var(--branco);
            font-size: 0.8rem;
            font-weight: 900;
            padding: 6px 18px;
            text-transform: uppercase;
            transform: rotate(15deg);
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.2);
            z-index: 2;
            border-radius: 4px;
        }




        /* Nomes dos produtos */
        .card-nome {
            font-size: 1rem;
            font-weight: 700;
            color: var(--cinza-texto);
            text-align: center;
            padding: 4px 8px;
            line-height: 1.2;
            margin-bottom: 6px;
            text-transform: uppercase;
        }

        /* Fontes menores em grids compactos */
        .qty-10 .card-nome,
        .qty-11 .card-nome,
        .qty-12 .card-nome,
        .qty-many .card-nome {
            font-size: 0.75rem;
            padding: 2px 4px;
            margin-bottom: 3px;
        }

        .card-destaque .card-nome {
            color: var(--cinza-texto);
        }


        /* Preços */
        .card-preco-de {
            font-size: 0.9rem;
            color: #777;
            text-decoration: line-through;
            text-align: center;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .card-destaque .card-preco-de {
            color: #777;
        }

        /* Box de Preço Amarelo */
        .card-preco-por {
            background: var(--amarelo-ouro);
            border: 3px solid var(--amarelo-escuro);
            color: var(--vermelho-vivo);
            font-weight: 900;
            font-size: 1.8rem;
            /* Reduzido um pouco para caber melhor */
            text-align: center;
            padding: 8px 2px;
            border-radius: 12px;
            margin: 0 auto;
            width: 98%;
            line-height: 1;
            box-shadow: 0 4px 0 rgba(0, 0, 0, 0.1);
            letter-spacing: -1px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
        }

        /* Removido o first-letter que causava quebra de linha errática no WeasyPrint */

        .card-destaque .card-preco-por {
            background: var(--amarelo-ouro);
            color: var(--vermelho-vivo);
        }

        /* ===== FAIXA ENDEREÇO (Rodapé Vermelho) ===== */
        .faixa-endereco {
            background: var(--vermelho-vivo);
            color: var(--branco);
            padding: 25px 40px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: auto;
            border-top: 4px solid var(--amarelo-ouro);
            text-align: center;
        }

        .faixa-endereco strong {
            font-size: 1.5rem;
            text-transform: uppercase;
            font-weight: 900;
        }

        .faixa-endereco .info-row {
            font-size: 1rem;
            font-weight: 700;
        }

        /* ===== RODAPÉ DISCRETO ===== */
        .rodape-sistema {
            background: var(--vermelho-escuro);
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
            padding: 10px 28px;
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-logo">
            <div class="nome-loja">
                @if(!empty($loja['logo_url']))
                    <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}"
                        style="max-height:120px;max-width:240px;object-fit:contain;vertical-align:middle;">
                @else
                    {{ $loja['nome'] ?? 'Seu Mercado' }}
                @endif
            </div>
            <div class="tagline">Ofertas da Semana</div>
        </div>
        <div class="header-info">
            <div>
                <div class="header-badge">OFERTAS</div>
                <div class="header-sub">DA SEMANA</div>
                @php \Carbon\Carbon::setLocale('pt_BR'); @endphp
                <div class="header-data">VÁLIDO: {{ \Carbon\Carbon::now()->translatedFormat('d \d\e F \d\e Y') }}</div>
            </div>
        </div>
    </div>

    {{-- FAIXA COPY: Headline e subtítulo gerados pela IA (SEM listar os produtos aqui) --}}
    @php
        $linhasCopy = [];
        if ($copyTexto) {
            preg_match('/HEADLINE:\s*(.+)/i', $copyTexto, $h);
            preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto, $s);
            $linhasCopy['headline'] = trim($h[1] ?? '');
            $linhasCopy['subtitulo'] = trim($s[1] ?? '');
        }
        $headline = $linhasCopy['headline'] ?? 'Economia de verdade é aqui!';
        $subtitulo = $linhasCopy['subtitulo'] ?? 'Confira as promoções que preparamos para você.';
    @endphp

    <div class="faixa-copy">
        <div class="headline">{{ $headline }}</div>
        <div class="subtitulo">{{ $subtitulo }}</div>
    </div>

    <div class="label-secao">⭐ PROMOÇÕES SELECIONADAS ⭐</div>

    @php $qtyClass = count($produtos) <= 12 ? 'qty-' . count($produtos) : 'qty-many'; @endphp
    <div class="grid-produtos {{ $qtyClass }}">
        @forelse($produtos as $i => $prod)
            <div class="card {{ $i === 0 ? 'card-destaque' : '' }}">
                <div class="card-topo">
                    @if(!empty($prod['imagem_url']))
                        <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}"
                            style="max-height:160px;max-width:100%;object-fit:contain;">
                    @else
                        <img src="https://placehold.co/150x150/e0e0e0/999999?text=Oferta" style="opacity:0.3">
                    @endif
                </div>
                <div class="tag-oferta">OFERTA</div>
                <div class="card-nome">{{ $prod['nome'] }}</div>
                <div class="card-preco-de">de R$ {{ $prod['preco_original'] }}</div>
                <div class="card-preco-por">R$ {{ $prod['preco_novo'] }}</div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:40px;color:#999;">Nenhum produto selecionado.</div>
        @endforelse
    </div>

    <div class="faixa-endereco" style="margin-top:auto;">
        <strong>{{ $loja['nome'] ?? '' }}</strong>
        <div class="info-row">
            📍 {{ $loja['endereco'] ?? '' }} — {{ $loja['cidade'] ?? '' }}<br>
            📞 {{ $loja['telefone'] ?? '' }}
        </div>
        <div class="cnpj">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif</div>
    </div>

    <div class="rodape-sistema">
        <span>Nº {{ $campaign->id ?? '--' }} &nbsp;|&nbsp; {{ $campaign->name ?? '' }}</span>
        <span>Criado pelo MaxCheckout ✦ MaxDivulga</span>
    </div>

</body>

</html>