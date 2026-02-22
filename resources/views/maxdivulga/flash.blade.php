<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tema Fresh Market</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            /* Nova Paleta "Fresh" */
            --verde-principal: #2E7D32;
            /* Verde folha escuro */
            --verde-claro: #E8F5E9;
            /* Fundo muito claro */
            --verde-medio: #66BB6A;
            /* Para detalhes */
            --laranja-preco: #F57C00;
            /* Cor de destaque para o preço */
            --branco: #ffffff;
            --texto-escuro: #263238;
            /* Cinza chumbo em vez de preto total */
            --cinza-claro: #ECEFF1;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: var(--verde-claro);
            /* Fundo geral suave */
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
            color: var(--texto-escuro);
        }

        /* ===== HEADER LIMPO E MODERNO ===== */
        .header {
            background: var(--verde-principal);
            color: var(--branco);
            display: flex;
            align-items: center;
            min-height: 180px;
            padding: 0 20px;
            /* Arredondar a parte inferior para um visual mais moderno */
            border-radius: 0 0 30px 30px;
            box-shadow: 0 10px 20px rgba(46, 125, 50, 0.15);
            position: relative;
            z-index: 10;
        }

        .header-logo {
            background: transparent;
            padding: 20px 30px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            min-width: 300px;
            /* Linha divisória sutiu */
            border-right: 1px solid rgba(255, 255, 255, 0.15);
        }

        .header-logo .nome-loja {
            font-size: 1.8rem;
            font-weight: 800;
            text-align: left;
            letter-spacing: 0.5px;
            line-height: 1.1;
            text-transform: none;
            /* Removendo all-caps forçado */
        }

        .header-logo .tagline {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 8px;
            text-transform: none;
            font-weight: 500;
        }

        .header-info {
            flex: 1;
            padding: 20px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
        }

        .header-badge {
            display: inline-block;
            background: var(--white);
            color: var(--verde-principal);
            font-weight: 700;
            font-size: 1.2rem;
            padding: 8px 25px;
            border-radius: 50px;
            /* Formato de pílula */
            letter-spacing: 0.5px;
            text-transform: uppercase;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.05);
            margin-bottom: 12px;
        }

        .header-sub {
            font-size: 1.8rem;
            font-weight: 700;
            color: var(--branco);
            letter-spacing: -0.5px;
        }

        .header-data {
            font-size: 0.95rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 8px;
            font-weight: 500;
        }

        /* ===== FAIXA COPY FLUTUANTE ===== */
        .faixa-copy {
            background: var(--branco);
            color: var(--texto-escuro);
            padding: 25px 40px;
            text-align: center;
            /* Fazendo a faixa "flutuar" sobre o fundo */
            margin: 25px 30px;
            border-radius: 20px;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.05);
        }

        .faixa-copy .headline {
            font-size: 1.8rem;
            font-weight: 800;
            color: var(--verde-principal);
            text-transform: none;
            line-height: 1.2;
        }

        .faixa-copy .subtitulo {
            font-size: 1.1rem;
            font-weight: 500;
            color: #546E7A;
            margin-top: 8px;
        }

        /* ===== GRID E CARDS ===== */
        .label-secao {
            display: none;
            /* Ocultando neste tema */
        }

        .grid-produtos {
            display: grid;
            gap: 25px;
            padding: 10px 30px 30px 30px;
            background: transparent;
            /* Fundo transparente para mostrar o verde claro */
            flex-grow: 1;
        }

        /* Mantendo a estrutura de colunas do seu HTML */
        .grid-produtos.qty-1 {
            grid-template-columns: repeat(1, 1fr);
            max-width: 550px;
            margin: 0 auto;
        }

        .grid-produtos.qty-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-produtos.qty-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-produtos.qty-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-produtos.qty-5,
        .grid-produtos.qty-6 {
            grid-template-columns: repeat(3, 1fr);
        }

        .grid-produtos.qty-7,
        .grid-produtos.qty-8,
        .grid-produtos.qty-9,
        .grid-produtos.qty-10,
        .grid-produtos.qty-many {
            grid-template-columns: repeat(4, 1fr);
        }

        /* Estilo do Card "Fresh" */
        .card {
            background: var(--branco);
            border-radius: 20px;
            /* Bordas bem arredondadas */
            overflow: hidden;
            /* Sombra suave e moderna em vez de bordas */
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 20px;
            border: 1px solid var(--cinza-claro);
            /* Borda muito sutil */
            position: relative;
        }

        /* Resetando o estilo do primeiro item que tinha no outro tema */
        .card-destaque {
            background: var(--branco);
        }

        .card-topo {
            width: 100%;
            background: var(--branco);
            padding: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 180px;
        }

        /* Ajustes de altura da imagem */
        .qty-1 .card-topo,
        .qty-2 .card-topo,
        .qty-3 .card-topo {
            min-height: 220px;
        }

        .qty-4 .card-topo,
        .qty-5 .card-topo,
        .qty-6 .card-topo {
            min-height: 180px;
        }

        .card-topo img {
            transition: transform 0.3s ease;
        }

        /* Pequeno zoom na imagem ao passar o mouse (opcional) */
        .card:hover .card-topo img {
            transform: scale(1.05);
        }

        /* Tag de oferta discreta no canto */
        .tag-oferta {
            position: absolute;
            top: 0;
            right: 0;
            width: auto;
            background: var(--laranja-preco);
            /* Laranja para destaque */
            color: var(--branco);
            font-size: 0.75rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 8px 15px;
            text-transform: uppercase;
            border-radius: 0 20px 0 20px;
            /* Canto arredondado */
        }

        /* Garantindo que o primeiro item use as mesmas cores */
        .card-destaque .tag-oferta {
            background: var(--laranja-preco);
            color: var(--branco);
        }


        .card-nome {
            font-size: 1rem;
            font-weight: 600;
            color: var(--texto-escuro);
            text-align: center;
            padding: 10px 20px 5px;
            line-height: 1.4;
            text-transform: capitalize;
            /* Apenas a primeira letra maiúscula */
        }

        .card-destaque .card-nome {
            color: var(--texto-escuro);
        }

        .card-preco-de {
            font-size: 0.85rem;
            color: #90A4AE;
            /* Cinza azulado suave */
            text-decoration: line-through;
            text-align: center;
            font-weight: 500;
            margin-bottom: 4px;
        }

        .card-destaque .card-preco-de {
            color: #90A4AE;
        }

        .card-preco-por {
            background: transparent;
            /* Sem box de fundo */
            color: var(--laranja-preco);
            /* Preço em laranja vibrante */
            font-weight: 800;
            font-size: 1.8rem;
            text-align: center;
            padding: 0;
            border-radius: 0;
            margin: 0 10px;
            width: auto;
        }

        /* Resetando o estilo do primeiro item */
        .card-destaque .card-preco-por {
            background: transparent;
            color: var(--laranja-preco);
        }

        /* Ajuste fino para o R$ ficar menor ao lado do preço grande */
        .card-preco-por::first-letter {
            font-size: 0.6em;
            font-weight: 600;
            margin-right: 3px;
            color: #90A4AE;
        }

        /* ===== FAIXA ENDEREÇO ===== */
        .faixa-endereco {
            background: var(--verde-principal);
            color: var(--branco);
            padding: 30px 40px;
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin: 0 30px;
            /* Margem lateral para "flutuar" */
            border-radius: 25px 25px 0 0;
            text-align: center;
            box-shadow: 0 -5px 20px rgba(0, 0, 0, 0.05);
        }

        .faixa-endereco strong {
            font-size: 1.4rem;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .faixa-endereco .info-row {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.6;
            font-weight: 500;
        }

        .faixa-endereco .cnpj {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            margin-top: 8px;
        }

        /* ===== RODAPÉ DISCRETO ===== */
        .rodape-sistema {
            background: #1B5E20;
            /* Verde bem escuro */
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.65rem;
            padding: 12px 40px;
            display: flex;
            justify-content: space-between;
            letter-spacing: 0.5px;
            margin: 0 30px 30px 30px;
            /* Alinhado com a faixa de endereço */
            border-radius: 0 0 20px 20px;
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
                <div class="header-badge">🔥 OFERTAS ESPECIAIS</div>
                <div class="header-sub">Preços que você não pode perder!</div>
                <div class="header-data">{{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [de] Y') }}</div>
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
        $headline = $linhasCopy['headline'] ?? 'Ofertas Imperdíveis da Semana!';
        $subtitulo = $linhasCopy['subtitulo'] ?? 'Corra, são por tempo limitado!';
    @endphp

    <div class="faixa-copy">
        <div class="headline">{{ $headline }}</div>
        <div class="subtitulo">{{ $subtitulo }}</div>
    </div>

    <div class="label-secao">⭐ PROMOÇÕES SELECIONADAS ⭐</div>

    @php $qtyClass = count($produtos) <= 9 ? 'qty-' . count($produtos) : 'qty-many'; @endphp
    <div class="grid-produtos {{ $qtyClass }}">
        @forelse($produtos as $i => $prod)
            <div class="card {{ $i === 0 ? 'card-destaque' : '' }}">
                <div class="card-topo">
                    @if(!empty($prod['imagem_url']))
                        <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}"
                            style="max-height:160px;max-width:100%;object-fit:contain;">
                    @else
                        <img src="https://placehold.co/150x150/ECEFF1/90A4AE?text=Oferta" style="opacity:0.5">
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
            📍 {{ $loja['endereco'] ?? '' }} — {{ $loja['cidade'] ?? '' }}&nbsp;&nbsp;|&nbsp;&nbsp;📞
            {{ $loja['telefone'] ?? '' }}
        </div>
        <div class="cnpj">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif</div>
    </div>

    <div class="rodape-sistema">
        <span>Nº {{ $campaign->id ?? '--' }} &nbsp;|&nbsp; {{ $campaign->name ?? '' }}</span>
        <span>Criado pelo MaxCheckout ✦ MaxDivulga</span>
    </div>

</body>

</html>