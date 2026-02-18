@extends('layouts.user_type.auth')


<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Produto</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Buscar Produto pelo Código de Barras</h2>
    <input type="text" id="codigo_barras" placeholder="Digite o código de barras">
    <button onclick="buscarProduto()">Buscar</button>

    <div id="resultado"></div>

    <script>
        function buscarProduto() {
            var codigoBarras = $('#codigo_barras').val();

            $.ajax({
                url: '/buscar-produto',
                type: 'GET',
                data: { codigo_barras: codigoBarras },
                success: function(data) {
                    $('#resultado').html(`
                        <p><strong>Nome:</strong> ${data.nome}</p>
                        <p><strong>Marca:</strong> ${data.marca}</p>
                        <p><strong>Categoria:</strong> ${data.categoria}</p>
                        ${data.imagem ? `<img src="${data.imagem}" width="100">` : ''}
                    `);
                },
                error: function() {
                    $('#resultado').html('<p>Produto não encontrado ou erro na busca.</p>');
                }
            });
        }
    </script>

</body>
</html>
