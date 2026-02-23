@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-3" x-data="themeStudio()" x-init="init()">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-4 px-2">
            <div>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-palette text-primary me-2"></i> Theme Studio
                </h4>
                <p class="text-sm text-muted mb-0">Pré-visualize e edite os temas das campanha em tempo real</p>
            </div>
            <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-sm btn-outline-secondary mb-0">
                <i class="fas fa-arrow-left me-1"></i> Campanhas
            </a>
        </div>

        <div class="row g-3" style="height: calc(100vh - 160px); min-height: 650px;">

            {{-- ── PAINEL ESQUERDO (controles) ── --}}
            <div class="col-lg-3 col-md-4 d-flex flex-column gap-3">

                {{-- Escolha de Tema --}}
                <div class="card shadow-sm border-0 flex-shrink-0">
                    <div class="card-header bg-gradient-primary pb-2 pt-3 px-3">
                        <h6 class="mb-0 text-white"><i class="fas fa-swatchbook me-1"></i> Tema</h6>
                    </div>
                    <div class="card-body px-3 py-2">
                        <select class="form-select form-select-sm" x-model="selectedTheme" @change="triggerPreview()">
                            <option value="">— Selecione um tema —</option>
                            @foreach($themes as $theme)
                                <option value="{{ $theme->id }}" data-id="{{ $theme->id }}">{{ $theme->name }}</option>
                            @endforeach
                        </select>

                        {{-- Link para o editor (somente admin) --}}
                        @hasanyrole('admin|super-admin')
                        <div x-show="selectedTheme" class="mt-2 d-flex flex-column gap-1">
                            <a :href="selectedTheme ? '/lojista/maxdivulga/themes/' + selectedTheme + '/editor' : '#'"
                                class="btn btn-xs btn-outline-warning mb-0 w-100" target="_blank"
                                @click.prevent="selectedTheme ? window.open('/lojista/maxdivulga/themes/' + selectedTheme + '/editor', '_blank') : null">
                                <i class="fas fa-code me-1"></i> Editar código
                            </a>
                            <a :href="selectedTheme ? '/lojista/maxdivulga/themes/' + selectedTheme + '/builder' : '#'"
                                class="btn btn-xs btn-outline-purple mb-0 w-100" target="_blank"
                                style="background:rgba(124,58,237,.1);border-color:#7c3aed;color:#7c3aed;"
                                @click.prevent="selectedTheme ? window.open('/lojista/maxdivulga/themes/' + selectedTheme + '/builder', '_blank') : null">
                                <i class="fas fa-magic me-1"></i> Builder Visual
                            </a>
                        </div>
                        @endhasanyrole

                    </div>
                </div>

                {{-- Configurações de Preview --}}
                <div class="card shadow-sm border-0 flex-shrink-0">
                    <div class="card-header bg-gradient-info pb-2 pt-3 px-3">
                        <h6 class="mb-0 text-white"><i class="fas fa-sliders-h me-1"></i> Configurações</h6>
                    </div>
                    <div class="card-body px-3 py-2">
                        <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Nº de produtos</label>
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <input type="range" class="form-range flex-1" x-model="qty" min="1" max="20"
                                @input="triggerPreview()">
                            <span class="badge bg-primary" x-text="qty" style="min-width:30px;text-align:center"></span>
                        </div>

                        <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Desconto global
                            (%)</label>
                        <div class="input-group input-group-sm mb-3">
                            <input type="number" class="form-control" x-model="discount" min="0" max="99"
                                @input="triggerPreview()" placeholder="0">
                            <span class="input-group-text">%</span>
                        </div>

                        <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Zoom do preview</label>
                        <div class="d-flex align-items-center gap-2">
                            <input type="range" class="form-range flex-1" x-model="zoom" min="15" max="100"
                                @input="updateZoom()">
                            <span class="badge bg-secondary" x-text="zoom + '%'"
                                style="min-width:40px;text-align:center"></span>
                        </div>
                    </div>
                </div>

                {{-- Seleção de Produtos --}}
                <div class="card shadow-sm border-0 flex-grow-1 overflow-hidden d-flex flex-column">
                    <div
                        class="card-header bg-gradient-success pb-2 pt-3 px-3 d-flex align-items-center justify-content-between flex-shrink-0">
                        <h6 class="mb-0 text-white"><i class="fas fa-boxes me-1"></i> Produtos</h6>
                        <div class="d-flex gap-1">
                            <button class="btn btn-xs btn-light mb-0" @click="selectAll()" title="Selecionar todos">
                                <i class="fas fa-check-double"></i>
                            </button>
                            <button class="btn btn-xs btn-light mb-0" @click="clearSelection()" title="Limpar seleção">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0 flex-grow-1 overflow-y-auto" style="max-height: 360px;">
                        <div class="p-2 border-bottom bg-light">
                            <input type="text" class="form-control form-control-sm" x-model="productSearch"
                                placeholder="🔍 Filtrar produtos..." @input="filterProducts()">
                        </div>
                        <div class="list-group list-group-flush">
                            <template x-for="product in filteredProducts" :key="product.id">
                                <label
                                    class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-1 px-3 cursor-pointer"
                                    :class="isSelected(product.id) ? 'bg-primary-50 border-start border-primary border-3' : ''">
                                    <input type="checkbox" class="form-check-input mt-0 flex-shrink-0"
                                        :checked="isSelected(product.id)" @change="toggleProduct(product.id)">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-xs font-weight-bold mb-0 text-truncate" x-text="product.nome"></p>
                                        <small class="text-muted"
                                            x-text="'R$ ' + parseFloat(product.preco).toFixed(2).replace('.',',')"></small>
                                    </div>
                                </label>
                            </template>
                            <template x-if="filteredProducts.length === 0">
                                <div class="text-center text-muted py-4 text-sm">Nenhum produto encontrado</div>
                            </template>
                        </div>
                    </div>
                    <div class="card-footer bg-light py-2 px-3 flex-shrink-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <small class="text-muted">
                                <span x-text="selectedProducts.length"></span> selecionados
                            </small>
                            <button class="btn btn-sm btn-primary mb-0 px-3" @click="triggerPreview()"
                                :disabled="!selectedTheme">
                                <i class="fas fa-eye me-1"></i> Pré-visualizar
                            </button>
                        </div>
                    </div>
                </div>

            </div>

            {{-- ── PAINEL DIREITO (preview iframe) ── --}}
            <div class="col-lg-9 col-md-8 d-flex flex-column">
                <div class="card shadow-sm border-0 flex-grow-1 overflow-hidden d-flex flex-column">
                    <div class="card-header pb-2 pt-3 px-3 d-flex align-items-center justify-content-between flex-shrink-0">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-gradient-dark px-2">
                                <i class="fas fa-desktop me-1"></i> Preview — 1080×1920px
                            </span>
                            <span x-show="loading" class="spinner-border spinner-border-sm text-primary"
                                role="status"></span>
                            <span x-show="!loading && previewLoaded" class="badge bg-success text-xs">
                                <i class="fas fa-check me-1"></i> Carregado
                            </span>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <button class="btn btn-xs btn-outline-secondary mb-0" @click="openFullscreen()"
                                title="Abrir em nova aba (tamanho real)">
                                <i class="fas fa-external-link-alt me-1"></i> Tela cheia
                            </button>
                        </div>
                    </div>

                    {{-- Área do iframe com rolagem --}}
                    <div class="flex-grow-1 overflow-auto bg-gray-100 d-flex align-items-start justify-content-center p-3"
                        style="background: repeating-linear-gradient(45deg,#f0f0f0,#f0f0f0 10px,#e8e8e8 10px,#e8e8e8 20px);">

                        {{-- Placeholder quando nenhum tema está selecionado --}}
                        <template x-if="!selectedTheme || !previewUrl">
                            <div
                                class="d-flex flex-column align-items-center justify-content-center text-center text-muted py-5 mt-5">
                                <div style="font-size:5rem; opacity:.3;">🎨</div>
                                <h5 class="mt-3">Selecione um tema para começar</h5>
                                <p class="text-sm">Escolha o tema e os produtos no painel esquerdo, depois clique em
                                    "Pré-visualizar"</p>
                            </div>
                        </template>

                        {{-- iframe de preview --}}
                        <template x-if="previewUrl">
                            <div
                                :style="'transform: scale(' + (zoom/100) + '); transform-origin: top center; width: 1080px; flex-shrink: 0;'">
                                <iframe id="previewFrame" :src="previewUrl"
                                    style="width:1080px; height:1920px; border:none; display:block; border-radius: 8px; box-shadow: 0 20px 60px rgba(0,0,0,.4);"
                                    @load="onIframeLoad()"></iframe>
                            </div>
                        </template>

                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('themeStudio', () => ({
                // State
                selectedTheme: '',
                qty: 10,
                discount: 0,
                zoom: 40,
                productSearch: '',
                selectedProducts: [],
                filteredProducts: [],
                previewUrl: null,
                previewLoaded: false,
                loading: false,
                debounceTimer: null,

                // Dados dos produtos (passados pelo Blade)
                allProducts: @json($produtos->map(fn($p) => ['id' => $p->id, 'nome' => $p->nome, 'preco' => $p->preco])),
                themesList: @json($themes->map(fn($t) => ['id' => $t->id, 'name' => $t->name])),

                // URLs
                previewBase: '{{ route("lojista.maxdivulga.theme_preview") }}',

                init() {
                    this.filteredProducts = this.allProducts;
                    this.updateZoom();
                },

                filterProducts() {
                    const q = this.productSearch.toLowerCase().trim();
                    this.filteredProducts = q
                        ? this.allProducts.filter(p => p.nome.toLowerCase().includes(q))
                        : this.allProducts;
                },

                isSelected(id) {
                    return this.selectedProducts.includes(id);
                },

                toggleProduct(id) {
                    if (this.isSelected(id)) {
                        this.selectedProducts = this.selectedProducts.filter(p => p !== id);
                    } else {
                        this.selectedProducts.push(id);
                    }
                    this.triggerPreview();
                },

                selectAll() {
                    this.selectedProducts = this.filteredProducts.map(p => p.id);
                    this.qty = Math.min(this.selectedProducts.length, 20);
                    this.triggerPreview();
                },

                clearSelection() {
                    this.selectedProducts = [];
                },

                triggerPreview() {
                    if (!this.selectedTheme) return;
                    clearTimeout(this.debounceTimer);
                    this.debounceTimer = setTimeout(() => this.buildPreview(), 400);
                },

                buildPreview() {
                    if (!this.selectedTheme) return;
                    this.loading = true;
                    this.previewLoaded = false;

                    const params = new URLSearchParams();
                    params.set('theme_id', this.selectedTheme);
                    params.set('qty', this.qty);
                    params.set('discount', this.discount);

                    const idsToSend = this.selectedProducts.length > 0
                        ? this.selectedProducts.slice(0, this.qty)
                        : [];
                    idsToSend.forEach(id => params.append('products[]', id));

                    this.previewUrl = this.previewBase + '?' + params.toString();
                },

                onIframeLoad() {
                    this.loading = false;
                    this.previewLoaded = true;
                },

                updateZoom() {
                    // só força re-render do Alpine
                },

                openFullscreen() {
                    if (this.previewUrl) window.open(this.previewUrl, '_blank');
                }
            }));
        });
    </script>

    <style>
        .bg-primary-50 {
            background-color: #e8f0fe;
        }

        .cursor-pointer {
            cursor: pointer;
        }

        .border-3 {
            border-width: 3px !important;
        }

        .overflow-y-auto {
            overflow-y: auto;
        }
    </style>
@endpush