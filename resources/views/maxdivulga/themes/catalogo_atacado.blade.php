<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;900&family=Barlow:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --azul: #0D47A1;
            --ciano: #00B0FF;
            --branco: #fff;
            --cinza: #f4f6fa;
            --dark: #0a1628;
        }

        body {
            font-family: 'Barlow', Arial, sans-serif;
            background: var(--cinza);
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
        }

        .header {
            background: var(--dark);
            display: flex;
            gap: 0;
        }

        .header-esq {
            background: var(--azul);
            padding: 36px 40px 36px 48px;
            flex: 1;
        }

        .header-esq .pre {
            font-size: 0.72rem;
            letter-spacing: 3px;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .header-esq .nome-loja {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2.6rem;
            font-weight: 900;
            color: var(--branco);
            line-height: 1.1;
            text-transform: uppercase;
        }

        .header-esq .dados {
            margin-top: 16px;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.6);
        }

        .header-dir {
            background: var(--ciano);
            padding: 36px 48px 36px 36px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 240px;
        }

        .header-dir .titulo-cat {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 3rem;
            font-weight: 900;
            color: var(--dark);
            line-height: 0.95;
            text-transform: uppercase;
            text-align: center;
        }

        .header-dir .sub-cat {
            font-size: 0.75rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: rgba(10, 22, 40, 0.65);
            margin-top: 8px;
            text-align: center;
        }

        .faixa-copy {
            background: var(--dark);
            padding: 22px 48px;
            border-top: 3px solid var(--ciano);
        }

        .copy-headline {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2rem;
            font-weight: 900;
            color: var(--branco);
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .copy-sub {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.55);
            margin-top: 5px;
        }

        .tabela-header {
            display: grid;
            grid-template-columns: 1fr 180px 180px;
            gap: 0;
            background: var(--azul);
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.72rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 12px 48px;
        }

        .linha-produto {
            display: grid;
            grid-template-columns: 1fr 180px 180px;
            gap: 0;
            align-items: center;
            padding: 18px 48px;
            border-bottom: 1px solid #dde2ee;
            background: var(--branco);
        }

        .linha-produto:nth-child(even) {
            background: #f0f4fb;
        }

        .linha-produto:first-child {
            background: #EBF5FF;
            border-left: 4px solid var(--ciano);
        }

        .prod-nome {
            font-weight: 600;
            font-size: 0.95rem;
            color: var(--dark);
        }

        .prod-de {
            font-size: 0.75rem;
            color: #aaa;
            text-decoration: line-through;
            text-align: right;
        }

        .prod-por {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--azul);
            text-align: right;
        }

        .prod-por.destaque {
            color: var(--ciano);
        }

        .rodape-loja {
            background: var(--dark);
            padding: 22px 48px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .rodape-loja .info {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.65);
            line-height: 1.7;
        }

        .rodape-loja strong {
            color: var(--ciano);
        }

        .rodape-loja .cnpj {
            font-size: 0.65rem;
            color: rgba(255, 255, 255, 0.3);
            margin-top: 4px;
            display: block;
        }

        .rodape-sistema {
            background: #050a14;
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
        <div class="header-esq">
            <div class="pre">Lista de Preços Atacado</div>
            <div class="nome-loja">
                @if(!empty($loja['logo_url']))
                    <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}"
                        style="max-height:55px;max-width:180px;object-fit:contain;vertical-align:middle;">
                @else
                    {{ $loja['nome'] ?? 'Seu Mercado Atacado' }}
                @endif
            </div>
            <div class="dados">
                @if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif<br>
                {{ $loja['endereco'] ?? '' }} — {{ $loja['cidade'] ?? '' }}
            </div>
        </div>
        <div class="header-dir">
            <div class="titulo-cat">TABELA<br>DE<br>PREÇOS</div>
            <div class="sub-cat">{{ \Carbon\Carbon::now()->isoFormat('MMMM / Y') }}</div>
        </div>
    </div>

    @php
        preg_match('/HEADLINE:\s*(.+)/i', $copyTexto ?? '', $h);
        preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto ?? '', $s);
        $headline = trim($h[1] ?? 'Melhores preços para revenda!');
        $subtitulo = trim($s[1] ?? 'Trabalhamos com qualidade e condições imbatíveis para seu negócio.');
    @endphp

    <div class="faixa-copy">
        <div class="copy-headline">{{ $headline }}</div>
        <div class="copy-sub">{{ $subtitulo }}</div>
    </div>

    <div class="tabela-header">
        <span>Produto</span>
        <span style="text-align:right;">Preço Tabela</span>
        <span style="text-align:right;">Preço Atacado</span>
    </div>

    @forelse($produtos as $i => $prod)
        <div class="linha-produto">
            <div class="prod-nome">{{ $prod['nome'] }}</div>
            <div class="prod-de">R$ {{ $prod['preco_original'] }}</div>
            <div class="prod-por {{ $i === 0 ? 'destaque' : '' }}">R$ {{ $prod['preco_novo'] }}</div>
        </div>
    @empty
        <div style="padding:40px;text-align:center;color:#aaa;">Nenhum produto.</div>
    @endforelse

    <div class="rodape-loja">
        <div class="info">
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