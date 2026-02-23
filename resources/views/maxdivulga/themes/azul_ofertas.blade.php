<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folheto de Ofertas Azul Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">

    @php
        $qty = count($produtos);
        // Define colunas e escala com base na quantidade
        if ($qty <= 2) {
            $cols = $qty;
            $scale = 1.3;
        } elseif ($qty <= 4) {
            $cols = 2;
            $scale = 1.1;
        } elseif ($qty <= 6) {
            $cols = 3;
            $scale = 1.0;
        } elseif ($qty <= 9) {
            $cols = 3;
            $scale = 0.9;
        } elseif ($qty <= 12) {
            $cols = 4;
            $scale = 0.78;
        } elseif ($qty <= 16) {
            $cols = 4;
            $scale = 0.65;
        } else {
            $cols = 4;
            $scale = 0.55;
        }
        $cardPadding    = round(12 * $scale) . 'px';
        $imgMinH        = round(85 * $scale) . 'px';
        $imgMaxH        = round(125 * $scale) . 'px';
        $tagFontSize    = round(0.72 * $scale * 10) / 10 . 'rem';
        $nomeFont       = round(1.0 * $scale * 10) / 10 . 'rem';
        $deFont         = round(0.82 * $scale * 10) / 10 . 'rem';
        $precoFont      = round(1.75 * $scale * 10) / 10 . 'rem';
        $gridGap        = round(13 * $scale) . 'px';
        $gridPadding    = $qty > 12 ? '14px 16px' : '20px 28px';
    @endphp

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --azul-vivo: #0057B8;
            --azul-escuro: #003A7A;
            --azul-claro: #1A7FE8;
            --amarelo-ouro: #FFD700;
            --amarelo-escuro: #FFC107;
            --branco: #ffffff;
            --cinza-texto: #1a1a2e;
            --borda-card: #FFD700;
        }

        html, body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--azul-vivo);
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
            color: var(--cinza-texto);
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, var(--azul-escuro) 0%, var(--azul-vivo) 60%, var(--azul-claro) 100%);
            color: var(--amarelo-ouro);
            display: flex;
            align-items: center;
            min-height: {{ $qty > 12 ? '190px' : '270px' }};
            padding: {{ $qty > 12 ? '18px 35px' : '30px 40px' }};
            border-bottom: 6px solid var(--amarelo-ouro);
            box-shadow: 0 5px 20px rgba(0,0,0,0.3);
            position: relative;
            z-index: 10;
            overflow: hidden;
            flex-shrink: 0;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 300px; height: 300px;
            border-radius: 50%;
            background: rgba(255,215,0,0.08);
        }

        .header-logo {
            flex: 1;
            padding-right: 25px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-logo img {
            max-height: {{ $qty > 12 ? '130px' : '180px' }};
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(2px 2px 6px rgba(0,0,0,0.4));
        }

        .header-logo .nome-loja {
            background: var(--azul-escuro);
            padding: 10px 25px;
            border-radius: 50px;
            font-size: {{ $qty > 12 ? '1.4rem' : '1.8rem' }};
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            display: inline-block;
            border: 3px solid var(--amarelo-ouro);
            box-shadow: 5px 5px 15px rgba(0,0,0,0.4);
        }

        .header-logo .tagline {
            font-size: {{ $qty > 12 ? '0.9rem' : '1.1rem' }};
            color: var(--amarelo-ouro);
            margin-top: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 1px 1px 4px rgba(0,0,0,0.5);
        }

        .header-info {
            flex: 2;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding-left: 20px;
            position: relative;
            z-index: 1;
        }

        .header-badge {
            display: inline-block;
            color: var(--branco);
            font-weight: 900;
            font-size: {{ $qty > 12 ? '3.8rem' : '5.5rem' }};
            line-height: 0.9;
            text-transform: uppercase;
            text-shadow: 3px 3px 0px var(--azul-escuro), 6px 6px 0px rgba(0,0,0,0.3), 8px 8px 12px rgba(0,0,0,0.4);
            margin-bottom: 4px;
        }

        .header-sub {
            font-size: {{ $qty > 12 ? '2.2rem' : '3rem' }};
            font-weight: 900;
            color: var(--amarelo-ouro);
            text-transform: uppercase;
            line-height: 1;
            text-shadow: 3px 3px 0px var(--azul-escuro), 5px 5px 8px rgba(0,0,0,0.4);
            margin-bottom: 12px;
        }

        .header-data {
            font-size: {{ $qty > 12 ? '1.0rem' : '1.2rem' }};
            color: var(--branco);
            font-weight: 700;
            background: rgba(0,0,0,0.3);
            padding: 7px 22px;
            border-radius: 50px;
            border: 2px solid var(--amarelo-ouro);
            box-shadow: 3px 3px 10px rgba(0,0,0,0.3);
        }

        /* Faixa Copy */
        .faixa-copy {
            background: linear-gradient(to bottom, var(--amarelo-ouro), #ffca00);
            color: var(--azul-escuro);
            padding: {{ $qty > 12 ? '10px 30px' : '20px 36px' }};
            text-align: center;
            border-bottom: 3px solid var(--amarelo-escuro);
            box-shadow: 0 5px 15px rgba(0,0,0,0.15);
            z-index: 2;
            flex-shrink: 0;
        }

        .faixa-copy .headline {
            font-size: {{ $qty > 12 ? '1.5rem' : '2.2rem' }};
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 1px 1px 0px rgba(255,255,255,0.6);
        }

        .faixa-copy .subtitulo {
            font-size: {{ $qty > 12 ? '0.95rem' : '1.25rem' }};
            font-weight: 800;
            color: var(--azul-escuro);
            margin-top: 5px;
        }

        /* Container principal */
        .main-content-wrapper {
            background: #F0F4FF;
            margin: 0 25px;
            display: flex;
            flex-direction: column;
            border-radius: 20px 20px 0 0;
            flex: 1;
        }

        /* Grid de Produtos */
        .grid-produtos {
            display: grid;
            grid-template-columns: repeat({{ $cols }}, 1fr);
            gap: {{ $gridGap }};
            padding: {{ $gridPadding }};
            background: #F0F4FF;
            align-content: start;
        }

        /* Card Azul */
        .card {
            background: var(--branco);
            border: {{ round(2.5 * $scale) }}px solid var(--azul-vivo);
            border-radius: {{ round(13 * $scale) }}px;
            overflow: hidden;         /* sem overflow visible - evita sobreposição de tags */
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: {{ $cardPadding }};
            box-shadow: 0 6px 18px rgba(0,87,184,0.12);
        }

        /* Tag OFERTA — cantoneira sem sair do card */
        .tag-oferta {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, var(--azul-vivo), var(--azul-escuro));
            color: var(--branco);
            font-size: {{ $tagFontSize }};
            font-weight: 900;
            padding: {{ round(5 * $scale) }}px {{ round(11 * $scale) }}px;
            text-transform: uppercase;
            border-radius: 0 {{ round(11 * $scale) }}px 0 {{ round(9 * $scale) }}px;
            z-index: 2;
        }

        /* Imagem */
        .card-topo {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: {{ $imgMinH }};
            max-height: {{ $imgMaxH }};
            padding: {{ round(5 * $scale) }}px;
            margin-bottom: {{ round(5 * $scale) }}px;
            overflow: hidden;
        }

        .card-topo img {
            max-width: 100%;
            max-height: {{ $imgMaxH }};
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.1));
        }

        /* Nome */
        .card-nome {
            font-size: {{ $nomeFont }};
            font-weight: 800;
            color: var(--cinza-texto);
            text-align: center;
            line-height: 1.1;
            margin-bottom: {{ round(5 * $scale) }}px;
            text-transform: uppercase;
        }

        /* Preço de */
        .card-preco-de {
            font-size: {{ $deFont }};
            color: #888;
            text-decoration: line-through;
            margin-bottom: {{ round(4 * $scale) }}px;
            font-weight: 600;
        }

        /* Preço por — fundo azul + borda amarela */
        .card-preco-por {
            background: linear-gradient(to bottom, var(--azul-vivo), var(--azul-escuro));
            border: {{ round(2.5 * $scale) }}px solid var(--amarelo-ouro);
            color: var(--branco);
            font-weight: 900;
            font-size: {{ $precoFont }};
            text-align: center;
            padding: {{ round(4 * $scale) }}px {{ round(8 * $scale) }}px;
            border-radius: {{ round(10 * $scale) }}px;
            width: 100%;
            line-height: 1;
            box-shadow: 0 4px 0 var(--azul-escuro), 0 6px 12px rgba(0,0,0,0.2);
            letter-spacing: -1px;
            white-space: nowrap;
            text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
        }

        /* Rodapé */
        .faixa-endereco {
            background: linear-gradient(to right, var(--azul-escuro), var(--azul-vivo), var(--azul-escuro));
            color: var(--branco);
            padding: {{ $qty > 12 ? '18px 30px' : '28px 40px' }};
            display: flex;
            flex-direction: column;
            gap: 8px;
            border-top: 6px solid var(--amarelo-ouro);
            text-align: center;
            box-shadow: 0 -5px 15px rgba(0,0,0,0.2);
            z-index: 10;
            flex-shrink: 0;
        }

        .faixa-endereco strong {
            font-size: {{ $qty > 12 ? '1.4rem' : '1.8rem' }};
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.4);
        }

        .faixa-endereco .info-row {
            font-size: {{ $qty > 12 ? '1.0rem' : '1.2rem' }};
            font-weight: 700;
        }

        .rodape-sistema {
            background: var(--azul-escuro);
            color: rgba(255,255,255,0.6);
            font-size: 0.78rem;
            padding: 10px 30px;
            display: flex;
            justify-content: space-between;
            text-transform: uppercase;
            letter-spacing: 1px;
            flex-shrink: 0;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-logo">
            @if(!empty($loja['logo_url']))
                <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}">
            @else
                <div class="nome-loja">{{ $loja['nome'] ?? 'Seu Mercado' }}</div>
            @endif
            <div class="tagline">Oferta de tirar o fôlego!</div>
        </div>
        <div class="header-info">
            <div>
                <div class="header-badge">OFERTA</div>
                <div class="header-sub">ESPECIAL</div>
                @php \Carbon\Carbon::setLocale('pt_BR'); @endphp
                <div class="header-data">VÁLIDO ATÉ:
                    {{ \Carbon\Carbon::now()->addDays(7)->translatedFormat('d \d\e F') }}</div>
            </div>
        </div>
    </div>

    <div class="main-content-wrapper">

        {{-- FAIXA COPY --}}
        @php
            $linhasCopy = [];
            if ($copyTexto) {
                preg_match('/HEADLINE:\s*(.+)/i', $copyTexto, $h);
                preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto, $s);
                $linhasCopy['headline'] = trim($h[1] ?? '');
                $linhasCopy['subtitulo'] = trim($s[1] ?? '');
            }
            $headline = $linhasCopy['headline'] ?? 'Economize de verdade nesta semana!';
            $subtitulo = $linhasCopy['subtitulo'] ?? 'Preços que cabem no seu bolso, qualidade que você merece.';
        @endphp

        <div class="faixa-copy">
            <div class="headline">{{ $headline }}</div>
            <div class="subtitulo">{{ $subtitulo }}</div>
        </div>

        <div class="grid-produtos">
            @forelse($produtos as $prod)
                <div class="card">
                    <div class="tag-oferta">OFERTA</div>
                    <div class="card-topo">
                        @if(!empty($prod['imagem_url']))
                            <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}">
                        @else
                            <img src="https://placehold.co/200x200/e0eeff/4477cc?text=Produto" style="opacity:0.35">
                        @endif
                    </div>
                    <div class="card-nome">{{ $prod['nome'] }}</div>
                    <div class="card-preco-de">de R$ {{ $prod['preco_original'] }}</div>
                    <div class="card-preco-por">R$ {{ $prod['preco_novo'] }}</div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px;color:#4477cc;font-size:1.5rem;">
                    Nenhum produto selecionado para exibir.
                </div>
            @endforelse
        </div>

    </div>

    <div class="faixa-endereco">
        <strong>{{ $loja['nome'] ?? 'Seu Mercado' }}</strong>
        <div class="info-row">
            📍 {{ $loja['endereco'] ?? 'Endereço da Loja' }} | 📞 {{ $loja['telefone'] ?? '(00) 0000-0000' }}
        </div>
        <div class="cnpj" style="opacity: 0.8;">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif</div>
    </div>

    <div class="rodape-sistema">
        <span>Campanha Nº {{ $campaign->id ?? '000' }}</span>
        <span>Powered by MaxCheckout</span>
    </div>

</body>

</html>