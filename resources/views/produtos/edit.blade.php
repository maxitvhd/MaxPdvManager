@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h2>Editar Produto</h2>
    
    <form method="POST" action="{{ route('produtos.update', $produto->id) }}" enctype="multipart/form-data">
    @csrf
        @method('PUT')

        <div class="row">

        <div class="col-md-4 mb-3">
    <label for="imagem_personalizada" class="form-label">Imagem</label>

    <!-- Exibe a imagem atual -->
    <div>
        @if ($produto->imagem)
        <img src="{{ asset('/storage/usuario/' . Auth::user()->codigo . '/produtos/' . $produto->imagem) }}" alt="Imagem do Produto" class="img-fluid mb-3" style="max-height: 200px;">
        @else
            <p>Sem imagem atual</p>
        @endif
    </div>

    <!-- Campo para alterar a imagem -->
    <input type="file" class="form-control" id="imagem_personalizada" name="imagem_personalizada">
</div>

            <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="preco" class="form-label">Preço</label>
                <input type="number" class="form-control" id="preco" name="preco" value="{{ old('preco', $produto->preco) }}" step="0.01" required>
            </div>

            <div class="col-md-6 mb-3">
                <label for="preco_compra" class="form-label">Preço de Compra</label>
                <input type="number" class="form-control" id="preco_compra" name="preco_compra" value="{{ old('preco_compra', $produto->preco_compra) }}" step="0.01" >
            </div>

            <div class="col-md-6 mb-3">
                <label for="marca" class="form-label">Marca</label>
                <input type="text" name="marca" value="{{ $produto->produtoFull->marca ?? '' }}" class="form-control" readonly>
                </div>

            <div class="col-md-6 mb-3">
                <label for="fabricante" class="form-label">Fabricante</label>
                <input type="text" class="form-control" id="fabricante" name="fabricante" value="{{ old('fabricante', $produto->produtoFull->fabricante) }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label for="peso" class="form-label">Peso</label>
                <input type="text" class="form-control" id="peso" name="peso" value="{{ old('peso', $produto->produtoFull->peso) }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label for="tamanho" class="form-label">Tamanho</label>
                <input type="text" class="form-control" id="tamanho" name="tamanho" value="{{ old('tamanho', $produto->produtoFull->tamanho) }}" readonly>
            </div>

            <div class="col-md-4 mb-3">
                <label for="categoria" class="form-label">Categoria</label>
                <input type="text" class="form-control" id="categoria" name="categoria" value="{{ old('categoria', $produto->categoria) }}" >
            </div>

            <div class="col-12 mb-3">
                <label for="descricao" class="form-label">Descrição</label>
                <textarea class="form-control" id="descricao" name="descricao" rows="2" >{{ old('descricao', $produto->descricao) }}</textarea>
            </div>

        </div>

        <hr>
        <!-- Informações Específicas do Cliente -->
        <h4 class="mb-3">Informações Específicas da Sua Loja</h4>
        <div class="row">
          

            <div class="col-md-6 mb-3">
                <label for="codigo_fiscal" class="form-label">Código Fiscal</label>
                <input type="text" class="form-control" id="codigo_fiscal" name="codigo_fiscal" value="{{ old('codigo_fiscal', $produto->codigo_fiscal) }}" >
            </div>

            <div class="col-md-6 mb-3">
                <label for="custo_medio" class="form-label">Custo Médio</label>
                <input type="number" class="form-control" id="custo_medio" name="custo_medio" value="{{ old('custo_medio', $produto->custo_medio) }}" step="0.01" >
            </div>

            <div class="col-md-6 mb-3">
                <label for="margem" class="form-label">Margem (%)</label>
                <input type="number" class="form-control" id="margem" name="margem" value="{{ old('margem', $produto->margem) }}" step="0.01" >
            </div>

            <div class="col-md-6 mb-3">
                <label for="comissao" class="form-label">Comissão (%)</label>
                <input type="number" class="form-control" id="comissao" name="comissao" value="{{ old('comissao', $produto->comissao) }}" step="0.01" >
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="habilitar_venda_atacado" name="habilitar_venda_atacado" {{ $produto->habilitar_venda_atacado ? 'checked' : '' }}>
                    <label class="form-check-label" for="habilitar_venda_atacado">Habilitar Venda no Atacado</label>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="quantidade_atacado" class="form-label">Quantidade Mínima Atacado</label>
                <input type="number" class="form-control" id="quantidade_atacado" name="quantidade_atacado" value="{{ old('quantidade_atacado', $produto->quantidade_atacado) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="preco_atacado" class="form-label">Preço no Atacado</label>
                <input type="number" class="form-control" id="preco_atacado" name="preco_atacado" value="{{ old('preco_atacado', $produto->preco_atacado) }}" step="0.01">
            </div>

            <div class="col-md-6 mb-3">
                <label for="estoque_minimo" class="form-label">Estoque Mínimo</label>
                <input type="number" class="form-control" id="estoque_minimo" name="estoque_minimo" value="{{ old('estoque_minimo', $produto->estoque_minimo) }}">
            </div>

            <div class="col-md-6 mb-3">
                <label for="estoque_maximo" class="form-label">Estoque Máximo</label>
                <input type="number" class="form-control" id="estoque_maximo" name="estoque_maximo" value="{{ old('estoque_maximo', $produto->estoque_maximo) }}">
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="controlar_estoque" name="controlar_estoque" {{ $produto->controlar_estoque ? 'checked' : '' }}>
                    <label class="form-check-label" for="controlar_estoque">Controlar Estoque</label>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="balanca_checkout" name="balanca_checkout" {{ $produto->balanca_checkout ? 'checked' : '' }}>
                    <label class="form-check-label" for="balanca_checkout">Balança no Checkout</label>
                </div>
            </div>

            <div class="col-md-6 mb-3">
                <label for="custo_ultima_compra" class="form-label">Custo Última Compra</label>
                <input type="number" class="form-control" id="custo_ultima_compra" name="custo_ultima_compra" value="{{ old('custo_ultima_compra', $produto->custo_ultima_compra) }}" step="0.01">
            </div>
        </div>


        
        <hr>
        <h4>Lotes</h4>

        <div id="lotes-container">
            @foreach ($produto->lotes as $index => $lote)
                <div class="row">
                    <input type="hidden" name="lotes[{{ $index }}][id]" value="{{ $lote->id }}">
                    
                    <div class="col-md-6 mb-3">
                        <label>Lote</label>
                        <input type="text" class="form-control" name="lotes[{{ $index }}][lote]" value="{{ $lote->lote }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Quantidade</label>
                        <input type="number" class="form-control" name="lotes[{{ $index }}][quantidade]" value="{{ $lote->quantidade }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Validade</label>
                        <input type="date" class="form-control" name="lotes[{{ $index }}][validade]" value="{{ $lote->validade }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label>Data de Fabricação</label>
                        <input type="date" class="form-control" name="lotes[{{ $index }}][data_fabricacao]" value="{{ $lote->data_fabricacao }}">
                    </div>

                    <button type="button" class="btn btn-danger remove-lote">Remover</button>
                </div>
            @endforeach
        </div>

        <button type="button" class="btn btn-success" id="add-lote">Adicionar Novo Lote</button>


        <button type="submit" class="btn btn-primary">Atualizar Produto</button>
    </form>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let loteIndex = {{ count($produto->lotes) }};
    
    // Função para adicionar um novo lote
    document.getElementById("add-lote").addEventListener("click", function () {
        let container = document.getElementById("lotes-container");
        let newLote = document.createElement("div");
        newLote.classList.add("row", "lote-group", "mb-3");

        newLote.innerHTML = `
            <input type="hidden" name="lotes[${loteIndex}][id]" value="">
            <div class="col-md-6 mb-3">
                <label>Lote</label>
                <input type="text" class="form-control" name="lotes[${loteIndex}][lote]">
            </div>
            <div class="col-md-6 mb-3">
                <label>Quantidade</label>
                <input type="number" class="form-control" name="lotes[${loteIndex}][quantidade]">
            </div>
            <div class="col-md-6 mb-3">
                <label>Validade</label>
                <input type="date" class="form-control" name="lotes[${loteIndex}][validade]">
            </div>
            <div class="col-md-6 mb-3">
                <label>Data de Fabricação</label>
                <input type="date" class="form-control" name="lotes[${loteIndex}][data_fabricacao]">
            </div>
            <div class="col-12">
                <button type="button" class="btn btn-danger remove-lote">Remover</button>
            </div>
        `;

        container.appendChild(newLote);
        loteIndex++;
    });

    // Delegação de evento para remover lotes
    document.getElementById("lotes-container").addEventListener("click", function (event) {
        if (event.target.classList.contains("remove-lote")) {
            const loteGroup = event.target.closest(".row"); // Seleciona o elemento pai mais próximo com a classe "row"
            if (loteGroup) {
                loteGroup.remove(); // Remove o elemento do DOM
            }
        }
    });
});
</script>
@endsection
