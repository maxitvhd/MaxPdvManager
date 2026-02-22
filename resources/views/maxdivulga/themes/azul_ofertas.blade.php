<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folheto de Ofertas Azul Premium</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">
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

        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--azul-vivo);
            width: 1080px;
            height: 1920px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--cinza-texto);
        }

        /* Container área branca central com moldura azul */
        .main-content-wrapper {
            flex: 1;
            background: var(--branco);
            margin: 0 25px;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3) inset;
            border-radius: 20px 20px 0 0;
        }

        /* ===== HEADER AZUL IMPACTANTE ===== */
        .header {
            background: linear-gradient(135deg, var(--azul-escuro) 0%, var(--azul-vivo) 60%, var(--azul-claro) 100%);
            color: var(--amarelo-ouro);
            display: flex;
            align-items: center;
            min-height: 280px;
            padding: 30px 40px;
            border-bottom: 6px solid var(--amarelo-ouro);
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
            position: relative;
            z-index: 10;
            overflow: hidden;
        }

        /* Círculos decorativos de fundo */
        .header::before {
            content: '';
            position: absolute;
            top: -60px;
            right: -60px;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(255, 215, 0, 0.08);
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -80px;
            left: 30%;
            width: 350px;
            height: 350px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.1);
        }

        .header-logo {
            flex: 1;
            padding-right: 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .header-logo img {
            max-height: 180px;
            max-width: 100%;
            object-fit: contain;
            filter: drop-shadow(2px 2px 6px rgba(0, 0, 0, 0.4));
        }

        .header-logo .nome-loja {
            background: var(--azul-escuro);
            padding: 15px 30px;
            border-radius: 50px;
            font-size: 1.8rem;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            display: inline-block;
            border: 3px solid var(--amarelo-ouro);
            box-shadow: 5px 5px 15px rgba(0, 0, 0, 0.4);
        }

        .header-logo .tagline {
            font-size: 1.1rem;
            color: var(--amarelo-ouro);
            margin-top: 14px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 2px;
            text-shadow: 1px 1px 4px rgba(0, 0, 0, 0.5);
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

        /* Efeito 3D nos títulos */
        .header-badge {
            display: inline-block;
            color: var(--branco);
            font-weight: 900;
            font-size: 5.5rem;
            line-height: 0.9;
            text-transform: uppercase;
            text-shadow:
                3px 3px 0px var(--azul-escuro),
                6px 6px 0px rgba(0, 0, 0, 0.3),
                8px 8px 12px rgba(0, 0, 0, 0.4);
            margin-bottom: 5px;
        }

        .header-sub {
            font-size: 3rem;
            font-weight: 900;
            color: var(--amarelo-ouro);
            text-transform: uppercase;
            line-height: 1;
            text-shadow:
                3px 3px 0px var(--azul-escuro),
                5px 5px 8px rgba(0, 0, 0, 0.4);
            margin-bottom: 15px;
        }

        .header-data {
            font-size: 1.2rem;
            color: var(--branco);
            font-weight: 700;
            background: rgba(0, 0, 0, 0.3);
            padding: 8px 25px;
            border-radius: 50px;
            border: 2px solid var(--amarelo-ouro);
            box-shadow: 3px 3px 10px rgba(0, 0, 0, 0.3);
        }

        /* ===== FAIXA COPY AZUL ===== */
        .faixa-copy {
            background: linear-gradient(to bottom, var(--amarelo-ouro), #ffca00);
            color: var(--azul-escuro);
            padding: 25px 36px;
            text-align: center;
            border-bottom: 3px solid var(--amarelo-escuro);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
            z-index: 2;
        }

        .faixa-copy .headline {
            font-size: 2.2rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 2px 2px 0px rgba(255, 255, 255, 0.6);
        }

        .faixa-copy .subtitulo {
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--azul-escuro);
            margin-top: 8px;
        }

        /* ===== GRID PRODUTOS ===== */
        .grid-produtos {
            display: grid;
            gap: 20px;
            padding: 25px 30px;
            background: #F0F4FF;
            /* Fundo azul muito claro */
            flex: 1;
            min-height: 0;
            align-content: start;
        }

        /* Ajustes de Grid */
        .grid-produtos.qty-1 {
            grid-template-columns: 1fr;
            padding: 50px 200px;
        }

        .grid-produtos.qty-2 {
            grid-template-columns: repeat(2, 1fr);
            padding: 50px;
            gap: 40px;
        }

        .grid-produtos.qty-3 {
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .grid-produtos.qty-4 {
            grid-template-columns: repeat(2, 1fr);
            gap: 30px;
        }

        .grid-produtos.qty-5,
        .grid-produtos.qty-6 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-produtos.qty-7,
        .grid-produtos.qty-8,
        .grid-produtos.qty-9 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-produtos.qty-10,
        .grid-produtos.qty-11,
        .grid-produtos.qty-12 {
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        .grid-produtos.qty-many {
            grid-template-columns: repeat(4, 1fr);
            gap: 10px;
            padding: 15px;
        }

        /* ===== CARD AZUL ===== */
        .card {
            background: var(--branco);
            border: 3px solid var(--azul-vivo);
            /* Borda azul */
            border-radius: 15px;
            overflow: visible;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            box-shadow: 0 8px 20px rgba(0, 87, 184, 0.15);
            height: 100%;
        }

        .card-topo {
            width: 100%;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            min-height: 120px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .card-topo img {
            max-width: 95%;
            max-height: 95%;
            width: auto;
            height: auto;
            object-fit: contain;
            filter: drop-shadow(0 5px 10px rgba(0, 0, 0, 0.1));
        }

        /* Tag OFERTA azul */
        .tag-oferta {
            position: absolute;
            top: -12px;
            right: -12px;
            background: linear-gradient(45deg, var(--azul-vivo), var(--azul-escuro));
            color: var(--branco);
            font-size: 0.9rem;
            font-weight: 900;
            padding: 8px 20px;
            text-transform: uppercase;
            transform: rotate(15deg);
            box-shadow: 3px 3px 8px rgba(0, 0, 0, 0.4);
            z-index: 2;
            border-radius: 4px;
            border: 2px solid var(--branco);
        }

        .card-nome {
            font-size: 1.1rem;
            font-weight: 800;
            color: var(--cinza-texto);
            text-align: center;
            line-height: 1.3;
            margin-bottom: 8px;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        .qty-10 .card-nome,
        .qty-11 .card-nome,
        .qty-12 .card-nome,
        .qty-many .card-nome {
            font-size: 0.9rem;
        }

        .card-preco-de {
            font-size: 1rem;
            color: #888;
            text-decoration: line-through;
            margin-bottom: 5px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Box de Preço Azul/Amarelo */
        .card-preco-por {
            background: linear-gradient(to bottom, var(--azul-vivo), var(--azul-escuro));
            border: 3px solid var(--amarelo-ouro);
            /* Borda amarela no preço */
            color: var(--branco);
            font-weight: 900;
            font-size: 2.4rem;
            text-align: center;
            padding: 5px 15px;
            border-radius: 12px;
            width: 100%;
            line-height: 1;
            box-shadow: 0 5px 0 var(--azul-escuro), 0 8px 15px rgba(0, 0, 0, 0.25);
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            flex-shrink: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* ===== RODAPÉ AZUL ===== */
        .faixa-endereco {
            background: linear-gradient(to right, var(--azul-escuro), var(--azul-vivo), var(--azul-escuro));
            color: var(--branco);
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            border-top: 6px solid var(--amarelo-ouro);
            text-align: center;
            box-shadow: 0 -5px 15px rgba(0, 0, 0, 0.2);
            z-index: 10;
        }

        .faixa-endereco strong {
            font-size: 1.8rem;
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.4);
        }

        .faixa-endereco .info-row {
            font-size: 1.2rem;
            font-weight: 700;
        }

        .rodape-sistema {
            background: var(--azul-escuro);
            color: rgba(255, 255, 255, 0.6);
            font-size: 0.8rem;
            padding: 12px 30px;
            display: flex;
            justify-content: space-between;
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

        @php $qtyClass = count($produtos) <= 12 ? 'qty-' . count($produtos) : 'qty-many'; @endphp
        <div class="grid-produtos {{ $qtyClass }}">
            @forelse($produtos as $i => $prod)
                <div class="card">
                    <div class="card-topo">
                        @if(!empty($prod['imagem_url']))
                            <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}">
                        @else
                            <img src="https://placehold.co/200x200/e0eeff/4477cc?text=Oferta" style="opacity:0.4">
                        @endif
                    </div>
                    <div class="tag-oferta">OFERTA</div>
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