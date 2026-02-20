<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --ouro: #C9A84C;
            --marfim: #FAF8F5;
            --dark: #1C1C1E;
            --muted: #777;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--marfim);
            width: 1080px;
            min-height: 1920px;
            display: flex;
            flex-direction: column;
        }

        .cabecalho {
            background: var(--dark);
            padding: 50px 55px 40px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .cabecalho-loja .nome {
            font-family: 'Playfair Display', serif;
            font-size: 2.4rem;
            font-weight: 900;
            color: var(--ouro);
            letter-spacing: -0.5px;
        }

        .cabecalho-loja .slogan {
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.45);
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-top: 4px;
        }

        .cabecalho-data {
            font-size: 0.78rem;
            color: rgba(255, 255, 255, 0.35);
            letter-spacing: 1px;
            text-transform: uppercase;
        }

        .strip-ouro {
            background: var(--ouro);
            height: 4px;
        }

        .secao-copy {
            padding: 42px 55px 36px;
            background: var(--marfim);
            border-bottom: 1px solid #e8e3da;
        }

        .copy-badge {
            display: inline-block;
            border: 1.5px solid var(--ouro);
            color: var(--ouro);
            font-size: 0.7rem;
            letter-spacing: 3px;
            text-transform: uppercase;
            padding: 4px 14px;
            border-radius: 99px;
            margin-bottom: 14px;
        }

        .copy-headline {
            font-family: 'Playfair Display', serif;
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--dark);
            line-height: 1.2;
        }

        .copy-sub {
            font-size: 0.95rem;
            color: var(--muted);
            margin-top: 10px;
            line-height: 1.6;
        }

        .label-colecao {
            padding: 30px 55px 14px;
            font-size: 0.72rem;
            letter-spacing: 3.5px;
            color: var(--muted);
            text-transform: uppercase;
        }

        .linha-divisora {
            height: 1px;
            background: #e0dbd0;
            margin: 0 55px 24px;
        }

        .lista-produtos {
            padding: 0 55px;
            display: flex;
            flex-direction: column;
            gap: 20px;
            flex: 1;
        }

        .produto-item {
            display: flex;
            align-items: center;
            gap: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.06);
        }

        .produto-simbolo {
            width: 90px;
            min-height: 90px;
            background: var(--dark);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }

        .produto-info {
            flex: 1;
            padding: 18px 22px;
        }

        .produto-nome {
            font-weight: 600;
            font-size: 0.97rem;
            color: var(--dark);
        }

        .produto-preco-de {
            font-size: 0.75rem;
            color: #bbb;
            text-decoration: line-through;
            margin-top: 2px;
        }

        .produto-preco-por {
            font-family: 'Playfair Display', serif;
            font-size: 1.55rem;
            font-weight: 700;
            color: var(--dark);
            margin-top: 2px;
        }

        .produto-preco-por span {
            font-size: 0.9rem;
            font-weight: 400;
        }

        .produto-tag {
            margin-left: auto;
            padding: 0 22px;
            display: flex;
            align-items: center;
        }

        .produto-badge {
            background: var(--ouro);
            color: var(--dark);
            font-size: 0.72rem;
            font-weight: 700;
            letter-spacing: 1px;
            padding: 6px 14px;
            border-radius: 6px;
            text-transform: uppercase;
        }

        .rodape-loja {
            margin-top: auto;
            background: var(--dark);
            padding: 28px 55px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .rodape-loja .contato {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.85rem;
            line-height: 1.8;
        }

        .rodape-loja .contato strong {
            color: var(--ouro);
            float: left;
            display: block;
            margin-bottom: 4px;
        }

        .rodape-cnpj {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.3);
        }

        .rodape-sistema {
            background: #111;
            color: rgba(255, 255, 255, 0.25);
            font-size: 0.58rem;
            padding: 7px 55px;
            display: flex;
            justify-content: space-between;
            letter-spacing: 0.5px;
        }
    </style>
</head>

<body>

    <div class="cabecalho">
        <div class="cabecalho-loja">
            <div class="nome">
                @if(!empty($loja['logo_url']))
                    <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}"
                        style="max-height:45px;max-width:150px;object-fit:contain;vertical-align:middle;">
                @else
                    {{ $loja['nome'] ?? 'Sua Loja' }}
                @endif
            </div>
            <div class="slogan">Produtos Selecionados com Qualidade</div>
        </div>
        <div class="cabecalho-data">{{ \Carbon\Carbon::now()->isoFormat('MMMM [de] Y') }}</div>
    </div>
    <div class="strip-ouro"></div>

    @php
        $linhas = [];
        if ($copyTexto) {
            preg_match('/HEADLINE:\s*(.+)/i', $copyTexto, $h);
            preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto, $s);
            $linhas['headline'] = trim($h[1] ?? '');
            $linhas['subtitulo'] = trim($s[1] ?? '');
        }
        $headline = $linhas['headline'] ?? 'Uma Curadoria Impecável de Ofertas';
        $subtitulo = $linhas['subtitulo'] ?? 'Qualidade e economia reunidas para você.';
    @endphp

    <div class="secao-copy">
        <span class="copy-badge">Esta Semana</span>
        <div class="copy-headline">{{ $headline }}</div>
        <div class="copy-sub">{{ $subtitulo }}</div>
    </div>

    <div class="label-colecao">Seleção de Produtos em Destaque</div>
    <div class="linha-divisora"></div>

    <div class="lista-produtos">
        @forelse($produtos as $prod)
            <div class="produto-item">
                <div class="produto-simbolo">
                    @if(!empty($prod['imagem_url']))
                        <img src="{{ $prod['imagem_url'] }}" alt="{{ $prod['nome'] }}"
                            style="max-height:80px;max-width:80px;object-fit:contain;">
                    @else
                        🛍️
                    @endif
                </div>
                <div class="produto-info">
                    <div class="produto-nome">{{ $prod['nome'] }}</div>
                    <div class="produto-preco-de">antes R$ {{ $prod['preco_original'] }}</div>
                    <div class="produto-preco-por"><span>R$&nbsp;</span>{{ $prod['preco_novo'] }}</div>
                </div>
                <div class="produto-tag">
                    <span class="produto-badge">Oferta</span>
                </div>
            </div>
        @empty
            <div style="text-align:center;padding:50px;color:#bbb;font-size:0.9rem;">Nenhum produto selecionado.</div>
        @endforelse
    </div>

    <div class="rodape-loja" style="margin-top:42px;">
        <div class="contato">
            <strong>{{ $loja['nome'] ?? '' }}</strong>
            📍 {{ $loja['endereco'] ?? '' }}, {{ $loja['cidade'] ?? '' }}<br>
            📞 {{ $loja['telefone'] ?? '' }}
        </div>
        <div class="rodape-cnpj">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }} @endif</div>
    </div>

    <div class="rodape-sistema">
        <span>Nº {{ $campaign->id ?? '--' }} &nbsp;|&nbsp; {{ $campaign->name ?? '' }}</span>
        <span>Criado pelo MaxCheckout ✦ MaxDivulga</span>
    </div>

</body>

</html>