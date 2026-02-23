<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Folheto de Ofertas Azul Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">

    @php
        $qty = count($produtos);
        // Define colunas
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

        // Tamanho do header e copy conforme quantidade
        $headerH = ($qty > 12) ? 180 : ($qty > 8 ? 230 : 280);
        $copyH = ($qty > 12) ? 70 : ($qty > 6 ? 85 : 105);
        $footerH = ($qty > 12) ? 130 : 165;
        $rodapeH = 42;

        // Área disponível para o grid
        $gridH = 1920 - $headerH - $copyH - $footerH - $rodapeH;
        $rowH = floor($gridH / $rows); // px por linha

        // Escala de fontes baseada na altura disponível por card
        $cardH = $rowH; // px
        $tagFs = max(0.58, min(0.85, $cardH / 280));
        $nomeFs = max(0.70, min(1.15, $cardH / 220));
        $deFs = max(0.62, min(0.95, $cardH / 280));
        $precoFs = max(1.10, min(2.50, $cardH / 130));
        $imgMaxH = max(55, min(160, intval($cardH * 0.42)));
        $pad = max(7, min(16, intval($cardH * 0.055)));

        $headerFontB = ($qty > 12) ? '3.8rem' : ($qty > 8 ? '4.8rem' : '5.5rem');
        $headerFontS = ($qty > 12) ? '2.2rem' : ($qty > 8 ? '2.7rem' : '3rem');
        $copyFontH = ($qty > 12) ? '1.4rem' : '2rem';
        $copyFontS = ($qty > 12) ? '0.90rem' : '1.2rem';
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
            --amarelo: #FFD700;
            --branco: #ffffff;
            --cinza: #1a1a2e;
        }

        html,
        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--azul-vivo);
            width: 1080px;
            height: 1920px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--cinza);
        }

        /* ──────── HEADER ──────── */
        .header {
            height:
                {{ $headerH }}
                px;
            min-height:
                {{ $headerH }}
                px;
            flex-shrink: 0;
            background: linear-gradient(135deg, var(--azul-escuro) 0%, var(--azul-vivo) 60%, var(--azul-claro) 100%);
            color: var(--amarelo);
            display: flex;
            align-items: center;
            padding: 20px 40px;
            border-bottom: 6px solid var(--amarelo);
            box-shadow: 0 5px 20px rgba(0, 0, 0, .3);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 215, 0, .08);
        }

        .header-logo {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-logo img {
            max-height:
                {{ intval($headerH * 0.67) }}
                px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(2px 2px 6px rgba(0, 0, 0, .4));
        }

        .header-logo .nome-loja {
            background: var(--azul-escuro);
            padding: 10px 24px;
            border-radius: 50px;
            font-size: 1.7rem;
            font-weight: 900;
            text-transform: uppercase;
            color: var(--branco);
            border: 3px solid var(--amarelo);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, .4);
        }

        .tagline {
            font-size: 1rem;
            color: var(--amarelo);
            margin-top: 8px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
        }

        .header-info {
            flex: 2;
            padding-left: 20px;
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
                {{ $headerFontB }}
            ;
            line-height: .9;
            text-transform: uppercase;
            text-shadow: 3px 3px 0 var(--azul-escuro), 6px 6px 0 rgba(0, 0, 0, .3), 8px 8px 12px rgba(0, 0, 0, .4);
            margin-bottom: 4px;
        }

        .header-sub {
            font-size:
                {{ $headerFontS }}
            ;
            font-weight: 900;
            color: var(--amarelo);
            text-transform: uppercase;
            text-shadow: 3px 3px 0 var(--azul-escuro), 5px 5px 8px rgba(0, 0, 0, .4);
            margin-bottom: 10px;
        }

        .header-data {
            font-size: 1.1rem;
            color: var(--branco);
            font-weight: 700;
            background: rgba(0, 0, 0, .3);
            padding: 7px 20px;
            border-radius: 50px;
            border: 2px solid var(--amarelo);
            display: inline-block;
        }

        /* ──────── FAIXA COPY ──────── */
        .faixa-copy {
            height:
                {{ $copyH }}
                px;
            min-height:
                {{ $copyH }}
                px;
            flex-shrink: 0;
            background: linear-gradient(to bottom, var(--amarelo), #ffca00);
            color: var(--azul-escuro);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 30px;
            border-bottom: 3px solid #ffc107;
        }

        .faixa-copy .headline {
            font-size:
                {{ $copyFontH }}
            ;
            font-weight: 900;
            text-transform: uppercase;
            line-height: 1.05;
        }

        .faixa-copy .subtitulo {
            font-size:
                {{ $copyFontS }}
            ;
            font-weight: 800;
            margin-top: 4px;
        }

        /* ──────── WRAPPER BRANCO ──────── */
        .main-content-wrapper {
            background: #EFF4FF;
            margin: 0 22px;
            display: flex;
            flex-direction: column;
            flex: 1;
            border-radius: 18px 18px 0 0;
            overflow: hidden;
        }

        /* ──────── GRID ──────── */
        .grid-produtos {
            flex: 1;
            display: grid;
            grid-template-columns: repeat({{ $cols }}, 1fr);
            grid-auto-rows:
                {{ $rowH }}
                px;
            gap:
                {{ max(6, intval($rowH * 0.03)) }}
                px
                {{ max(6, intval($rowH * 0.03)) }}
                px;
            padding:
                {{ max(6, intval($rowH * 0.04)) }}
                px
                {{ max(8, 20 - $cols) }}
                px;
            align-content: start;
        }

        /* ──────── CARD ──────── */
        .card {
            background: var(--branco);
            border:
                {{ max(2, intval($rowH / 90)) }}
                px solid var(--azul-vivo);
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
            box-shadow: 0 4px 14px rgba(0, 87, 184, .12);
            height: 100%;
        }

        .tag-oferta {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, var(--azul-vivo), var(--azul-escuro));
            color: var(--branco);
            font-size:
                {{ round($tagFs * 100) / 100 }}
                rem;
            font-weight: 900;
            padding:
                {{ max(3, intval($pad * 0.4)) }}
                px
                {{ max(6, intval($pad * 0.9)) }}
                px;
            text-transform: uppercase;
            border-radius: 0
                {{ max(7, intval($rowH / 28)) }}
                px 0
                {{ max(6, intval($rowH / 32)) }}
                px;
            z-index: 2;
        }

        .card-topo {
            width: 100%;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            padding:
                {{ max(3, intval($pad * 0.5)) }}
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

        .card-nome {
            font-size:
                {{ round($nomeFs * 100) / 100 }}
                rem;
            font-weight: 800;
            color: var(--cinza);
            text-align: center;
            line-height: 1.05;
            margin-bottom:
                {{ max(2, intval($pad * 0.35)) }}
                px;
            text-transform: uppercase;
            flex-shrink: 0;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }

        .card-preco-de {
            font-size:
                {{ round($deFs * 100) / 100 }}
                rem;
            color: #888;
            text-decoration: line-through;
            margin-bottom:
                {{ max(2, intval($pad * 0.3)) }}
                px;
            font-weight: 600;
            flex-shrink: 0;
        }

        .card-preco-por {
            background: linear-gradient(to bottom, var(--azul-vivo), var(--azul-escuro));
            border:
                {{ max(2, intval($rowH / 90)) }}
                px solid var(--amarelo);
            color: var(--branco);
            font-weight: 900;
            font-size:
                {{ round($precoFs * 100) / 100 }}
                rem;
            text-align: center;
            padding:
                {{ max(3, intval($pad * 0.45)) }}
                px
                {{ max(5, intval($pad * 0.7)) }}
                px;
            border-radius:
                {{ max(6, intval($rowH / 36)) }}
                px;
            width: 100%;
            line-height: 1;
            box-shadow: 0 3px 0 var(--azul-escuro), 0 5px 10px rgba(0, 0, 0, .18);
            letter-spacing: -1px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        /* ──────── RODAPÉ ──────── */
        .faixa-endereco {
            height:
                {{ $footerH }}
                px;
            min-height:
                {{ $footerH }}
                px;
            flex-shrink: 0;
            background: linear-gradient(to right, var(--azul-escuro), var(--azul-vivo), var(--azul-escuro));
            color: var(--branco);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 6px;
            border-top: 5px solid var(--amarelo);
            text-align: center;
            padding: 0 35px;
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
            background: var(--azul-escuro);
            color: rgba(255, 255, 255, .6);
            font-size: .75rem;
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
                            <img src="https://placehold.co/200x200/e0eeff/4477cc?text=Produto" style="opacity:.35">
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
        @if(!empty($loja['cnpj']))
            <div style="opacity:.8;font-size:.95rem;">CNPJ: {{ $loja['cnpj'] }}</div>
        @endif
    </div>

    <div class="rodape-sistema">
        <span>Campanha Nº {{ $campaign->id ?? '000' }}</span>
        <span>Powered by MaxCheckout</span>
    </div>

</body>

</html>