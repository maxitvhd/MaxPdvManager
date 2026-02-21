<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --vermelho: #D32F2F;
            --amarelo: #FDD835;
            --branco: #fff;
            --cinza: #f5f5f5;
            --dark: #1a1a1a;
        }

        body {
            font-family: 'Poppins', Arial, sans-serif;
            background: #fff;
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER ===== */
        .header {
            background: var(--vermelho);
            color: var(--branco);
            display: flex;
            align-items: stretch;
            min-height: 200px;
        }

        .header-logo {
            background: #b71c1c;
            padding: 30px 28px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-width: 280px;
        }

        .header-logo .nome-loja {
            font-size: 1.6rem;
            font-weight: 900;
            text-align: center;
            letter-spacing: -0.5px;
            line-height: 1.2;
            text-transform: uppercase;
        }

        .header-logo .tagline {
            font-size: 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            margin-top: 6px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .header-info {
            flex: 1;
            padding: 28px 32px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .header-badge {
            display: inline-block;
            background: var(--amarelo);
            color: var(--dark);
            font-weight: 900;
            font-size: 2rem;
            padding: 8px 22px;
            border-radius: 8px;
            letter-spacing: -0.5px;
            text-transform: uppercase;
        }

        .header-sub {
            font-size: 1.4rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.9);
            margin-top: 8px;
        }

        .header-data {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.65);
            margin-top: 4px;
        }

        /* ===== FAIXA COPY ===== */
        .faixa-copy {
            background: var(--amarelo);
            color: var(--dark);
            padding: 18px 36px;
            text-align: center;
        }

        .faixa-copy .headline {
            font-size: 1.55rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.5px;
            line-height: 1.2;
        }

        .faixa-copy .subtitulo {
            font-size: 0.95rem;
            font-weight: 600;
            color: #5d4037;
            margin-top: 4px;
        }

        /* ===== GRID PRODUTOS — adaptável pela quantidade ===== */
        .label-secao {}

        .grid-produtos {
            display: grid;
            gap: 14px;
            padding: 20px;
            background: #f7f7f7;
        }

        /* 1 produto: 1 coluna centralizada bem grande */
        .grid-produtos.qty-1 {
            grid-template-columns: repeat(1, 1fr);
            max-width: 480px;
            margin: 0 auto;
        }

        /* 2 a 3 produtos: 2 ou 3 colunas */
        .grid-produtos.qty-2 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-produtos.qty-3 {
            grid-template-columns: repeat(3, 1fr);
        }

        /* 4 a 6 produtos: 3 colunas */
        .grid-produtos.qty-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .grid-produtos.qty-5,
        .grid-produtos.qty-6 {
            grid-template-columns: repeat(3, 1fr);
        }

        /* 7 a 20 produtos: 4 colunas */
        .grid-produtos.qty-7,
        .grid-produtos.qty-8,
        .grid-produtos.qty-9,
        .grid-produtos.qty-10,
        .grid-produtos.qty-many {
            grid-template-columns: repeat(4, 1fr);
        }

        .card {
            background: var(--branco);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding-bottom: 14px;
        }

        .card-destaque {
            background: var(--vermelho);
        }

        .card-topo {
            width: 100%;
            background: #fafafa;
            padding: 20px 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 140px;
        }

        /* Imagem maior quando poucos produtos */
        .qty-1 .card-topo,
        .qty-2 .card-topo,
        .qty-3 .card-topo {
            min-height: 160px;
        }

        .qty-4 .card-topo,
        .qty-5 .card-topo,
        .qty-6 .card-topo {
            min-height: 120px;
        }

        .card-topo span {
            font-size: 2rem;
            opacity: .6;
        }

        /* Fontes maiores quando poucos produtos */
        .qty-1 .card-nome,
        .qty-2 .card-nome {
            font-size: 1rem;
        }

        .qty-3 .card-nome {
            font-size: 0.9rem;
        }

        .qty-1 .card-preco-por {
            font-size: 1.6rem;
        }

        .qty-2 .card-preco-por {
            font-size: 1.4rem;
        }

        .qty-3 .card-preco-por {
            font-size: 1.3rem;
        }

        .tag-oferta {
            background: var(--vermelho);
            color: var(--branco);
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 1.5px;
            padding: 3px 10px;
            width: 100%;
            text-align: center;
            text-transform: uppercase;
        }

        .card-destaque .tag-oferta {
            background: #fff;
            color: var(--vermelho);
        }

        .card-nome {
            font-size: 0.78rem;
            font-weight: 600;
            color: #333;
            text-align: center;
            padding: 8px 10px 4px;
            line-height: 1.3;
        }

        .card-destaque .card-nome {
            color: #fff;
        }

        .card-preco-de {
            font-size: 0.72rem;
            color: #aaa;
            text-decoration: line-through;
            text-align: center;
        }

        .card-destaque .card-preco-de {
            color: rgba(255, 255, 255, 0.65);
        }

        .card-preco-por {
            background: var(--amarelo);
            color: var(--dark);
            font-weight: 900;
            font-size: 1.15rem;
            text-align: center;
            padding: 6px 12px;
            border-radius: 6px;
            margin: 6px 10px 0;
            width: calc(100% - 20px);
        }

        .card-destaque .card-preco-por {
            background: #fff;
            color: var(--vermelho);
        }

        /* ===== FAIXA ENDEREÇO ===== */
        .faixa-endereco {
            background: var(--dark);
            color: var(--branco);
            padding: 14px 28px;
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-top: auto;
        }

        .faixa-endereco strong {
            font-size: 1.1rem;
        }

        .faixa-endereco .info-row {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.75);
            line-height: 1.6;
        }

        .faixa-endereco .cnpj {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.4);
            margin-top: 4px;
        }

        /* ===== RODAPÉ DISCRETO ===== */
        .rodape-sistema {
            background: #111;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.58rem;
            padding: 7px 28px;
            display: flex;
            justify-content: space-between;
            letter-spacing: 0.5px;
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
                        <span>🛒</span>
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