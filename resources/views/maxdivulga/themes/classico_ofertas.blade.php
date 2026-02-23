<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Folheto de Ofertas Clássico</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">

    @php
        $qty = count($produtos);
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

        $headerH = ($qty > 12) ? 180 : ($qty > 8 ? 230 : 280);
        $copyH = ($qty > 12) ? 70 : ($qty > 6 ? 85 : 105);
        $footerH = ($qty > 12) ? 130 : 165;
        $rodapeH = 42;

        $gridH = 1920 - $headerH - $copyH - $footerH - $rodapeH;
        $rowH = floor($gridH / $rows);

        $tagFs = max(0.58, min(0.90, $rowH / 280));
        $nomeFs = max(0.72, min(1.20, $rowH / 210));
        $deFs = max(0.62, min(0.98, $rowH / 280));
        $precoFs = max(1.15, min(2.60, $rowH / 128));
        $imgMaxH = max(55, min(165, intval($rowH * 0.44)));
        $pad = max(8, min(17, intval($rowH * 0.06)));

        $headerFontB = ($qty > 12) ? '3.8rem' : ($qty > 8 ? '5rem' : '6.5rem');
        $headerFontS = ($qty > 12) ? '2.2rem' : ($qty > 8 ? '3rem' : '3.8rem');
        $copyFontH = ($qty > 12) ? '1.5rem' : '2.2rem';
        $copyFontS = ($qty > 12) ? '0.92rem' : '1.3rem';
    @endphp

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --vermelho: #E60012;
            --verm-esc: #A3000D;
            --amarelo: #FFD700;
            --amar-esc: #FFC107;
            --branco: #ffffff;
            --cinza: #333333;
        }

        html,
        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--vermelho);
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
            background: linear-gradient(to bottom, var(--vermelho), #d30011),
                radial-gradient(circle at 50% 0%, rgba(255, 215, 0, .1) 0%, transparent 60%);
            color: var(--amarelo);
            display: flex;
            align-items: center;
            gap: 30px;
            padding: 20px 45px;
            border-bottom: 8px solid var(--amarelo);
            box-shadow: 0 10px 25px rgba(0, 0, 0, .3);
            position: relative;
            z-index: 10;
        }

        .header-logo {
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header-logo img {
            max-height:
                {{ intval($headerH * 0.68) }}
                px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(4px 4px 6px rgba(0, 0, 0, .4));
        }

        .header-logo .nome-loja {
            background: var(--verm-esc);
            padding: 12px 30px;
            border-radius: 50px;
            font-size: 1.8rem;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            border: 4px solid var(--amarelo);
            box-shadow: 6px 6px 15px rgba(0, 0, 0, .4);
        }

        .tagline {
            font-size: 1.1rem;
            color: var(--amarelo);
            margin-top: 12px;
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
        }

        .header-badge {
            color: var(--branco);
            font-weight: 900;
            font-size:
                {{ $headerFontB }}
            ;
            line-height: .85;
            text-transform: uppercase;
            text-shadow: 2px 2px 0 var(--verm-esc), 4px 4px 0 var(--verm-esc), 6px 6px 0 rgba(0, 0, 0, .4), 8px 8px 12px rgba(0, 0, 0, .5);
            margin-bottom: 5px;
        }

        .header-sub {
            font-size:
                {{ $headerFontS }}
            ;
            font-weight: 900;
            color: var(--amarelo);
            text-transform: uppercase;
            text-shadow: 2px 2px 0 var(--verm-esc), 4px 4px 0 var(--verm-esc), 6px 6px 10px rgba(0, 0, 0, .5);
            margin-bottom: 15px;
        }

        .header-data {
            font-size: 1.2rem;
            color: var(--branco);
            font-weight: 700;
            background: rgba(0, 0, 0, .3);
            padding: 8px 22px;
            border-radius: 50px;
            border: 3px solid var(--amarelo);
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
            background: linear-gradient(to bottom, var(--amarelo), #ffc200);
            color: var(--vermelho);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 0 36px;
            border-bottom: 4px solid var(--amar-esc);
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
            color: var(--verm-esc);
            margin-top: 4px;
        }

        /* ──────── WRAPPER ──────── */
        .main-content-wrapper {
            background: var(--branco);
            margin: 0 25px;
            display: flex;
            flex-direction: column;
            flex: 1;
            border-radius: 25px 25px 0 0;
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
                {{ max(6, intval($rowH * 0.028)) }}
                px
                {{ max(6, intval($rowH * 0.028)) }}
                px;
            padding:
                {{ max(6, intval($rowH * 0.038)) }}
                px
                {{ max(10, 22 - $cols) }}
                px;
            align-content: start;
        }

        /* ──────── CARD ──────── */
        .card {
            background: var(--branco);
            border:
                {{ max(2, intval($rowH / 90)) }}
                px solid var(--amarelo);
            border-radius:
                {{ max(8, intval($rowH / 26)) }}
                px;
            overflow: hidden;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding:
                {{ $pad }}
                px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .1);
            height: 100%;
        }

        .tag-oferta {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, var(--vermelho), var(--verm-esc));
            color: var(--branco);
            font-size:
                {{ round($tagFs * 100) / 100 }}
                rem;
            font-weight: 900;
            padding:
                {{ max(3, intval($pad * 0.4)) }}
                px
                {{ max(7, intval($pad * 0.95)) }}
                px;
            text-transform: uppercase;
            border-radius: 0
                {{ max(7, intval($rowH / 26)) }}
                px 0
                {{ max(6, intval($rowH / 30)) }}
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
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, .12));
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
            background: linear-gradient(to bottom, var(--amarelo), #ffc107);
            border:
                {{ max(2, intval($rowH / 90)) }}
                px solid var(--vermelho);
            color: var(--vermelho);
            font-weight: 900;
            font-size:
                {{ round($precoFs * 100) / 100 }}
                rem;
            text-align: center;
            padding:
                {{ max(3, intval($pad * 0.45)) }}
                px
                {{ max(5, intval($pad * 0.75)) }}
                px;
            border-radius:
                {{ max(6, intval($rowH / 35)) }}
                px;
            width: 100%;
            line-height: 1;
            box-shadow: 0 4px 0 #c7a000, 0 6px 10px rgba(0, 0, 0, .2);
            letter-spacing: -1px;
            white-space: nowrap;
            flex-shrink: 0;
            text-shadow: 1px 1px 0 var(--branco);
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
            background: var(--vermelho);
            color: var(--branco);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 7px;
            border-top: 8px solid var(--amarelo);
            text-align: center;
            padding: 0 45px;
        }

        .faixa-endereco strong {
            font-size:
                {{ ($qty > 12) ? '1.5rem' : '2rem' }}
            ;
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, .5);
        }

        .faixa-endereco .info-row {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .rodape-sistema {
            height:
                {{ $rodapeH }}
                px;
            flex-shrink: 0;
            background: var(--verm-esc);
            color: rgba(255, 255, 255, .7);
            font-size: .8rem;
            padding: 0 35px;
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
            <div class="tagline">Ofertas Imperdíveis da Semana</div>
        </div>
        <div class="header-info">
            <div class="header-badge">OFERTAS</div>
            <div class="header-sub">DA SEMANA</div>
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
            $headline = $linhasCopy['headline'] ?? 'Preço baixo de verdade é aqui!';
            $subtitulo = $linhasCopy['subtitulo'] ?? 'Garanta as melhores ofertas para a sua família.';
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
                            <img src="https://placehold.co/200x200/e0e0e0/999999?text=Produto" style="opacity:.25">
                        @endif
                    </div>
                    <div class="card-nome">{{ $prod['nome'] }}</div>
                    <div class="card-preco-de">de R$ {{ $prod['preco_original'] }}</div>
                    <div class="card-preco-por">R$ {{ $prod['preco_novo'] }}</div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px;color:#999;font-size:1.5rem;">
                    Nenhum produto selecionado.
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