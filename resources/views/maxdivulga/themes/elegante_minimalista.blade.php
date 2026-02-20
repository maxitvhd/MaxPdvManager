<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Elegante Minimalista</title>
    <style>
        body {
            font-family: 'Helvetica Neue', sans-serif;
            background: #fafafa;
            color: #222;
            margin: 0;
            padding: 40px;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .title {
            font-size: 2em;
            letter-spacing: 2px;
            font-weight: 300;
            margin: 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 1em;
            color: #888;
            margin-top: 10px;
            font-style: italic;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
        }

        .product {
            background: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .product-name {
            font-weight: 500;
            font-size: 1em;
            margin-bottom: 10px;
            text-transform: capitalize;
        }

        .price-new {
            color: #111;
            font-size: 1.2em;
            font-weight: bold;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            border-top: 1px solid #eee;
            padding-top: 20px;
            font-size: 0.8em;
            color: #aaa;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">{{ $campaign->name ?? 'Nova Coleção' }}</div>
        <div class="subtitle">{{ $campaign->copy ?? 'Descubra a elegância nos mínimos detalhes.' }}</div>
    </div>

    <div class="grid">
        @for($i = 1; $i <= 6; $i++)
            <div class="product">
                <div class="product-name">Item Exclusivo {{ $i }}</div>
                <div class="price-new">R$ 1{{$i}}9,00</div>
            </div>
        @endfor
    </div>

    <div class="footer">
        A elegância que você merece. Visite nossa loja.
    </div>
</body>

</html>