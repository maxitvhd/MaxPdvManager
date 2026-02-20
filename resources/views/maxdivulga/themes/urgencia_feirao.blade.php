<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Urgência Feirão</title>
    <style>
        body {
            font-family: 'Impact', sans-serif;
            background: #000;
            color: #fff;
            margin: 0;
            padding: 20px;
            text-align: center;
        }

        .header {
            background: #ffeb3b;
            color: #000;
            padding: 20px;
            margin-bottom: 20px;
            transform: rotate(-2deg);
        }

        .title {
            font-size: 3em;
            margin: 0;
            text-transform: uppercase;
            text-shadow: 2px 2px 0px #fff;
        }

        .subtitle {
            font-size: 1.5em;
            background: #e53935;
            color: #fff;
            display: inline-block;
            padding: 5px 15px;
            margin-top: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .product {
            background: #222;
            padding: 20px;
            border: 3px solid #ffeb3b;
            border-radius: 5px;
        }

        .product-name {
            font-size: 1.5em;
            margin-bottom: 5px;
            color: #ffeb3b;
        }

        .price-new {
            color: #e53935;
            font-size: 2.5em;
            text-shadow: 1px 1px 0px #fff;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">{{ $campaign->name ?? 'FIM DE ESTOQUE!' }}</div>
        <div class="subtitle">{{ $campaign->copy ?? 'SÓ HOJE! NÃO PERCA TEMPO!' }}</div>
    </div>

    <div class="grid">
        @for($i = 1; $i <= 4; $i++)
            <div class="product">
                <div class="product-name">PRODUTO MATADOR {{ $i }}</div>
                <div class="price-new">POR: R$ {{$i}}9,99</div>
            </div>
        @endfor
    </div>
</body>

</html>