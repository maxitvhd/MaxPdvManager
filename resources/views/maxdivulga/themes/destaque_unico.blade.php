<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@700;900&family=Lato:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --roxo: #6A1B9A;
            --rosa: #E91E8C;
            --amarelo: #FFC107;
            --branco: #fff;
            --dark: #1a0a2e;
        }

        body {
            font-family: 'Lato', Arial, sans-serif;
            background: var(--dark);
            width: 1080px;
            height: 1920px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER ===== */
        .header {
            width: 100%;
            background: linear-gradient(160deg, var(--dark) 0%, var(--roxo) 100%);
            padding: 50px 60px 44px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .badge-destaque {
            display: inline-block;
            background: var(--rosa);
            color: var(--branco);
            font-family: 'Montserrat', sans-serif;
            font-size: 0.75rem;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            font-weight: 900;
            padding: 5px 16px;
            border-radius: 4px;
        }

        .nome-loja {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--branco);
            margin-top: 14px;
            line-height: 1.1;
        }

        .header-data {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 1.5px;
            text-transform: uppercase;
            margin-top: 6px;
        }

        .header-dir {
            text-align: right;
        }

        .header-dir .label {
            font-size: 0.72rem;
            letter-spacing: 2px;
            color: rgba(255, 255, 255, 0.45);
            text-transform: uppercase;
        }

        .header-dir .detalhe {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--amarelo);
            margin-top: 4px;
        }

        /* ===== PRODUTO DESTAQUE (1o produto) ===== */
        .destaque-bloco {
            width: 100%;
            background: linear-gradient(135deg, var(--roxo), var(--rosa));
            padding: 60px;
            display: flex;
            align-items: center;
            gap: 60px;
        }

        .destaque-emoji {
            font-size: 7rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 24px;
            padding: 26px 36px;
        }

        .destaque-info .label-prod {
            font-size: 0.72rem;
            letter-spacing: 2.5px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .destaque-info h2 {
            font-family: 'Montserrat', sans-serif;
            font-size: 2.4rem;
            font-weight: 900;
            color: var(--branco);
            line-height: 1.1;
            text-transform: uppercase;
        }

        .destaque-info .preco-de {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: line-through;
            margin-top: 14px;
        }

        .destaque-info .preco-por {
            font-family: 'Montserrat', sans-serif;
            font-size: 4rem;
            font-weight: 900;
            color: var(--amarelo);
            line-height: 1;
            margin-top: 6px;
        }

        .destaque-info .preco-por small {
            font-size: 1.8rem;
        }

        /* ===== COPY ===== */
        .faixa-copy {
            width: 100%;
            background: rgba(255, 255, 255, 0.05);
            border-top: 1.5px solid rgba(255, 255, 255, 0.1);
            border-bottom: 1.5px solid rgba(255, 255, 255, 0.1);
            padding: 24px 60px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .copy-icone {
            font-size: 1.8rem;
        }

        .copy-headline {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--branco);
            text-transform: uppercase;
            letter-spacing: -0.3px;
        }

        .copy-sub {
            font-size: 0.85rem;
            color: rgba(255, 255, 255, 0.55);
            margin-top: 3px;
        }

        /* ===== OUTROS PRODUTOS ===== */
        .label-mais {
            padding: 28px 60px 14px;
            font-size: 0.7rem;
            letter-spacing: 3.5px;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            width: 100%;
        }

        .grid-mais {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 14px;
            padding: 0 60px 40px;
            width: 100%;
        }

        .card-mini {
            background: rgba(255, 255, 255, 0.07);
            border-radius: 10px;
            padding: 20px 18px;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .mini-nome {
            font-size: 0.85rem;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.85);
            line-height: 1.3;
            margin-bottom: 8px;
        }

        .mini-de {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.35);
            text-decoration: line-through;
        }

        .mini-por {
            font-family: 'Montserrat', sans-serif;
            font-size: 1.45rem;
            font-weight: 900;
            color: var(--amarelo);
            margin-top: 2px;
        }

        /* ===== RODAPÉ ===== */
        .faixa-endereco {
            width: 100%;
            background: #0d0620;
            padding: 18px 60px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .end-info {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.55);
            line-height: 1.7;
        }

        .end-info strong {
            color: var(--rosa);
        }

        .end-info .cnpj {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.25);
            display: block;
            margin-top: 3px;
        }

        .rodape-sistema {
            width: 100%;
            background: #050510;
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.57rem;
            padding: 7px 60px;
            display: flex;
            justify-content: space-between;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div>
            <span class="badge-destaque">⭐ OFERTA EXCLUSIVA</span>
            <div class="nome-loja">
                @if(!empty($loja['logo_url']))
                    <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}"
                        style="max-height:50px;max-width:160px;object-fit:contain;vertical-align:middle;">
                @else
                    {{ $loja['nome'] ?? 'Sua Loja' }}
                @endif
            </div>
            <div class="header-data">{{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM [de] Y') }}</div>
        </div>
        <div class="header-dir">
            <div class="label">Produto em Destaque</div>
            <div class="detalhe">Preço Especial ↓</div>
        </div>
    </div>

    @php
        $primeiro = $produtos[0] ?? null;
        $restantes = array_slice($produtos, 1);
        preg_match('/HEADLINE:\s*(.+)/i', $copyTexto ?? '', $h);
        preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto ?? '', $s);
        $headline = trim($h[1] ?? 'Você não pode perder essa oportunidade!');
        $subtitulo = trim($s[1] ?? 'Corra e garanta o seu antes que acabe!');
    @endphp

    @if($primeiro)
        <div class="destaque-bloco">
            <div class="destaque-emoji">@if(!empty($primeiro["imagem_url"]))<img src="{{ $primeiro["imagem_url"] }}"
            alt="{{ $primeiro["nome"] }}" style="max-height:150px;max-width:150px;object-fit:contain;">@else🛒@endif
            </div>
            <div class="destaque-info">
                <div class="label-prod">Produto em Destaque</div>
                <h2>{{ $primeiro['nome'] }}</h2>
                <div class="preco-de">de R$ {{ $primeiro['preco_original'] }}</div>
                <div class="preco-por"><small>R$&nbsp;</small>{{ $primeiro['preco_novo'] }}</div>
            </div>
        </div>
    @endif

    <div class="faixa-copy">
        <span class="copy-icone">✨</span>
        <div>
            <div class="copy-headline">{{ $headline }}</div>
            <div class="copy-sub">{{ $subtitulo }}</div>
        </div>
    </div>

    @if(count($restantes) > 0)
        <div class="label-mais">Mais Produtos em Promoção</div>
        <div class="grid-mais">
            @foreach($restantes as $prod)
                <div class="card-mini">
                    <div class="mini-nome">{{ $prod['nome'] }}</div>
                    <div class="mini-de">de R$ {{ $prod['preco_original'] }}</div>
                    <div class="mini-por">R$ {{ $prod['preco_novo'] }}</div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="faixa-endereco">
        <div class="end-info">
            <strong>{{ $loja['nome'] ?? '' }}</strong><br>
            📍 {{ $loja['endereco'] ?? '' }} — {{ $loja['cidade'] ?? '' }}<br>
            📞 {{ $loja['telefone'] ?? '' }}
            <span class="cnpj">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif</span>
        </div>
    </div>

    <div class="rodape-sistema">
        <span>Nº {{ $campaign->id ?? '--' }} &nbsp;|&nbsp; {{ $campaign->name ?? '' }}</span>
        <span>MaxCheckout ✦ MaxDivulga</span>
    </div>

</body>

</html>