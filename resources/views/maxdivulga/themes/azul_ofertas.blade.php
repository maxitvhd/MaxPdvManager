<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Folheto de Ofertas Azul Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">

    @php
        $qty = count($produtos);

        // Número de colunas
        if ($qty <= 2) {
            $cols = $qty;
        } elseif ($qty <= 4) {
            $cols = 2;
        } elseif ($qty <= 9) {
            $cols = 3;
        } else {
            $cols = 4;
        }

        $rows = ceil($qty / $cols);

        // Alturas fixas de seções (conservadoras para não cortar o logo)
        $headerH = 260; // px — fixo para todos os casos
        $copyH = 90;  // px
        $footerH = 150; // px
        $rodapeH = 44;  // px

        // Gap e padding FIXOS — descontados do cálculo
        $gapPx = 10; // px entre cards
        $padTop = 14; // padding vertical do grid
        $padSide = 18; // padding horizontal do grid

        // Altura disponível para as linhas (descontando tudo)
        $gridH = 1920 - $headerH - $copyH - $footerH - $rodapeH;
        $rowsOnlyH = $gridH - ($rows - 1) * $gapPx - 2 * $padTop;
        $rowH = max(80, floor($rowsOnlyH / $rows));

        // Escalas de fonte proporcionais à altura das linhas
        $tagFs = round(max(0.58, min(0.92, $rowH / 275)) * 10) / 10;
        $nomeFs = round(max(0.72, min(1.25, $rowH / 200)) * 10) / 10;
        $deFs = round(max(0.62, min(0.98, $rowH / 275)) * 10) / 10;
        $precoFs = round(max(1.10, min(2.70, $rowH / 120)) * 10) / 10;
        $imgMaxH = max(50, min(170, intval($rowH * 0.45)));
        $pad = max(7, min(18, intval($rowH * 0.058)));

        // Fontes do header (reduzidas para qty alta)
        $hBadgeFs = ($qty > 12) ? '4rem' : '5.5rem';
        $hSubFs = ($qty > 12) ? '2.5rem' : '3rem';
        $copyHFs = ($qty > 12) ? '1.5rem' : '2.2rem';
        $copySFs = ($qty > 12) ? '0.95rem' : '1.3rem';
    @endphp

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --azul: #0057B8;
            --azulE: #003A7A;
            --azulC: #1A7FE8;
            --amar: #FFD700;
            --amarE: #FFC107;
            --branco: #ffffff;
            --cinza: #1a1a2e;
        }

        html,
        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--azul);
            width: 1080px;
            height: 1920px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--cinza);
        }

        /* ──── HEADER (altura fixa + overflow hidden) ──── */
        .header {
            height:
                {{ $headerH }}
                px;
            max-height:
                {{ $headerH }}
                px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--azulE) 0%, var(--azul) 60%, var(--azulC) 100%);
            color: var(--amar);
            display: flex;
            align-items: center;
            padding: 16px 38px;
            border-bottom: 6px solid var(--amar);
            box-shadow: 0 5px 18px rgba(0, 0, 0, .3);
            overflow: hidden;
            position: relative;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 260px;
            height: 260px;
            border-radius: 50%;
            background: rgba(255, 215, 0, .07);
        }

        .header-logo {
            flex: 0 0 auto;
            width: 310px;
            max-width: 310px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            /* garante que o logo não vaze */
            padding-right: 20px;
        }

        .header-logo img {
            max-width: 100%;
            max-height: 160px;
            /* limite absoluto */
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(2px 2px 5px rgba(0, 0, 0, .4));
            display: block;
        }

        .header-logo .nome-loja {
            background: var(--azulE);
            padding: 8px 20px;
            border-radius: 40px;
            font-size: 1.5rem;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            border: 3px solid var(--amar);
            box-shadow: 4px 4px 12px rgba(0, 0, 0, .4);
        }

        .tagline {
            font-size: 0.85rem;
            color: var(--amar);
            margin-top: 6px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-align: center;
        }

        .header-info {
            flex: 1;
            padding-left: 18px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            z-index: 1;
        }

        .header-badge {
            color: var(--branco);
            font-weight: 900;
            font-size:
                {{ $hBadgeFs }}
            ;
            line-height: .88;
            text-transform: uppercase;
            text-shadow: 2px 2px 0 var(--azulE), 5px 5px 0 rgba(0, 0, 0, .3), 7px 7px 10px rgba(0, 0, 0, .4);
            margin-bottom: 4px;
        }

        .header-sub {
            font-size:
                {{ $hSubFs }}
            ;
            font-weight: 900;
            color: var(--amar);
            text-transform: uppercase;
            text-shadow: 2px 2px 0 var(--azulE), 4px 4px 8px rgba(0, 0, 0, .4);
            margin-bottom: 10px;
        }

        .header-data {
            font-size: 1.1rem;
            color: var(--branco);
            font-weight: 700;
            background: rgba(0, 0, 0, .3);
            padding: 7px 20px;
            border-radius: 40px;
            border: 2px solid var(--amar);
            display: inline-block;
        }

        /* ──── FAIXA COPY ──── */
        .faixa-copy {
            height:
                {{ $copyH }}
                px;
            max-height:
                {{ $copyH }}
                px;
            flex-shrink: 0;
            background: linear-gradient(to bottom, var(--amar), #ffca00);
            color: var(--azulE);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 30px;
            border-bottom: 3px solid var(--amarE);
            overflow: hidden;
        }

        .faixa-copy .headline {
            font-size:
                {{ $copyHFs }}
            ;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.05;
        }

        .faixa-copy .subtitulo {
            font-size:
                {{ $copySFs }}
            ;
            font-weight: 800;
            margin-top: 3px;
        }

        /* ──── WRAPPER ──── */
        .main-content-wrapper {
            background: #EFF4FF;
            margin: 0 20px;
            flex: 1;
            min-height: 0;
            /* crucial para flex não vazar */
            display: flex;
            flex-direction: column;
            border-radius: 18px 18px 0 0;
            overflow: hidden;
        }

        /* ──── GRID ──── */
        .grid-produtos {
            flex: 1;
            min-height: 0;
            /* crucial */
            display: grid;
            grid-template-columns: repeat({{ $cols }}, 1fr);
            grid-auto-rows:
                {{ $rowH }}
                px;
            gap:
                {{ $gapPx }}
                px;
            padding:
                {{ $padTop }}
                px
                {{ $padSide }}
                px;
            align-content: start;
        }

        /* ──── CARD ──── */
        .card {
            background: var(--branco);
            border:
                {{ max(2, intval($rowH / 95)) }}
                px solid var(--azul);
            border-radius:
                {{ max(8, intval($rowH / 28)) }}
                px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding:
                {{ $pad }}
                px;
            box-shadow: 0 5px 14px rgba(0, 87, 184, .12);
        }

        /* Tag no canto — dentro do card, sem sair */
        .tag-oferta {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, var(--azul), var(--azulE));
            color: var(--branco);
            font-size:
                {{ $tagFs }}
                rem;
            font-weight: 900;
            padding:
                {{ max(3, intval($pad * .38)) }}
                px
                {{ max(7, intval($pad * .9)) }}
                px;
            text-transform: uppercase;
            border-radius: 0
                {{ max(7, intval($rowH / 28)) }}
                px 0
                {{ max(6, intval($rowH / 32)) }}
                px;
            z-index: 2;
        }

        /* Área da imagem */
        .card-topo {
            width: 100%;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding:
                {{ max(3, intval($pad * .45)) }}
                px;
        }

        .card-topo img {
            max-width: 100%;
            max-height:
                {{ $imgMaxH }}
                px;
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 3px 6px rgba(0, 0, 0, .1));
        }

        /* Nome */
        .card-nome {
            font-size:
                {{ $nomeFs }}
                rem;
            font-weight: 800;
            color: var(--cinza);
            text-align: center;
            line-height: 1.05;
            margin-bottom:
                {{ max(2, intval($pad * .32)) }}
                px;
            text-transform: uppercase;
            flex-shrink: 0;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        /* Preço de */
        .card-preco-de {
            font-size:
                {{ $deFs }}
                rem;
            color: #888;
            text-decoration: line-through;
            margin-bottom:
                {{ max(2, intval($pad * .28)) }}
                px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Preço por */
        .card-preco-por {
            background: linear-gradient(to bottom, var(--azul), var(--azulE));
            border:
                {{ max(2, intval($rowH / 95)) }}
                px solid var(--amar);
            color: var(--branco);
            font-weight: 900;
            font-size:
                {{ $precoFs }}
                rem;
            text-align: center;
            padding:
                {{ max(3, intval($pad * .42)) }}
                px
                {{ max(5, intval($pad * .72)) }}
                px;
            border-radius:
                {{ max(6, intval($rowH / 36)) }}
                px;
            width: 100%;
            line-height: 1;
            box-shadow: 0 3px 0 var(--azulE), 0 5px 10px rgba(0, 0, 0, .18);
            letter-spacing: -1px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ──── RODAPÉ ──── */
        .faixa-endereco {
            height:
                {{ $footerH }}
                px;
            max-height:
                {{ $footerH }}
                px;
            flex-shrink: 0;
            background: linear-gradient(to right, var(--azulE), var(--azul), var(--azulE));
            color: var(--branco);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 6px;
            border-top: 5px solid var(--amar);
            text-align: center;
            padding: 0 35px;
            overflow: hidden;
        }

        .faixa-endereco strong {
            font-size:
                {{ ($qty > 12) ? '1.5rem' : '1.9rem' }}
            ;
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, .4);
        }

        .faixa-endereco .info-row {
            font-size: 1.1rem;
            font-weight: 700;
        }

        .rodape-sistema {
            height:
                {{ $rodapeH }}
                px;
            flex-shrink: 0;
            background: var(--azulE);
            color: rgba(255, 255, 255, .6);
            font-size: .76rem;
            padding: 0 28px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            text-transform: uppercase;
            letter-spacing: 1px;
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
            <div class="header-badge">OFERTA</div>
            <div class="header-sub">ESPECIAL</div>
            @php \Carbon\Carbon::setLocale('pt_BR'); @endphp
            <div class="header-data">VÁLIDO ATÉ: {{ \Carbon\Carbon::now()->addDays(7)->translatedFormat('d \d\e F') }}
            </div>
        </div>
    </div>

    <div class="main-content-wrapper">
        @php
            $linhasCopy = [];
            if ($copyTexto) {
                preg_match('/HEADLINE:\s*(.+)/i', $copyTexto, $h);
                preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto, $s);
                $linhasCopy['headline'] = trim($h[1] ?? '');
                $linhasCopy['subtitulo'] = trim($s[1] ?? '');
            }
            $headline = $linhasCopy['headline'] ?? 'Economize de verdade nesta semana!';
            $subtitulo = $linhasCopy['subtitulo'] ?? 'Preços que cabem no seu bolso.';
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
                            <img src="https://placehold.co/200x200/e0eeff/4477cc?text=Produto" style="opacity:.3">
                        @endif
                    </div>
                    <div class="card-nome">{{ $prod['nome'] }}</div>
                    <div class="card-preco-de">de R$ {{ $prod['preco_original'] }}</div>
                    <div class="card-preco-por">R$ {{ $prod['preco_novo'] }}</div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:#4477cc;font-size:1.4rem;">
                    Nenhum produto para exibir.
                </div>
            @endforelse
        </div>
    </div>

    <div class="faixa-endereco">
        <strong>{{ $loja['nome'] ?? 'Seu Mercado' }}</strong>
        <div class="info-row">
            📍 {{ $loja['endereco'] ?? 'Endereço da Loja' }} | 📞 {{ $loja['telefone'] ?? '(00) 0000-0000' }}
        </div>
        @if(!empty($loja['cnpj']))
            <div style="opacity:.8;font-size:.9rem;">CNPJ: {{ $loja['cnpj'] }}</div>
        @endif
    </div>

    <div class="rodape-sistema">
        <span>Campanha Nº {{ $campaign->id ?? '000' }}</span>
        <span>Powered by MaxCheckout</span>
    </div>

</body>

</html>