<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folheto de Ofertas - Clássico</title>
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
        // Tamanhos dinâmicos baseados na escala
        $cardPadding = round(12 * $scale) . 'px';
        $imgMinH = round(90 * $scale) . 'px';
        $imgMaxH = round(130 * $scale) . 'px';
        $tagFontSize = round(0.75 * $scale * 10) / 10 . 'rem';
        $nomeFont = round(1.0 * $scale * 10) / 10 . 'rem';
        $deFont = round(0.85 * $scale * 10) / 10 . 'rem';
        $precoFont = round(1.8 * $scale * 10) / 10 . 'rem';
        $gridGap = round(14 * $scale) . 'px';
        $gridPadding = $qty > 12 ? '15px 18px' : '20px 30px';
        $cardInner = 'auto'; // sem altura mínima forçada
    @endphp

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --vermelho-vivo: #E60012;
            --vermelho-escuro: #A3000D;
            --amarelo-ouro: #FFD700;
            --amarelo-escuro: #FFC107;
            --branco: #ffffff;
            --cinza-texto: #333333;
            --borda-card: #FFD700;
        }

        html,
        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--vermelho-vivo);
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
            color: var(--cinza-texto);
        }

        /* Header */
        .header {
            background: linear-gradient(to bottom, var(--vermelho-vivo), #d30011);
            color: var(--amarelo-ouro);
            display: flex;
            align-items: center;
            gap: 30px;
            min-height:
                {{ $qty > 12 ? '200px' : '280px' }}
            ;
            padding:
                {{ $qty > 12 ? '20px 35px' : '35px 45px' }}
            ;
            border-bottom: 8px solid var(--amarelo-ouro);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            flex-shrink: 0;
        }

        .header-logo {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header-logo img {
            max-height:
                {{ $qty > 12 ? '140px' : '200px' }}
            ;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(4px 4px 6px rgba(0, 0, 0, 0.4));
        }

        .header-logo .nome-loja {
            background: var(--vermelho-escuro);
            padding: 10px 25px;
            border-radius: 50px;
            font-size:
                {{ $qty > 12 ? '1.5rem' : '2rem' }}
            ;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            display: inline-block;
            border: 4px solid var(--amarelo-ouro);
            box-shadow: 6px 6px 15px rgba(0, 0, 0, 0.4);
        }

        .header-logo .tagline {
            font-size:
                {{ $qty > 12 ? '1.0rem' : '1.3rem' }}
            ;
            color: var(--amarelo-ouro);
            margin-top: 10px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
        }

        .header-info {
            flex: 2 1 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            padding-left: 20px;
        }

        .header-badge {
            display: inline-block;
            color: var(--branco);
            font-weight: 900;
            font-size:
                {{ $qty > 12 ? '4.5rem' : '6.5rem' }}
            ;
            line-height: 0.85;
            text-transform: uppercase;
            text-shadow: 2px 2px 0px var(--vermelho-escuro), 4px 4px 0px var(--vermelho-escuro), 6px 6px 0px rgba(0, 0, 0, 0.4), 8px 8px 12px rgba(0, 0, 0, 0.5);
            margin-bottom: 5px;
        }

        .header-sub {
            font-size:
                {{ $qty > 12 ? '2.5rem' : '3.8rem' }}
            ;
            font-weight: 900;
            color: var(--amarelo-ouro);
            text-transform: uppercase;
            line-height: 1;
            text-shadow: 2px 2px 0px var(--vermelho-escuro), 4px 4px 0px var(--vermelho-escuro), 6px 6px 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 12px;
        }

        .header-data {
            font-size:
                {{ $qty > 12 ? '1.1rem' : '1.4rem' }}
            ;
            color: var(--branco);
            font-weight: 700;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 20px;
            border-radius: 50px;
            border: 3px solid var(--amarelo-ouro);
            box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.4);
        }

        /* Faixa Copy */
        .faixa-copy {
            background: linear-gradient(to bottom, var(--amarelo-ouro), #ffc200);
            color: var(--vermelho-vivo);
            padding:
                {{ $qty > 12 ? '12px 30px' : '20px 36px' }}
            ;
            text-align: center;
            border-bottom: 4px solid var(--amarelo-escuro);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            z-index: 2;
            flex-shrink: 0;
        }

        .faixa-copy .headline {
            font-size:
                {{ $qty > 12 ? '1.6rem' : '2.2rem' }}
            ;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 1px 1px 0px rgba(255, 255, 255, 0.6);
        }

        .faixa-copy .subtitulo {
            font-size:
                {{ $qty > 12 ? '1.0rem' : '1.3rem' }}
            ;
            font-weight: 800;
            color: var(--vermelho-escuro);
            margin-top: 5px;
        }

        /* Grid de Produtos */
        .main-content-wrapper {
            background: var(--branco);
            margin: 0 25px;
            display: flex;
            flex-direction: column;
            border-radius: 25px 25px 0 0;
            flex: 1;
        }

        .grid-produtos {
            display: grid;
            grid-template-columns: repeat({{ $cols }}, 1fr);
            gap:
                {{ $gridGap }}
            ;
            padding:
                {{ $gridPadding }}
            ;
            background: var(--branco);
            align-content: start;
        }

        /* Card */
        .card {
            background: var(--branco);
            border:
                {{ round(3 * $scale) }}
                px solid var(--borda-card);
            border-radius:
                {{ round(14 * $scale) }}
                px;
            overflow: hidden;
            /* SEM overflow visible para evitar sobreposição de tags */
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding:
                {{ $cardPadding }}
            ;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.1);
        }

        /* Tag OFERTA - dentro do card, sem position absolute externo */
        .tag-oferta {
            position: absolute;
            top: 0;
            right: 0;
            background: linear-gradient(135deg, var(--vermelho-vivo), var(--vermelho-escuro));
            color: var(--branco);
            font-size:
                {{ $tagFontSize }}
            ;
            font-weight: 900;
            padding:
                {{ round(5 * $scale) }}
                px
                {{ round(12 * $scale) }}
                px;
            text-transform: uppercase;
            border-radius: 0
                {{ round(12 * $scale) }}
                px 0
                {{ round(10 * $scale) }}
                px;
            z-index: 2;
        }

        /* Imagem */
        .card-topo {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height:
                {{ $imgMinH }}
            ;
            max-height:
                {{ $imgMaxH }}
            ;
            padding:
                {{ round(6 * $scale) }}
                px;
            margin-bottom:
                {{ round(6 * $scale) }}
                px;
            overflow: hidden;
        }

        .card-topo img {
            max-width: 100%;
            max-height:
                {{ $imgMaxH }}
            ;
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.12));
        }

        /* Nome */
        .card-nome {
            font-size:
                {{ $nomeFont }}
            ;
            font-weight: 800;
            color: var(--cinza-texto);
            text-align: center;
            line-height: 1.1;
            margin-bottom:
                {{ round(5 * $scale) }}
                px;
            text-transform: uppercase;
        }

        /* Preço de */
        .card-preco-de {
            font-size:
                {{ $deFont }}
            ;
            color: #888;
            text-decoration: line-through;
            margin-bottom:
                {{ round(4 * $scale) }}
                px;
            font-weight: 600;
        }

        /* Preço por */
        .card-preco-por {
            background: linear-gradient(to bottom, var(--amarelo-ouro), #ffc107);
            border:
                {{ round(3 * $scale) }}
                px solid var(--vermelho-vivo);
            color: var(--vermelho-vivo);
            font-weight: 900;
            font-size:
                {{ $precoFont }}
            ;
            text-align: center;
            padding:
                {{ round(4 * $scale) }}
                px
                {{ round(8 * $scale) }}
                px;
            border-radius:
                {{ round(10 * $scale) }}
                px;
            width: 100%;
            line-height: 1;
            box-shadow: 0 4px 0 #c7a000, 0 6px 10px rgba(0, 0, 0, 0.2);
            letter-spacing: -1px;
            white-space: nowrap;
            text-shadow: 1px 1px 0px var(--branco);
        }

        /* Rodapé */
        .faixa-endereco {
            background: var(--vermelho-vivo);
            color: var(--branco);
            padding:
                {{ $qty > 12 ? '20px 35px' : '30px 45px' }}
            ;
            display: flex;
            flex-direction: column;
            gap: 8px;
            border-top: 6px solid var(--amarelo-ouro);
            text-align: center;
            box-shadow: 0 -8px 20px rgba(0, 0, 0, 0.25);
            z-index: 10;
            flex-shrink: 0;
        }

        .faixa-endereco strong {
            font-size:
                {{ $qty > 12 ? '1.5rem' : '2rem' }}
            ;
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
        }

        .faixa-endereco .info-row {
            font-size:
                {{ $qty > 12 ? '1.0rem' : '1.3rem' }}
            ;
            font-weight: 700;
        }

        .rodape-sistema {
            background: var(--vermelho-escuro);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.80rem;
            padding: 12px 35px;
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
            <div class="tagline">Ofertas Imperdíveis da Semana</div>
        </div>
        <div class="header-info">
            <div>
                <div class="header-badge">OFERTAS</div>
                <div class="header-sub">DA SEMANA</div>
                @php \Carbon\Carbon::setLocale('pt_BR'); @endphp
                <div class="header-data">VÁLIDO ATÉ:
                    {{ \Carbon\Carbon::now()->addDays(7)->translatedFormat('d \d\e F') }}
                </div>
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
                            <img src="https://placehold.co/200x200/e0e0e0/999999?text=Produto" style="opacity:0.25">
                        @endif
                    </div>
                    <div class="card-nome">{{ $prod['nome'] }}</div>
                    <div class="card-preco-de">de R$ {{ $prod['preco_original'] }}</div>
                    <div class="card-preco-por">R$ {{ $prod['preco_novo'] }}</div>
                </div>
            @empty
                <div style="grid-column:1/-1;text-align:center;padding:60px;color:#999;font-size:1.5rem;">
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