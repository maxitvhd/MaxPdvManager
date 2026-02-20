<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Destaque Único</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #FF6B6B, #556270);
            color: #fff;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 90%;
        }

        .tag {
            background: #FFD166;
            color: #333;
            font-weight: bold;
            padding: 5px 15px;
            border-radius: 20px;
            display: inline-block;
            margin-bottom: 20px;
            font-size: 0.9em;
            text-transform: uppercase;
        }

        .title {
            font-size: 2.2em;
            margin: 0 0 15px 0;
            font-weight: 800;
        }

        .copy {
            font-size: 1.1em;
            line-height: 1.5;
            margin-bottom: 30px;
        }

        .price {
            font-size: 3em;
            font-weight: 900;
            color: #06D6A0;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="tag">Produto Estrela</div>
        <h1 class="title">{{ $campaign->name ?? 'Smartphone Super X' }}</h1>
        <p class="copy">
            {{ $campaign->copy ?? 'Desempenho máximo, câmera de cinema. O melhor da tecnologia nas suas mãos. Oferta imperdível por tempo limitado!' }}
        </p>
        <div class="price">R$ 1.999</div>
    </div>
</body>

</html>