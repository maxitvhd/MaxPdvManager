<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Black+Han+Sans&family=Nunito:wght@600;700;900&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --laranja: #FF6D00;
            --amarelo: #FFD600;
            --vermelho: #D50000;
            --dark: #1A1A1A;
            --branco: #fff;
        }

        body {
            font-family: 'Nunito', Arial, sans-serif;
            background: var(--dark);
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
        }

        /* ===== HEADER BOMBA ===== */
        .header {
            background: linear-gradient(135deg, var(--vermelho), var(--laranja) 60%, var(--amarelo) 100%);
            padding: 42px 48px 36px;
            display: flex;
            align-items: flex-end;
            justify-content: space-between;
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -40px;
            right: -40px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.07);
        }

        .header::after {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 20%;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.12);
        }

        .header-tag {
            background: var(--dark);
            color: var(--amarelo);
            font-size: 0.75rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 5px 16px;
            border-radius: 4px;
            margin-bottom: 12px;
            display: inline-block;
            font-weight: 700;
        }

        .header-titulo {
            font-size: 3.8rem;
            font-weight: 900;
            color: var(--branco);
            line-height: 0.95;
            text-transform: uppercase;
            letter-spacing: -2px;
            text-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
        }

        .header-titulo span {
            color: var(--amarelo);
        }

        .header-loja {
            text-align: right;
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            position: relative;
            z-index: 1;
        }

        .header-loja strong {
            display: block;
            font-size: 1.4rem;
            color: var(--branco);
        }

        /* ===== FAIXA COPY ===== */
        .faixa-copy {
            background: var(--amarelo);
            padding: 20px 48px;
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .alerta-icon {
            font-size: 2.4rem;
            animation: none;
        }

        .copy-texto .headline {
            font-size: 1.55rem;
            font-weight: 900;
            color: var(--dark);
            text-transform: uppercase;
            line-height: 1.15;
        }

        .copy-texto .sub {
            font-size: 0.88rem;
            color: #5d4037;
            font-weight: 600;
            margin-top: 2px;
        }

        /* ===== PRODUTOS ===== */
        .label-feirao {
            background: var(--vermelho);
            padding: 13px 48px;
            font-size: 0.72rem;
            letter-spacing: 4px;
            color: rgba(255, 255, 255, 0.85);
            text-transform: uppercase;
            font-weight: 700;
        }

        .grid-produtos {
            display: grid;
            gap: 16px;
            padding: 22px 48px;
            flex: 1;
        }

        .grid-produtos.qty-1 {
            grid-template-columns: repeat(1, 1fr);
            max-width: 420px;
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
            grid-template-columns: repeat(3, 1fr);
        }

        .card {
            background: #252525;
            border-radius: 12px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            border: 1.5px solid #333;
            position: relative;
        }

        .card-principal {
            background: var(--laranja);
            border-color: var(--laranja);
        }

        .card::before {
            content: '🔥';
            position: absolute;
            top: 8px;
            right: 8px;
            font-size: 1.1rem;
        }

        .card-topo {
            padding: 22px;
            display: flex;
            justify-content: center;
            align-items: center;
            background: rgba(255, 255, 255, 0.06);
            min-height: 90px;
            font-size: 2.4rem;
        }

        .card-info {
            padding: 14px 16px 16px;
        }

        .card-nome {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.8);
            font-weight: 600;
            line-height: 1.4;
            margin-bottom: 8px;
        }

        .card-principal .card-nome {
            color: var(--dark);
        }

        .card-de {
            font-size: 0.7rem;
            color: #666;
            text-decoration: line-through;
        }

        .card-principal .card-de {
            color: rgba(0, 0, 0, 0.45);
        }

        .card-preco {
            font-size: 1.6rem;
            font-weight: 900;
            color: var(--amarelo);
            line-height: 1;
        }

        .card-principal .card-preco {
            color: var(--dark);
        }

        .card-label {
            font-size: 0.6rem;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 1.5px;
            margin-top: 2px;
        }

        .card-principal .card-label {
            color: rgba(0, 0, 0, 0.45);
        }

        /* ===== RODAPÉ ===== */
        .faixa-endereco {
            background: #111;
            padding: 18px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-top: auto;
        }

        .end-texto {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
        }

        .end-texto strong {
            color: var(--laranja);
            font-size: 1rem;
        }

        .cnpj-txt {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .tel-box {
            background: var(--laranja);
            color: var(--dark);
            font-weight: 900;
            font-size: 1.05rem;
            padding: 12px 22px;
            border-radius: 10px;
            white-space: nowrap;
            text-align: center;
        }

        .tel-box small {
            display: block;
            font-size: 0.7rem;
            font-weight: 600;
            opacity: 0.7;
        }

        .rodape-sistema {
            background: #050505;
            color: rgba(255, 255, 255, 0.2);
            font-size: 0.57rem;
            padding: 7px 48px;
            display: flex;
            justify-content: space-between;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>

    <div class="header">
        <div>
            <span class="header-tag">⚡ FEIRÃO RELÂMPAGO</span>
            <div class="header-titulo">PROMOÇÃO<br><span>IM&shy;PER&shy;DÍ&shy;VEL</span></div>
        </div>
        <div class="header-loja">
            @if(!empty($loja['logo_url']))
                <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}"
                    style="max-height:50px;max-width:160px;object-fit:contain;vertical-align:middle;">
            @else
                <strong>{{ $loja['nome'] ?? 'Seu Mercado' }}</strong>
            @endif
            {{ \Carbon\Carbon::now()->isoFormat('D [de] MMMM') }}
        </div>
    </div>

    @php
        preg_match('/HEADLINE:\s*(.+)/i', $copyTexto ?? '', $h);
        preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto ?? '', $s);
        $headline = trim($h[1] ?? '🔥 Só hoje! Preços que só existem aqui!');
        $subtitulo = trim($s[1] ?? 'Aproveite enquanto durar o estoque!');
    @endphp

    <div class="faixa-copy">
        <span class="alerta-icon">⚡</span>
        <div class="copy-texto">
            <div class="headline">{{ $headline }}</div>
            <div class="sub">{{ $subtitulo }}</div>
        </div>
    </div>

    <div class="label-feirao">🏷️ &nbsp; PRODUTOS DO FEIRÃO &nbsp; 🏷️</div>

    @php $qtyClass = count($produtos) <= 9 ? 'qty-' . count($produtos) : 'qty-many'; @endphp
    <div class="grid-produtos {{ $qtyClass }}">
        @forelse($produtos as $i => $prod)
            <div class="card {{ $i === 0 ? 'card-principal' : '' }}">
                <div class="card-topo">
                    @if(!empty($prod['imagem_url']))
                        <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}"
                            style="max-height:90px;max-width:100%;object-fit:contain;">
                    @else
                        🛒
                    @endif
                </div>
                <div class="card-info">
                    <div class="card-nome">{{ $prod['nome'] }}</div>
                    <div class="card-de">de R$ {{ $prod['preco_original'] }}</div>
                    <div class="card-preco">R$ {{ $prod['preco_novo'] }}</div>
                    <div class="card-label">POR APENAS</div>
                </div>
            </div>
        @empty
            <div style="grid-column:1/-1;text-align:center;padding:50px;color:#555;">Nenhum produto.</div>
        @endforelse
    </div>

    <div class="faixa-endereco">
        <div class="end-texto">
            <strong>{{ $loja['nome'] ?? '' }}</strong><br>
            📍 {{ $loja['endereco'] ?? '' }} — {{ $loja['cidade'] ?? '' }}<br>
            <span class="cnpj-txt">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif</span>
        </div>
        <div class="tel-box">
            <small>LIGUE AGORA</small>
            {{ $loja['telefone'] ?? '' }}
        </div>
    </div>

    <div class="rodape-sistema">
        <span>Nº {{ $campaign->id ?? '--' }} &nbsp;|&nbsp; {{ $campaign->name ?? '' }}</span>
        <span>MaxCheckout ✦ MaxDivulga</span>
    </div>

</body>

</html>