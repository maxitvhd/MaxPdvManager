<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Barlow+Condensed:wght@600;700;900&family=Barlow:wght@400;500;600;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --azul-escuro: #0D47A1;
            /* Renomeado para clareza */
            --ciano-destaque: #00B0FF;
            /* Renomeado para clareza */
            --branco: #ffffff;
            --cinza-fundo: #f4f6fa;
            --dark: #0a1628;
            --borda-suave: #e0e6ed;
        }

        body {
            font-family: 'Barlow', Arial, sans-serif;
            background: var(--cinza-fundo);
            width: 1080px;
            /* Largura fixa mantida para geração de PDF/Imagem */
            min-height: 1920px;
            display: flex;
            flex-direction: column;
            color: var(--dark);
        }

        /* --- HEADER APRIMORADO --- */
        .header {
            background: var(--dark);
            display: flex;
            gap: 0;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            /* Sombra para profundidade */
            position: relative;
            z-index: 10;
        }

        .header-esq {
            background: linear-gradient(135deg, var(--azul-escuro) 0%, #1565c0 100%);
            /* Gradiente sutil */
            padding: 40px 48px;
            flex: 1;
        }

        .header-esq .pre {
            font-size: 0.8rem;
            letter-spacing: 4px;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            margin-bottom: 12px;
            font-weight: 600;
        }

        .header-esq .nome-loja {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2.8rem;
            font-weight: 900;
            color: var(--branco);
            line-height: 1.1;
            text-transform: uppercase;
        }

        .header-esq .dados {
            margin-top: 20px;
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.7);
            line-height: 1.5;
        }

        .header-dir {
            background: var(--ciano-destaque);
            padding: 40px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-width: 260px;
            /* Detalhe visual de corte */
            clip-path: polygon(0 0, 100% 0, 100% 100%, 10% 100%);
        }

        .header-dir .titulo-cat {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 3.2rem;
            font-weight: 900;
            color: var(--dark);
            line-height: 0.9;
            text-transform: uppercase;
            text-align: center;
        }

        .header-dir .sub-cat {
            font-size: 0.85rem;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--dark);
            font-weight: 700;
            margin-top: 12px;
            text-align: center;
            background: rgba(255, 255, 255, 0.2);
            padding: 4px 12px;
            border-radius: 4px;
        }

        /* --- FAIXA COPY --- */
        .faixa-copy {
            background: var(--branco);
            padding: 25px 48px;
            border-bottom: 2px solid var(--borda-suave);
        }

        .copy-headline {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2.2rem;
            font-weight: 900;
            color: var(--azul-escuro);
            text-transform: uppercase;
            letter-spacing: -0.5px;
        }

        .copy-sub {
            font-size: 1rem;
            color: #666;
            margin-top: 8px;
            font-weight: 500;
        }

        /* --- TABELA DE PRODUTOS PROFISSIONAL --- */
        .container-tabela {
            background: var(--branco);
            margin: 20px 48px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
            overflow: hidden;
        }

        .tabela-header {
            display: grid;
            /* Ajuste nas colunas para dar mais espaço ao nome */
            grid-template-columns: 2fr 1fr 1.2fr;
            gap: 20px;
            background: var(--azul-escuro);
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.75rem;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            font-weight: 700;
            padding: 15px 30px;
        }

        .linha-produto {
            display: grid;
            grid-template-columns: 2fr 1fr 1.2fr;
            gap: 20px;
            align-items: center;
            padding: 20px 30px;
            /* Mais espaçamento interno */
            border-bottom: 1px solid var(--borda-suave);
            background: var(--branco);
            transition: background 0.2s;
        }

        /* Efeito hover suave para melhor usabilidade em tela */
        .linha-produto:hover {
            background: #f9fbff;
        }

        /* Linhas alternadas mais sutis */
        .linha-produto:nth-child(even) {
            background: #fcfcfc;
        }

        .linha-produto:nth-child(even):hover {
            background: #f9fbff;
        }


        .prod-nome {
            font-weight: 700;
            font-size: 1.1rem;
            color: var(--dark);
            line-height: 1.3;
        }

        /* Bloco de preços para melhor alinhamento */
        .price-block {
            text-align: right;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .prod-de {
            font-size: 0.85rem;
            color: #999;
            text-decoration: line-through;
            font-weight: 500;
        }

        .prod-por {
            font-family: 'Barlow Condensed', sans-serif;
            font-size: 2rem;
            /* Muito maior */
            font-weight: 900;
            color: var(--ciano-destaque);
            /* Cor de destaque para todos */
            line-height: 1;
            margin-top: 4px;
        }

        /* Adiciona o R$ via CSS para ficar mais limpo */
        .prod-por::before {
            content: 'R$ ';
            font-size: 1rem;
            font-weight: 700;
            vertical-align: middle;
            color: var(--azul-escuro);
            opacity: 0.8;
        }

        /* --- RODAPÉ --- */
        .rodape-loja {
            background: var(--dark);
            padding: 30px 48px;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: auto;
            border-top: 4px solid var(--ciano-destaque);
            color: rgba(255, 255, 255, 0.8);
        }

        .rodape-col {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .rodape-loja strong {
            color: var(--ciano-destaque);
            font-size: 1.2rem;
            text-transform: uppercase;
            margin-bottom: 5px;
            display: block;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.9rem;
        }

        .rodape-sistema {
            background: #050a14;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.6rem;
            padding: 10px 48px;
            display: flex;
            justify-content: space-between;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
    </style>
</head>

<body>

    <div class="header">
        <div class="header-esq">
            <div class="pre">Catálogo Digital B2B</div>
            <div class="nome-loja">
                @if(!empty($loja['logo_url']))
                    <img src="{{ $loja['logo_url'] }}" alt="{{ $loja['nome'] ?? '' }}"
                        style="max-height:120px;max-width:280px;object-fit:contain;vertical-align:middle;">
                @else
                    {{ $loja['nome'] ?? 'Seu Mercado Atacado' }}
                @endif
            </div>
            <div class="dados">
                Condições comerciais exclusivas para<br>revendedores e parceiros.
            </div>
        </div>
        <div class="header-dir">
            <div class="titulo-cat">TABELA<br>DE<br>PREÇOS</div>
            <div class="sub-cat">Vigência: {{ \Carbon\Carbon::now()->isoFormat('MMMM / Y') }}</div>
        </div>
    </div>

    @php
        preg_match('/HEADLINE:\s*(.+)/i', $copyTexto ?? '', $h);
        preg_match('/SUBTITULO:\s*(.+)/i', $copyTexto ?? '', $s);
        $headline = trim($h[1] ?? 'Oportunidades de Alta Margem');
        $subtitulo = trim($s[1] ?? 'Confira os itens selecionados para impulsionar suas vendas este mês.');
    @endphp

    <div class="faixa-copy">
        <div class="copy-headline">{{ $headline }}</div>
        <div class="copy-sub">{{ $subtitulo }}</div>
    </div>

    <div class="container-tabela">
        <div class="tabela-header">
            <span>Descrição do Produto</span>
            <span style="text-align:right;">Preço Base</span>
            <span style="text-align:right;">Condição Atacado</span>
        </div>

        @forelse($produtos as $i => $prod)
            <div class="linha-produto">
                <div class="prod-nome">{{ $prod['nome'] }}</div>

                <div class="price-block">
                    <div class="prod-de">R$ {{ $prod['preco_original'] }}</div>
                </div>

                <div class="price-block">
                    <div class="prod-por">{{ $prod['preco_novo'] }}</div>
                </div>
            </div>
        @empty
            <div style="padding:50px;text-align:center;color:#999;font-weight:500;">
                Nenhum produto disponível nesta seleção.
            </div>
        @endforelse
    </div>

    <div class="rodape-loja">
        <div class="rodape-col">
            <strong>{{ $loja['nome'] ?? '' }}</strong>
            <span style="font-size: 0.8rem; opacity: 0.6;">@if(!empty($loja['cnpj'])) CNPJ: {{ $loja['cnpj'] }}
            @endif</span>
        </div>

        <div class="rodape-col" style="text-align: right;">
            <div class="info-item" style="justify-content: flex-end;">
                {{ $loja['endereco'] ?? '' }} — {{ $loja['cidade'] ?? '' }} 📍
            </div>
            <div class="info-item" style="justify-content: flex-end;">
                Central de Vendas: {{ $loja['telefone'] ?? '' }} 📞
            </div>
        </div>
    </div>

    <div class="rodape-sistema">
        <span>ID: {{ $campaign->id ?? '--' }} | Campanha: {{ $campaign->name ?? 'Geral' }}</span>
        <span>Gerado via MaxCheckout B2B</span>
    </div>

</body>

</html>