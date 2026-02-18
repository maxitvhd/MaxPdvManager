@extends('layouts.user_type.auth')

@section('content')
<div class="container mt-4">
    <h2 class="text-center">Adicionar produto</h2>
    
    <div id="loadingImage" style="display:none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(255,255,255,0.8); z-index: 9999; text-align: center; padding-top: 20%;">
        <div class="spinner-border text-primary" role="status"></div>
        <p class="mt-2 font-weight-bold">Baixando imagem do servidor externo...</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('produtos.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="mb-3">
                    <label class="form-label">Pré-visualização</label>
                    <div style="min-height: 150px; display: flex; align-items: center; justify-content: center; background-color: #f8f9fa; border: 1px dashed #ced4da; border-radius: 5px;">
                        <img id="preview_imagem_visual" src="#" class="img-fluid rounded" style="max-height: 150px; display: none;">
                        <span id="placeholder_img" class="text-muted">Sem imagem</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="imagem_personalizada" class="form-label">Imagem do Produto</label>
                    <input type="file" id="imagem_personalizada" name="imagem_personalizada" class="form-control" onchange="previewPersonalizedImage(event)">
                </div>
                
                <input type="hidden" name="url_image" id="url_image">
            </div>

            <div class="col-md-8">
                <label for="codigo_barras" class="form-label">Código de Barras</label>
                <div class="input-group">
                    <input type="text" id="codigo_barras" name="codigo_barra" class="form-control" autofocus placeholder="Escaneie ou digite o código">
                    <button type="button" id="buscarProduto" class="btn btn-primary">
                        <i class="fas fa-search"></i> Buscar
                    </button>
                </div>
                <small class="text-muted">Pressione Enter para buscar automaticamente.</small>
            </div>
        </div>
        
        <hr>
        
        <h4 class="mb-3">Informações Globais do Produto</h4>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" id="nome" name="nome" class="form-control" required>
            </div>
            <div class="col-md-3 mb-3">
                <label for="preco" class="form-label">Preço de Venda</label>
                <input type="number" id="preco" name="preco" class="form-control" step="0.01">
            </div> 
            <div class="col-md-3 mb-3">
                <label for="preco_compra" class="form-label">Preço de Compra</label>
                <input type="number" id="preco_compra" name="preco_compra" class="form-control" step="0.01">
            </div>
            <div class="col-md-6 mb-3">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" id="marca" name="marca" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="fabricante" class="form-label">Fabricante</label>
                <input type="text" id="fabricante" name="fabricante" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="peso" class="form-label">Peso</label>
                <input type="text" id="peso" name="peso" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="tamanho" class="form-label">Tamanho</label>
                <input type="text" id="tamanho" name="tamanho" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label for="categoria" class="form-label">Categoria Global</label>
                <input type="text" id="categoria" name="categoria" class="form-control">
            </div>
            <div class="col-12 mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea id="descricao" name="descricao" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12 mb-3">
                <label for="ingredientes" class="form-label">Ingredientes</label>
                <textarea id="ingredientes" name="ingredientes" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-12 mb-3">
                <label for="descricao_ingredientes" class="form-label">Descrição dos Ingredientes</label>
                <textarea id="descricao_ingredientes" name="descricao_ingredientes" class="form-control" rows="2"></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label for="embalagem" class="form-label">Embalagem</label>
                <input type="text" id="embalagem" name="embalagem" class="form-control">
            </div>
        </div>

        <hr>
        
        <h4 class="mb-3">Informações Específicas da Sua Loja</h4>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="codigo_fiscal" class="form-label">Código Fiscal</label>
                <input type="text" id="codigo_fiscal" name="codigo_fiscal" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="custo_medio" class="form-label">Custo Médio</label>
                <input type="number" id="custo_medio" name="custo_medio" class="form-control" step="0.01">
            </div>
            <div class="col-md-6 mb-3">
                <label for="margem" class="form-label">Margem (%)</label>
                <input type="number" id="margem" name="margem" class="form-control" step="0.01">
            </div>
            <div class="col-md-6 mb-3">
                <label for="comissao" class="form-label">Comissão (%)</label>
                <input type="number" id="comissao" name="comissao" class="form-control" step="0.01">
            </div>
            
            <div class="col-md-12 mb-2 mt-2">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" id="habilitar_venda_atacado" name="habilitar_venda_atacado">
                    <label class="form-check-label fw-bold" for="habilitar_venda_atacado">Habilitar Venda no Atacado</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <label for="quantidade_atacado" class="form-label">Qtd. Mínima Atacado</label>
                <input type="number" id="quantidade_atacado" name="quantidade_atacado" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="preco_atacado" class="form-label">Preço no Atacado</label>
                <input type="number" id="preco_atacado" name="preco_atacado" class="form-control" step="0.01">
            </div>

            <div class="col-md-6 mb-3">
                <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                <input type="number" id="estoque_minimo" name="estoque_minimo" class="form-control">
            </div>
            <div class="col-md-6 mb-3">
                <label for="estoque_maximo" class="form-label">Estoque Máximo</label>
                <input type="number" id="estoque_maximo" name="estoque_maximo" class="form-control">
            </div>
            
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" id="controlar_estoque" name="controlar_estoque" class="form-check-input" checked>
                    <label for="controlar_estoque" class="form-check-label">Controlar Estoque</label>
                </div>
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" id="balanca_checkout" name="balanca_checkout" class="form-check-input">
                    <label for="balanca_checkout" class="form-check-label">Balança no Checkout</label>
                </div>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="custo_ultima_compra" class="form-label">Custo Última Compra</label>
                <input type="number" id="custo_ultima_compra" name="custo_ultima_compra" class="form-control" step="0.01">
            </div>
            <div class="col-md-6 mb-3">
                <label for="margem_ultima_compra" class="form-label">Margem Última Compra (%)</label>
                <input type="number" id="margem_ultima_compra" name="margem_ultima_compra" class="form-control" step="0.01">
            </div>
        </div>

        <hr>
        <h4 class="mb-3">Lotes</h4>
        <div class="row">
            <div class="col-12 mb-3">
                <div id="lotesContainer">
                    <div class="lote mb-2 p-3 border rounded bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">Número do Lote</label>
                                <input type="text" name="lotes[0][lote]" class="form-control" placeholder="Ex: A123">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Quantidade</label>
                                <input type="number" name="lotes[0][quantidade]" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Validade</label>
                                <input type="date" name="lotes[0][validade]" class="form-control">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Data Fabricação</label>
                                <input type="date" name="lotes[0][data_fabricacao]" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="addLote" class="btn btn-secondary mt-2">
                    <i class="fas fa-plus"></i> Adicionar Lote
                </button>
            </div>
        </div>

        <button type="submit" class="btn btn-success w-100 mt-4 btn-lg">Salvar Produto</button>
    </form>
</div>

<script>
    // 1. Preview de Imagem (Upload Manual)
    function previewPersonalizedImage(event) {
        const reader = new FileReader();
        reader.onload = function() {
            const output = document.getElementById('preview_imagem_visual');
            const placeholder = document.getElementById('placeholder_img');
            output.src = reader.result;
            output.style.display = 'block';
            if(placeholder) placeholder.style.display = 'none';
        };
        if(event.target.files[0]){
            reader.readAsDataURL(event.target.files[0]);
        }
    }

    // 2. Busca com Enter
    document.getElementById('codigo_barras').addEventListener('keydown', function(event) {
        if (event.key === 'Enter') {
            event.preventDefault(); // Evita submit do form
            document.getElementById('buscarProduto').click();
        }
    });

    // 3. Lógica Principal: Busca + Download de Imagem
    document.getElementById('buscarProduto').addEventListener('click', function() {
        let codigoBarras = document.getElementById('codigo_barras').value.trim();
        
        if (codigoBarras === '') {
            alert("Digite um código de barras válido!");
            return;
        }

        // Mostra loading
        const loading = document.getElementById('loadingImage');
        
        fetch("{{ route('produtos.buscarPorCodigo') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ codigo_barras: codigoBarras })
        })
        .then(response => response.json())
        .then(async data => {
            if (data.erro) {
                alert(data.erro);
            } else {
                // Preencher campos
                document.getElementById('nome').value = data.nome ?? '';
                document.getElementById('descricao').value = data.descricao ?? '';
                document.getElementById('marca').value = data.marca ?? '';
                document.getElementById('fabricante').value = data.fabricante ?? '';
                document.getElementById('tamanho').value = data.tamanho ?? '';
                document.getElementById('peso').value = data.peso ?? '';
                document.getElementById('categoria').value = data.categoria ?? '';
                document.getElementById('ingredientes').value = data.ingredientes ?? '';
                document.getElementById('descricao_ingredientes').value = data.descricao_ingredientes ?? '';
                document.getElementById('embalagem').value = data.embalagem ?? '';
                
                // Limpa input de arquivo anterior
                const fileInput = document.getElementById('imagem_personalizada');
                fileInput.value = ''; 
                
                // Lógica da Imagem
                if (data.imagem) {
                    const imgUrl = data.imagem;
                    document.getElementById('url_image').value = imgUrl; 

                    const isUrl = /^(http|https):\/\//.test(imgUrl);

                    if (isUrl) {
                        // Link Externo: Tenta baixar
                        loading.style.display = 'block'; 
                        
                        try {
                            const responseImg = await fetch(imgUrl);
                            const blob = await responseImg.blob();
                            
                            // Cria arquivo fake para o input
                            const fileName = codigoBarras + ".jpg";
                            const file = new File([blob], fileName, { type: blob.type });

                            // Insere no input type="file"
                            const container = new DataTransfer();
                            container.items.add(file);
                            fileInput.files = container.files;

                            // Preview
                            const preview = document.getElementById('preview_imagem_visual');
                            const placeholder = document.getElementById('placeholder_img');
                            preview.src = URL.createObjectURL(blob);
                            preview.style.display = 'block';
                            if(placeholder) placeholder.style.display = 'none';

                        } catch (error) {
                            console.error("Erro CORS ou Rede:", error);
                            // Fallback visual (mostra só o link, backend baixa)
                            const preview = document.getElementById('preview_imagem_visual');
                            preview.src = imgUrl;
                            preview.style.display = 'block';
                        } finally {
                            loading.style.display = 'none';
                        }

                    } else {
                        // Imagem local (código.jpg)
                        const preview = document.getElementById('preview_imagem_visual');
                        const placeholder = document.getElementById('placeholder_img');
                        preview.src = imgUrl;
                        preview.style.display = 'block';
                        if(placeholder) placeholder.style.display = 'none';
                    }
                } else {
                    document.getElementById('preview_imagem_visual').style.display = 'none';
                    document.getElementById('placeholder_img').style.display = 'block';
                }
            }
        })
        .catch(error => {
            console.error("Erro:", error);
            document.getElementById('loadingImage').style.display = 'none';
        });
    });

    // 4. Formatação de Preço (Vírgula para Ponto)
    document.getElementById('preco').addEventListener('blur', function() {
        if (this.value.includes(',')) this.value = this.value.replace(',', '.');
    });
    document.getElementById('preco_compra').addEventListener('blur', function() {
        if (this.value.includes(',')) this.value = this.value.replace(',', '.');
    });

    // 5. Adicionar Lotes Dinamicamente
    document.getElementById('addLote').addEventListener('click', function() {
        let lotesContainer = document.getElementById('lotesContainer');
        let loteIndex = lotesContainer.getElementsByClassName('lote').length;
        let loteDiv = document.createElement('div');
        loteDiv.classList.add('lote', 'mb-2', 'p-3', 'border', 'rounded', 'bg-light');
        loteDiv.innerHTML = `
            <div class="row">
                <div class="col-md-4">
                    <label class="form-label">Número do Lote</label>
                    <input type="text" name="lotes[${loteIndex}][lote]" class="form-control" placeholder="Ex: B${loteIndex + 456}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">Quantidade</label>
                    <input type="number" name="lotes[${loteIndex}][quantidade]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Validade</label>
                    <input type="date" name="lotes[${loteIndex}][validade]" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label">Data Fabricação</label>
                    <input type="date" name="lotes[${loteIndex}][data_fabricacao]" class="form-control">
                </div>
                <div class="col-12 text-end mt-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('.lote').remove()">Remover</button>
                </div>
            </div>
        `;
        lotesContainer.appendChild(loteDiv);
    });
</script>
@endsection