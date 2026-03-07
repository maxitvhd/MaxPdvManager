@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="text-dark font-weight-bolder mb-0">Editar Produto</h2>
            <p class="text-muted text-sm">Atualize as informações, estoque e lotes do produto.</p>
        </div>
    </div>
    
    <form method="POST" action="{{ route('produtos.update', $produto->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100">
                    <div class="card-header pb-0 border-0 bg-transparent">
                        <h6 class="mb-0 text-primary font-weight-bolder"><i class="fas fa-box me-2"></i> Informações Globais</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-12 mb-4 text-center">
                                <div class="position-relative d-inline-block shadow-sm rounded-3 bg-light p-2 mb-3" style="max-width: 250px;">
                                    @if ($produto->imagem)
                                        <img src="{{ asset('storage/lojas/' . ($produto->loja->codigo ?? Auth::user()->codigo) . '/produtos/' . $produto->imagem) }}" 
                                             alt="Imagem do Produto" 
                                             class="img-fluid rounded-3" 
                                             style="max-height: 200px; object-fit: contain;"
                                             onerror="this.onerror=null; this.src='{{ asset('storage/produtos_full/' . $produto->imagem) }}'; if(this.src=='{{ asset('storage/produtos_full/' . $produto->imagem) }}') this.src='https://via.placeholder.com/250x200.png?text=Sem+Imagem';">
                                    @else
                                        <div class="d-flex align-items-center justify-content-center" style="height: 200px; width: 230px;">
                                            <span class="text-muted"><i class="fas fa-image fa-2x mb-2 d-block"></i> Sem Imagem</span>
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <label for="imagem_personalizada" class="form-label font-weight-bold">Alterar Imagem da Loja</label>
                                    <input type="file" class="form-control form-control-sm w-50 mx-auto" id="imagem_personalizada" name="imagem_personalizada">
                                </div>
                            </div>

                            <div class="col-md-12 mb-3">
                                <label for="nome" class="form-label font-weight-bold">Nome do Produto *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $produto->nome) }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="marca" class="form-label">Marca (Global)</label>
                                <input type="text" name="marca" value="{{ $produto->produtoFull->marca ?? '' }}" class="form-control bg-gray-100" readonly>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fabricante" class="form-label">Fabricante (Global)</label>
                                <input type="text" class="form-control bg-gray-100" id="fabricante" name="fabricante" value="{{ old('fabricante', $produto->produtoFull->fabricante ?? '') }}" readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="peso" class="form-label">Peso (Global)</label>
                                <input type="text" class="form-control bg-gray-100" id="peso" name="peso" value="{{ old('peso', $produto->produtoFull->peso ?? '') }}" readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="tamanho" class="form-label">Tamanho (Global)</label>
                                <input type="text" class="form-control bg-gray-100" id="tamanho" name="tamanho" value="{{ old('tamanho', $produto->produtoFull->tamanho ?? '') }}" readonly>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="categoria" class="form-label font-weight-bold">Categoria da Loja</label>
                                <input type="text" class="form-control" id="categoria" name="categoria" value="{{ old('categoria', $produto->categoria) }}">
                            </div>

                            <div class="col-12 mb-3">
                                <label for="descricao" class="form-label font-weight-bold">Descrição na Loja</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3">{{ old('descricao', $produto->descricao) }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card shadow-sm border-0 rounded-4 h-100 bg-gradient-light">
                    <div class="card-header pb-0 border-0 bg-transparent">
                        <h6 class="mb-0 text-success font-weight-bolder"><i class="fas fa-tags me-2"></i> Comercial e Financeiro</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 mb-3">
                                <label for="preco" class="form-label font-weight-bold text-success">Preço de Venda (R$) *</label>
                                <input type="number" class="form-control form-control-lg border-success" id="preco" name="preco" value="{{ old('preco', $produto->preco) }}" step="0.01" required>
                            </div>

                            <div class="col-6 mb-3">
                                <label for="preco_compra" class="form-label">Preço Compra</label>
                                <input type="number" class="form-control" id="preco_compra" name="preco_compra" value="{{ old('preco_compra', $produto->preco_compra) }}" step="0.01">
                            </div>

                            <div class="col-6 mb-3">
                                <label for="custo_medio" class="form-label">Custo Médio</label>
                                <input type="number" class="form-control" id="custo_medio" name="custo_medio" value="{{ old('custo_medio', $produto->custo_medio) }}" step="0.01">
                            </div>

                            <div class="col-6 mb-3">
                                <label for="margem" class="form-label">Margem (%)</label>
                                <input type="number" class="form-control" id="margem" name="margem" value="{{ old('margem', $produto->margem) }}" step="0.01">
                            </div>

                            <div class="col-6 mb-3">
                                <label for="comissao" class="form-label">Comissão (%)</label>
                                <input type="number" class="form-control" id="comissao" name="comissao" value="{{ old('comissao', $produto->comissao) }}" step="0.01">
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label for="custo_ultima_compra" class="form-label">Custo Última Compra</label>
                                <input type="number" class="form-control" id="custo_ultima_compra" name="custo_ultima_compra" value="{{ old('custo_ultima_compra', $produto->custo_ultima_compra) }}" step="0.01">
                            </div>

                            <hr class="horizontal dark my-3">

                            <div class="col-12 mb-3">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="habilitar_venda_atacado" name="habilitar_venda_atacado" {{ $produto->habilitar_venda_atacado ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark font-weight-bold" for="habilitar_venda_atacado">Habilitar Atacado</label>
                                </div>
                            </div>

                            <div class="col-6 mb-3">
                                <label for="quantidade_atacado" class="form-label">Qtd. Min. Atacado</label>
                                <input type="number" class="form-control" id="quantidade_atacado" name="quantidade_atacado" value="{{ old('quantidade_atacado', $produto->quantidade_atacado) }}">
                            </div>

                            <div class="col-6 mb-3">
                                <label for="preco_atacado" class="form-label">Preço Atacado</label>
                                <input type="number" class="form-control" id="preco_atacado" name="preco_atacado" value="{{ old('preco_atacado', $produto->preco_atacado) }}" step="0.01">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-12 mb-4">
                 <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <label for="codigo_fiscal" class="form-label font-weight-bold mb-1">Código Fiscal (NCM/CEST)</label>
                                <input type="text" class="form-control" id="codigo_fiscal" name="codigo_fiscal" value="{{ old('codigo_fiscal', $produto->codigo_fiscal) }}">
                            </div>
                            
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="estoque_minimo" class="form-label font-weight-bold mb-1">Estoque Mín.</label>
                                <input type="number" class="form-control" id="estoque_minimo" name="estoque_minimo" value="{{ old('estoque_minimo', $produto->estoque_minimo) }}">
                            </div>
                            
                            <div class="col-md-2 mb-3 mb-md-0">
                                <label for="estoque_maximo" class="form-label font-weight-bold mb-1">Estoque Máx.</label>
                                <input type="number" class="form-control" id="estoque_maximo" name="estoque_maximo" value="{{ old('estoque_maximo', $produto->estoque_maximo) }}">
                            </div>

                            <div class="col-md-5 d-flex justify-content-around mt-3 mt-md-0">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="controlar_estoque" name="controlar_estoque" {{ $produto->controlar_estoque ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark font-weight-bold" for="controlar_estoque">Controlar Estoque</label>
                                </div>
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-0 me-2" type="checkbox" id="balanca_checkout" name="balanca_checkout" {{ $produto->balanca_checkout ? 'checked' : '' }}>
                                    <label class="form-check-label text-dark font-weight-bold" for="balanca_checkout">Balança PDV</label>
                                </div>
                            </div>
                        </div>
                    </div>
                 </div>
            </div>

            <div class="col-12 mb-4">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header pb-0 border-0 bg-transparent d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 text-info font-weight-bolder"><i class="fas fa-layer-group me-2"></i> Gestão de Lotes</h6>
                        <button type="button" class="btn btn-sm btn-outline-info mb-0" id="add-lote">
                            <i class="fas fa-plus"></i> Novo Lote
                        </button>
                    </div>
                    <div class="card-body">
                        <div id="lotes-container">
                            @foreach ($produto->lotes as $index => $lote)
                                <div class="row align-items-end mb-3 pb-3 border-bottom lote-group">
                                    <input type="hidden" name="lotes[{{ $index }}][id]" value="{{ $lote->id }}">
                                    
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <label class="text-xs text-muted mb-1">Número do Lote</label>
                                        <input type="text" class="form-control" name="lotes[{{ $index }}][lote]" value="{{ $lote->lote }}">
                                    </div>

                                    <div class="col-md-2 mb-2 mb-md-0">
                                        <label class="text-xs text-muted mb-1">Quantidade</label>
                                        <input type="number" class="form-control" name="lotes[{{ $index }}][quantidade]" value="{{ $lote->quantidade }}">
                                    </div>

                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <label class="text-xs text-muted mb-1">Fabricação</label>
                                        <input type="date" class="form-control" name="lotes[{{ $index }}][data_fabricacao]" value="{{ $lote->data_fabricacao }}">
                                    </div>
                                    
                                    <div class="col-md-3 mb-2 mb-md-0">
                                        <label class="text-xs text-muted mb-1">Vencimento</label>
                                        <input type="date" class="form-control" name="lotes[{{ $index }}][validade]" value="{{ $lote->validade }}">
                                    </div>

                                    <div class="col-md-1 text-end">
                                        <button type="button" class="btn btn-link text-danger px-3 mb-0 remove-lote" data-bs-toggle="tooltip" title="Remover Lote">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

        </div> <div class="row mt-2">
            <div class="col-12 text-end">
                <a href="{{ route('produtos.index') }}" class="btn btn-secondary me-2">Cancelar</a>
                <button type="submit" class="btn bg-gradient-primary">
                    <i class="fas fa-save me-2"></i> Salvar Alterações
                </button>
            </div>
        </div>

    </form>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let loteIndex = {{ count($produto->lotes) }};
    
    // Função para adicionar um novo lote
    document.getElementById("add-lote").addEventListener("click", function () {
        let container = document.getElementById("lotes-container");
        let newLote = document.createElement("div");
        newLote.classList.add("row", "align-items-end", "mb-3", "pb-3", "border-bottom", "lote-group");

        newLote.innerHTML = `
            <input type="hidden" name="lotes[${loteIndex}][id]" value="">
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="text-xs text-muted mb-1">Número do Lote</label>
                <input type="text" class="form-control" name="lotes[${loteIndex}][lote]" placeholder="Ex: L-001">
            </div>
            <div class="col-md-2 mb-2 mb-md-0">
                <label class="text-xs text-muted mb-1">Quantidade</label>
                <input type="number" class="form-control" name="lotes[${loteIndex}][quantidade]" value="0">
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="text-xs text-muted mb-1">Fabricação</label>
                <input type="date" class="form-control" name="lotes[${loteIndex}][data_fabricacao]">
            </div>
            <div class="col-md-3 mb-2 mb-md-0">
                <label class="text-xs text-muted mb-1">Vencimento</label>
                <input type="date" class="form-control" name="lotes[${loteIndex}][validade]">
            </div>
            <div class="col-md-1 text-end">
                <button type="button" class="btn btn-link text-danger px-3 mb-0 remove-lote" title="Remover Lote">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        container.appendChild(newLote);
        loteIndex++;
    });

    // Delegação de evento para remover lotes (Correção na seleção do container "row")
    document.getElementById("lotes-container").addEventListener("click", function (event) {
        let btn = event.target.closest(".remove-lote");
        if (btn) {
            let loteGroup = btn.closest(".lote-group"); 
            if (loteGroup) {
                // Se o campo hidden tiver ID (já existia no banco), não apaga do DOM, 
                // apenas marca para exclusão (ou você pode processar via backend). 
                // Para simplificar, vamos remover visualmente:
                loteGroup.remove(); 
            }
        }
    });
});
</script>
@endsection