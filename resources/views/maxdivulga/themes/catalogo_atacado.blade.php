<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Catálogo Atacado</title>
    <style>
        body {
            font-family: 'Verdana', sans-serif;
            background: #f0f0f0;
            color: #333;
            margin: 0;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }

        th {
            background: #0d47a1;
            color: #fff;
            font-weight: bold;
        }

        .header {
            background: #1976d2;
            color: #fff;
            padding: 15px;
            margin-bottom: 20px;
        }

        .title {
            margin: 0;
            font-size: 1.5em;
        }

        .subtitle {
            font-size: 0.9em;
            margin-top: 5px;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1 class="title">{{ $campaign->name ?? 'Tabela de Preços Atacado' }}</h1>
        <div class="subtitle">{{ $campaign->copy ?? 'Condições especiais para lojistas e revendedores.' }}</div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Descrição do Produto</th>
                <th>Qtd Min.</th>
                <th>Preço Un.</th>
            </tr>
        </thead>
        <tbody>
            @for($i = 1; $i <= 10; $i++)
                <tr>
                    <td>{{ 1000 + $i }}</td>
                    <td>Caixa Produto Base {{ $i }}</td>
                    <td>12 un.</td>
                    <td><strong>R$ {{$i}}4,50</strong></td>
                </tr>
            @endfor
        </tbody>
    </table>
</body>

</html>