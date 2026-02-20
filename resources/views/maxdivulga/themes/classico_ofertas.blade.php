<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Clássico Ofertas</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: #fff;
            color: #333;
            margin: 0;
            padding: 20px;
        }

        .header {
            background: #e53935;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px;
        }

        .title {
            font-size: 2.5em;
            font-weight: 900;
            margin: 0;
            text-transform: uppercase;
        }

        .subtitle {
            font-size: 1.2em;
            font-weight: bold;
            background: #ffeb3b;
            color: #d32f2f;
            display: inline-block;
            padding: 5px 15px;
            border-radius: 20px;
            margin-top: 10px;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 20px;
        }

        .product {
            border: 2px dashed #e53935;
            padding: 15px;
            text-align: center;
            border-radius: 10px;
            position: relative;
        }

        .product-name {
            font-weight: bold;
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        .price-old {
            text-decoration: line-through;
            color: #757575;
            font-size: 0.9em;
        }

        .price-new {
            color: #e53935;
            font-size: 1.8em;
            font-weight: 900;
        }

        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #555;
        }
    </style>
</head>

<body>
    <div class="header">
        <div class="title">{{ $campaign->name ?? 'SUPER OFERTAS' }}</div>
        <div class="subtitle">{{ $campaign->copy ?? 'O patrão ficou maluco! Venha conferir!' }}</div>
    </div>

    <div class="grid">
        @forelse($produtos as $prod)
            <div class="product">
                <div class="product-name">{{ $prod['nome'] }}</div>
                <div class="price-old">R$ {{ $prod['preco_original'] }}</div>
                <div class="price-new">R$ {{ $prod['preco_novo'] }}</div>
            </div>
        @empty
            <div class="product">Nenhum produto selecionado.</div>
        @endforelse
    </div>

    <div class="footer">
        Ofertas válidas enquanto durarem os estoques. Imagens meramente ilustrativas.
    </div>
</body>

</html>