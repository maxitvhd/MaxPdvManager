<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Folheto de Ofertas Vibrante Premium - Final</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap"
        rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --vermelho-vivo: #E60012;
            --vermelho-escuro: #A3000D;
            /* Usado para sombras do texto */
            --amarelo-ouro: #FFD700;
            --amarelo-escuro: #FFC107;
            --branco: #ffffff;
            --cinza-texto: #333333;
            --borda-card: #FFD700;
        }

        body {
            font-family: 'Roboto Condensed', Arial, sans-serif;
            background: var(--vermelho-vivo);
            width: 1080px;
            height: 1920px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            color: var(--cinza-texto);
        }

        /* Container da área branca central (efeito moldura) */
        .main-content-wrapper {
            flex: 1;
            background: var(--branco);
            margin: 0 25px;
            /* Cria a borda vermelha lateral */
            display: flex;
            flex-direction: column;
            overflow: hidden;
            box-shadow: 0 0 25px rgba(0, 0, 0, 0.35) inset;
            /* Sombra interna mais forte */
            border-radius: 25px 25px 0 0;
        }

        /* ===== HEADER COM EFEITOS VISUAIS ===== */
        .header {
            /* Gradiente mais rico e textura sutil */
            background: linear-gradient(to bottom, var(--vermelho-vivo), #d30011),
                radial-gradient(circle at 50% 0%, rgba(255, 215, 0, 0.1) 0%, transparent 60%);
            color: var(--amarelo-ouro);
            display: flex;
            align-items: center;
            /* Gap ajustado para os elementos grandes */
            gap: 30px;
            min-height: 300px;
            /* Altura aumentada para impacto */
            padding: 35px 45px;
            border-bottom: 8px solid var(--amarelo-ouro);
            /* Borda inferior mais grossa */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
            /* Sombra externa forte */
            position: relative;
            z-index: 10;
        }

        .header-logo {
            flex: 1 1 auto;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
        }

        .header-logo img {
            max-height: 200px;
            /* Logo maior */
            max-width: 100%;
            object-fit: contain;
            /* Sombra para destacar a logo do fundo vermelho */
            filter: drop-shadow(4px 4px 6px rgba(0, 0, 0, 0.4));
        }

        .header-logo .nome-loja {
            background: var(--vermelho-escuro);
            padding: 15px 35px;
            border-radius: 50px;
            font-size: 2rem;
            font-weight: 900;
            text-align: center;
            text-transform: uppercase;
            color: var(--branco);
            display: inline-block;
            border: 4px solid var(--amarelo-ouro);
            box-shadow: 6px 6px 15px rgba(0, 0, 0, 0.4);
        }

        .header-logo .tagline {
            font-size: 1.3rem;
            color: var(--amarelo-ouro);
            margin-top: 18px;
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

        /* TEXTOS GIGANTES COM EFEITO 3D */
        .header-badge {
            display: inline-block;
            color: var(--branco);
            font-weight: 900;
            font-size: 6.5rem;
            /* Um pouco maior e mais pesado */
            line-height: 0.85;
            text-transform: uppercase;
            /* Sombra em camadas para efeito 3D profundo e visível */
            text-shadow:
                2px 2px 0px var(--vermelho-escuro),
                4px 4px 0px var(--vermelho-escuro),
                6px 6px 0px rgba(0, 0, 0, 0.4),
                8px 8px 12px rgba(0, 0, 0, 0.5);
            margin-bottom: 5px;
            filter: drop-shadow(4px 4px 8px rgba(0, 0, 0, 0.3));
        }

        .header-sub {
            font-size: 3.8rem;
            /* Maior */
            font-weight: 900;
            color: var(--amarelo-ouro);
            text-transform: uppercase;
            line-height: 1;
            /* Sombra em camadas para efeito 3D */
            text-shadow:
                2px 2px 0px var(--vermelho-escuro),
                4px 4px 0px var(--vermelho-escuro),
                6px 6px 10px rgba(0, 0, 0, 0.5);
            margin-bottom: 20px;
            filter: drop-shadow(3px 3px 6px rgba(0, 0, 0, 0.3));
        }

        .header-data {
            font-size: 1.4rem;
            color: var(--branco);
            font-weight: 700;
            background: rgba(0, 0, 0, 0.3);
            padding: 10px 30px;
            border-radius: 50px;
            border: 3px solid var(--amarelo-ouro);
            box-shadow: 4px 4px 12px rgba(0, 0, 0, 0.4);
        }

        /* ===== FAIXA COPY ===== */
        .faixa-copy {
            background: linear-gradient(to bottom, var(--amarelo-ouro), #ffc200);
            color: var(--vermelho-vivo);
            padding: 25px 36px;
            text-align: center;
            border-bottom: 4px solid var(--amarelo-escuro);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            z-index: 2;
        }

        .faixa-copy .headline {
            font-size: 2.4rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.1;
            text-shadow: 2px 2px 0px rgba(255, 255, 255, 0.6);
        }

        .faixa-copy .subtitulo {
            font-size: 1.4rem;
            font-weight: 800;
            color: var(--vermelho-escuro);
            margin-top: 8px;
        }

        /* ===== GRID PRODUTOS (Estes são os estilos que fazem funcionar) ===== */
        .grid-produtos {
            display: grid;
            gap: 20px;
            padding: 25px 35px;
            background: var(--branco);
            flex: 1;
            min-height: 0;
            align-content: start;
        }

        /* Regras de colunas dinâmicas */
        .grid-produtos.qty-1 {
            grid-template-columns: 1fr;
            padding: 50px 200px;
        }

        .grid-produtos.qty-2 {
            grid-template-columns: repeat(2, 1fr);
            padding: 50px;
            gap: 50px;
        }

        .grid-produtos.qty-3 {
            grid-template-columns: repeat(3, 1fr);
            gap: 35px;
        }

        .grid-produtos.qty-4 {
            grid-template-columns: repeat(2, 1fr);
            gap: 35px;
            padding: 30px 100px;
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
            grid-template-rows: repeat(auto-fill, minmax(280px, 1fr));
            gap: 15px;
            padding: 20px;
        }


        /* ===== CARD DE PRODUTO ===== */
        .card {
            background: var(--branco);
            /* Borda sólida amarela grossa para destaque */
            border: 4px solid var(--borda-card);
            border-radius: 18px;
            overflow: visible;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px;
            /* Sombra para destacar do fundo branco */
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            height: 100%;
            min-height: 280px;
        }

        .card-topo {
            width: 100%;
            background: transparent;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            min-height: 130px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .card-topo img {
            max-width: 95%;
            max-height: 95%;
            width: auto;
            height: auto;
            object-fit: contain;
            /* Sombra suave na imagem do produto */
            filter: drop-shadow(0 6px 12px rgba(0, 0, 0, 0.15));
        }

        .tag-oferta {
            position: absolute;
            top: -15px;
            right: -15px;
            background: linear-gradient(45deg, var(--vermelho-vivo), var(--vermelho-escuro));
            color: var(--branco);
            font-size: 0.95rem;
            font-weight: 900;
            padding: 10px 22px;
            text-transform: uppercase;
            transform: rotate(15deg);
            box-shadow: 4px 4px 10px rgba(0, 0, 0, 0.4);
            z-index: 2;
            border-radius: 6px;
            border: 2px solid var(--branco);
        }

        .card-nome {
            font-size: 1.2rem;
            font-weight: 800;
            color: var(--cinza-texto);
            text-align: center;
            line-height: 1.2;
            margin-bottom: 10px;
            text-transform: uppercase;
            flex-shrink: 0;
        }

        /* Ajuste de fonte para muitos produtos */
        .qty-10 .card-nome,
        .qty-11 .card-nome,
        .qty-12 .card-nome,
        .qty-many .card-nome {
            font-size: 0.9rem;
            margin-bottom: 5px;
        }

        .card-preco-de {
            font-size: 1.1rem;
            color: #888;
            text-decoration: line-through;
            margin-bottom: 5px;
            font-weight: 600;
            flex-shrink: 0;
        }

        /* Box de Preço Impactante */
        .card-preco-por {
            background: linear-gradient(to bottom, var(--amarelo-ouro), #ffc107);
            border: 4px solid var(--vermelho-vivo);
            color: var(--vermelho-vivo);
            font-weight: 1000;
            font-size: 2.5rem;
            text-align: center;
            padding: 5px 10px;
            border-radius: 14px;
            width: 100%;
            line-height: 1;
            /* Sombra dura "pop" */
            box-shadow: 0 5px 0 #c7a000, 0 8px 12px rgba(0, 0, 0, 0.25);
            letter-spacing: -1.5px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            flex-shrink: 0;
            text-shadow: 2px 2px 0px var(--branco);
        }

        .qty-many .card-preco-por {
            font-size: 1.8rem;
            padding: 4px 8px;
        }

        .qty-many .card-preco-de {
            font-size: 0.9rem;
            margin-bottom: 2px;
        }

        /* ===== FAIXA ENDEREÇO ===== */
        .faixa-endereco {
            background: var(--vermelho-vivo);
            color: var(--branco);
            padding: 35px 45px;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-top: auto;
            border-top: 8px solid var(--amarelo-ouro);
            text-align: center;
            box-shadow: 0 -8px 20px rgba(0, 0, 0, 0.25);
            z-index: 10;
        }

        .faixa-endereco strong {
            font-size: 2rem;
            text-transform: uppercase;
            font-weight: 900;
            text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.5);
        }

        .faixa-endereco .info-row {
            font-size: 1.3rem;
            font-weight: 700;
        }

        .rodape-sistema {
            background: var(--vermelho-escuro);
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.85rem;
            padding: 15px 35px;
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

        @php $qtyClass = count($produtos) <= 12 ? 'qty-' . count($produtos) : 'qty-many'; @endphp
        <div class="grid-produtos {{ $qtyClass }}">
            @forelse($produtos as $i => $prod)
                <div class="card">
                    <div class="card-topo">
                        @if(!empty($prod['imagem_url']))
                            <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}">
                        @else
                            <img src="https://placehold.co/200x200/e0e0e0/999999?text=Oferta" style="opacity:0.3">
                        @endif
                    </div>
                    <div class="tag-oferta">OFERTA</div>
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