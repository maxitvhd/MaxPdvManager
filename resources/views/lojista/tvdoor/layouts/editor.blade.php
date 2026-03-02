@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-lg-3">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Elementos</h6>
                </div>
                <div class="card-body p-3">
                    <div class="d-grid gap-2">
                        <button class="btn btn-outline-primary btn-sm add-element" data-type="text">
                            <i class="fas fa-font me-2"></i> Texto
                        </button>
                        <button class="btn btn-outline-primary btn-sm add-element" data-type="clock">
                            <i class="fas fa-clock me-2"></i> Relógio
                        </button>
                        <button class="btn btn-outline-primary btn-sm" id="btn-show-products">
                            <i class="fas fa-tag me-2"></i> Produto do Catálogo
                        </button>
                    </div>

                    <hr class="horizontal dark mt-4 mb-3">
                    
                    <h6>Salvar Layout</h6>
                    <form action="{{ route('lojista.tvdoor.layouts.store') }}" method="POST" id="form-layout">
                        @csrf
                        <div class="form-group mb-3">
                            <label class="form-label text-xs">Nome do Layout</label>
                            <input type="text" name="name" class="form-control form-control-sm" placeholder="Ex: Promoção de Verão" required>
                        </div>
                        <input type="hidden" name="content" id="layout-content">
                        <button type="submit" class="btn bg-gradient-success btn-sm w-100 mb-0">Salvar Layout</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-9">
            <div class="card bg-gray-200" style="min-height: 500px; position: relative; overflow: hidden;" id="canvas">
                <!-- Elementos serão adicionados aqui via JS -->
                <div class="text-center mt-5 text-secondary opacity-5" id="canvas-placeholder">
                    <i class="ni ni-palette fa-5x"></i>
                    <p>Arraste elementos para começar a criar seu layout</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Produtos -->
<div class="modal fade" id="productModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Selecionar Produto</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    @forelse($produtos as $prod)
                    <div class="col-md-4 mb-3">
                        <div class="card product-card cursor-pointer border" data-name="{{ $prod->nome }}" data-price="{{ $prod->preco_venda }}" data-image="{{ $prod->imagem ? asset('storage/'.$prod->imagem) : 'https://via.placeholder.com/150' }}">
                            <div class="card-body p-2 text-center">
                                <img src="{{ $prod->imagem ? asset('storage/'.$prod->imagem) : 'https://via.placeholder.com/150' }}" class="img-fluid rounded border mb-2" style="height: 100px; object-fit: cover;">
                                <p class="text-sm font-weight-bold mb-0">{{ $prod->nome }}</p>
                                <p class="text-xs text-secondary">R$ {{ number_format($prod->preco_venda, 2, ',', '.') }}</p>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center">Catálogo vazio.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- CSS Adicional -->
<style>
    .draggable-element {
        position: absolute;
        cursor: move;
        background: white;
        padding: 5px 10px;
        border: 1px dashed #ccc;
        border-radius: 4px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .draggable-element:hover {
        border-color: #5e72e4;
    }
    .resizer {
        width: 10px; height: 10px;
        background: #5e72e4;
        position: absolute;
        right: -5px; bottom: -5px;
        cursor: nwse-resize;
    }
    .delete-btn {
        position: absolute;
        top: -10px; right: -10px;
        background: red; color: white;
        border-radius: 50%; width: 20px; height: 20px;
        display: flex; align-items: center; justify-content: center;
        font-size: 10px; cursor: pointer; display: none;
    }
    .draggable-element:hover .delete-btn {
        display: flex;
    }
</style>

<!-- JS para o Editor -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<script>
$(function() {
    const $canvas = $("#canvas");
    const $placeholder = $("#canvas-placeholder");
    const elements = [];

    // Mostrar Modal de Produtos
    $("#btn-show-products").click(() => {
        $("#productModal").modal('show');
    });

    // Adicionar Elemento de Texto ou Relógio
    $(".add-element").click(function() {
        const type = $(this).data('type');
        addElement({ type: type, content: type === 'clock' ? '00:00:00' : 'Novo Texto', x: 50, y: 50 });
    });

    // Adicionar Produto
    $(".product-card").click(function() {
        const name = $(this).data('name');
        const price = $(this).data('price');
        const image = $(this).data('image');
        
        const html = `
            <div class="text-center" style="width: 200px;">
                <img src="${image}" class="img-fluid rounded mb-1" style="max-height: 120px;">
                <h6 class="mb-0 text-sm">${name}</h6>
                <p class="text-primary font-weight-bold">R$ ${price}</p>
            </div>
        `;
        
        addElement({ type: 'product', content: html, x: 100, y: 100 });
        $("#productModal").modal('hide');
    });

    function addElement(config) {
        $placeholder.hide();
        const $el = $(`<div class="draggable-element" style="left: ${config.x}px; top: ${config.y}px;">
                        <div class="content">${config.content}</div>
                        <div class="resizer"></div>
                        <div class="delete-btn"><i class="fas fa-times"></i></div>
                    </div>`);
        
        $el.draggable({
            containment: "#canvas",
            stop: updateContent
        }).resizable({
            stop: updateContent
        });

        $el.find(".delete-btn").click(function() {
            $el.remove();
            updateContent();
            if ($canvas.find(".draggable-element").length === 0) $placeholder.show();
        });

        $canvas.append($el);
        updateContent();
    }

    function updateContent() {
        const layout = [];
        $canvas.find(".draggable-element").each(function() {
            const $this = $(this);
            layout.push({
                content: $this.find(".content").html(),
                x: parseInt($this.css("left")),
                y: parseInt($this.css("top")),
                w: $this.width(),
                h: $this.height()
            });
        });
        $("#layout-content").val(JSON.stringify(layout));
    }

    // Salvar
    $("#form-layout").submit(function() {
        updateContent();
        return true;
    });
});
</script>
@endsection
