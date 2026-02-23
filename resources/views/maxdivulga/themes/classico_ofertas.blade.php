<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Folheto de Ofertas Clássico</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap" rel="stylesheet">

    @php
        $qty = count($produtos);

        if ($qty <= 2)      { $cols = $qty; }
        elseif ($qty <= 4)  { $cols = 2; }
        elseif ($qty <= 9)  { $cols = 3; }
        else                { $cols = 4; }

        $rows = ceil($qty / $cols);

        $headerH = 260;
        $copyH   = 90;
        $footerH = 150;
        $rodapeH = 44;

        $gapPx  = 10;
        $padTop = 14;
        $padSide = 18;

        $gridH     = 1920 - $headerH - $copyH - $footerH - $rodapeH;
        $rowsOnlyH = $gridH - ($rows - 1) * $gapPx - 2 * $padTop;
        $rowH      = max(80, floor($rowsOnlyH / $rows));

        $tagFs   = round(max(0.58, min(0.92, $rowH / 275)) * 10) / 10;
        $nomeFs  = round(max(0.72, min(1.25, $rowH / 200)) * 10) / 10;
        $deFs    = round(max(0.62, min(0.98, $rowH / 275)) * 10) / 10;
        $precoFs = round(max(1.10, min(2.70, $rowH / 120)) * 10) / 10;
        $imgMaxH = max(50, min(170, intval($rowH * 0.45)));
        $pad     = max(7,  min(18,  intval($rowH * 0.058)));

        $hBadgeFs = ($qty > 12) ? '4rem' : '5.5rem';
        $hSubFs   = ($qty > 12) ? '2.5rem' : '3.5rem';
        $copyHFs  = ($qty > 12) ? '1.5rem' : '2.2rem';
        $copySFs  = ($qty > 12) ? '0.95rem' : '1.3rem';
    @endphp

    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --verm:  #E60012;
            --vermE: #A3000D;
            --amar:  #FFD700;
            --amarE: #FFC107;
            --bco:   #ffffff;
            --cinza: #333333;
        }

        html, body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--verm);
            width: 1080px;
            height: 1920px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--cinza);
        }

        /* ──── HEADER ──── */
        .header {
            height: {{ $headerH }}px;
            max-height: {{ $headerH }}px;
            flex-shrink: 0;
            background: linear-gradient(to bottom, var(--verm), #d30011),
                        radial-gradient(circle at 50% 0%, rgba(255,215,0,.1) 0%, transparent 60%);
            color: var(--amar);
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 16px 40px;
            border-bottom: 8px solid var(--amar);
            box-shadow: 0 8px 22px rgba(0,0,0,.3);
            overflow: hidden;
        }

        .header-logo {
            flex: 0 0 auto;
            width: 310px;
            max-width: 310px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            overflow: hidden;      /* contém logos grandes */
        }

        .header-logo img {
            max-width: 100%;
            max-height: 165px;     /* limite absoluto */
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(4px 4px 6px rgba(0,0,0,.4));
            display: block;
        }

        .header-logo .nome-loja {
            background: var(--vermE);
            padding: 10px 22px; border-radius: 50px;
            font-size: 1.6rem; font-weight: 900;
            text-align: center; text-transform: uppercase;
            color: var(--bco);
            border: 4px solid var(--amar);
            box-shadow: 5px 5px 14px rgba(0,0,0,.4);
        }

        .tagline {
            font-size: 0.85rem; color: var(--amar);
            margin-top: 7px; text-transform: uppercase;
            font-weight: 700; letter-spacing: 1.5px; text-align: center;
        }

        .header-info {
            flex: 1; padding-left: 16px;
            display: flex; flex-direction: column; justify-content: center;
        }

        .header-badge {
            color: var(--bco); font-weight: 900;
            font-size: {{ $hBadgeFs }};
            line-height: .85; text-transform: uppercase;
            text-shadow: 2px 2px 0 var(--vermE), 4px 4px 0 var(--vermE), 6px 6px 0 rgba(0,0,0,.4), 8px 8px 12px rgba(0,0,0,.5);
            margin-bottom: 4px;
        }

        .header-sub {
            font-size: {{ $hSubFs }};
            font-weight: 900; color: var(--amar); text-transform: uppercase;
            text-shadow: 2px 2px 0 var(--vermE), 4px 4px 0 var(--vermE), 6px 6px 10px rgba(0,0,0,.5);
            margin-bottom: 12px;
        }

        .header-data {
            font-size: 1.15rem; color: var(--bco); font-weight: 700;
            background: rgba(0,0,0,.3); padding: 8px 22px;
            border-radius: 50px; border: 3px solid var(--amar);
            display: inline-block;
        }

        /* ──── FAIXA COPY ──── */
        .faixa-copy {
            height: {{ $copyH }}px;
            max-height: {{ $copyH }}px;
            flex-shrink: 0;
            background: linear-gradient(to bottom, var(--amar), #ffc200);
            color: var(--verm);
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            text-align: center; padding: 0 36px;
            border-bottom: 4px solid var(--amarE);
            overflow: hidden;
        }

        .faixa-copy .headline {
            font-size: {{ $copyHFs }};
            font-weight: 900; text-transform: uppercase; line-height: 1.05;
        }

        .faixa-copy .subtitulo {
            font-size: {{ $copySFs }};
            font-weight: 800; color: var(--vermE); margin-top: 4px;
        }

        /* ──── WRAPPER ──── */
        .main-content-wrapper {
            background: var(--bco);
            margin: 0 22px;
            flex: 1;
            min-height: 0;
            display: flex;
            flex-direction: column;
            border-radius: 22px 22px 0 0;
            overflow: hidden;
        }

        /* ──── GRID ──── */
        .grid-produtos {
            flex: 1;
            min-height: 0;
            display: grid;
            grid-template-columns: repeat({{ $cols }}, 1fr);
            grid-auto-rows: {{ $rowH }}px;
            gap: {{ $gapPx }}px;
            padding: {{ $padTop }}px {{ $padSide }}px;
            align-content: start;
        }

        /* ──── CARD ──── */
        .card {
            background: var(--bco);
            border: {{ max(2, intval($rowH / 95)) }}px solid var(--amar);
            border-radius: {{ max(8, intval($rowH / 26)) }}px;
            overflow: hidden;
            position: relative;
            display: flex; flex-direction: column; align-items: center;
            padding: {{ $pad }}px;
            box-shadow: 0 5px 14px rgba(0,0,0,.1);
        }

        .tag-oferta {
            position: absolute; top: 0; right: 0;
            background: linear-gradient(135deg, var(--verm), var(--vermE));
            color: var(--bco);
            font-size: {{ $tagFs }}rem; font-weight: 900;
            padding: {{ max(3, intval($pad * .38)) }}px {{ max(7, intval($pad * .9)) }}px;
            text-transform: uppercase;
            border-radius: 0 {{ max(7, intval($rowH / 26)) }}px 0 {{ max(6, intval($rowH / 30)) }}px;
            z-index: 2;
        }

        .card-topo {
            width: 100%; flex: 1;
            display: flex; align-items: center; justify-content: center;
            overflow: hidden;
            padding: {{ max(3, intval($pad * .45)) }}px;
        }

        .card-topo img {
            max-width: 100%;
            max-height: {{ $imgMaxH }}px;
            width: auto; height: auto; object-fit: contain;
            filter: drop-shadow(0 3px 6px rgba(0,0,0,.12));
        }

        .card-nome {
            font-size: {{ $nomeFs }}rem; font-weight: 800;
            color: var(--cinza); text-align: center; line-height: 1.05;
            margin-bottom: {{ max(2, intval($pad * .32)) }}px;
            text-transform: uppercase; flex-shrink: 0;
            overflow: hidden;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        }

        .card-preco-de {
            font-size: {{ $deFs }}rem; color: #888;
            text-decoration: line-through;
            margin-bottom: {{ max(2, intval($pad * .28)) }}px;
            font-weight: 600; flex-shrink: 0;
        }

        .card-preco-por {
            background: linear-gradient(to bottom, var(--amar), #ffc107);
            border: {{ max(2, intval($rowH / 95)) }}px solid var(--verm);
            color: var(--verm); font-weight: 900;
            font-size: {{ $precoFs }}rem; text-align: center;
            padding: {{ max(3, intval($pad * .42)) }}px {{ max(5, intval($pad * .72)) }}px;
            border-radius: {{ max(6, intval($rowH / 35)) }}px;
            width: 100%; line-height: 1;
            box-shadow: 0 3px 0 #c7a000, 0 5px 10px rgba(0,0,0,.18);
            letter-spacing: -1px; white-space: nowrap; flex-shrink: 0;
            text-shadow: 1px 1px 0 var(--bco);
        }

        /* ──── RODAPÉ ──── */
        .faixa-endereco {
            height: {{ $footerH }}px;
            max-height: {{ $footerH }}px;
            flex-shrink: 0;
            background: var(--verm); color: var(--bco);
            display: flex; flex-direction: column;
            justify-content: center; align-items: center;
            gap: 6px; border-top: 7px solid var(--amar);
            text-align: center; padding: 0 45px;
            overflow: hidden;
        }

        .faixa-endereco strong {
            font-size: {{ ($qty > 12) ? '1.5rem' : '2rem' }};
            text-transform: uppercase; font-weight: 900;
            text-shadow: 3px 3px 6px rgba(0,0,0,.5);
        }

        .faixa-endereco .info-row { font-size: 1.15rem; font-weight: 700; }

        .rodape-sistema {
            height: {{ $rodapeH }}px; flex-shrink: 0;
            background: var(--vermE); color: rgba(255,255,255,.7);
            font-size: .78rem; padding: 0 35px;
            display: flex; justify-content: space-between; align-items: center;
            text-transform: uppercase; letter-spacing: 1px;
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
            <div class="header-data">VÁLIDO ATÉ: {{ \Carbon\Carbon::now()->addDays(7)->translatedFormat('d \d\e F') }}</div>
        </div>
    </div>

    <div class="main-content-wrapper">
        @php
            $linhasCopy = [];
            if ($copyTexto) {
                preg_match('/HEADLINE:\s*(.+)/i', $copyTexto, $h);
                preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto, $s);
                $linhasCopy['headline']  = trim($h[1] ?? '');
                $linhasCopy['subtitulo'] = trim($s[1] ?? '');
            }
            $headline  = $linhasCopy['headline']  ?? 'Preço baixo de verdade é aqui!';
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
                <div style="grid-column:1/-1;text-align:center;padding:40px;color:#999;font-size:1.4rem;">
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