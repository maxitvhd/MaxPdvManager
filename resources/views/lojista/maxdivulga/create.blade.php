@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4" x-data="wizardData()" x-init="init()">

        <div class="row justify-content-center">
            <div class="col-12 col-xl-9">

                {{-- Header --}}
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h4 class="mb-0 font-weight-bolder">✨ Nova Campanha com IA</h4>
                        <p class="text-muted text-sm mb-0">Siga os passos para gerar sua arte e copy profissionais</p>
                    </div>
                </div>

                <div class="card border-0 shadow-lg" style="border-radius:20px; overflow:hidden;">

                    {{-- Progress Steps --}}
                    <div class="card-header py-3 px-4"
                        style="background:linear-gradient(135deg,#1a1a2e,#0f3460); border:none;">
                        <div class="d-flex justify-content-between align-items-center">
                            @foreach(['Básico', 'Quantidade', 'Produtos', 'Persona', 'Formato', 'Confirmar'] as $i => $label)
                                <div class="d-flex flex-column align-items-center" style="flex:1;">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center font-weight-bolder text-sm mb-1"
                                        :class="step > {{ $i + 1 }} ? 'bg-success text-white' : (step === {{ $i + 1 }} ? 'bg-white text-dark' : 'bg-white opacity-30 text-dark')"
                                        style="width:32px;height:32px;font-size:0.75rem;">
                                        <template x-if="step > {{ $i + 1 }}"><span>✓</span></template>
                                        <template x-if="step <= {{ $i + 1 }}"><span>{{ $i + 1 }}</span></template>
                                    </div>
                                    <span class="text-white-50 d-none d-md-block" style="font-size:0.6rem;">{{ $label }}</span>
                                </div>
                                @if($i < 5)
                                    <div class="flex-fill mx-1"
                                        style="height:2px;background:rgba(255,255,255,0.2);margin-bottom:18px;">
                                        <div style="height:2px;background:#10b981;transition:width 0.4s;"
                                            :style="'width:' + (step > {{ $i + 1 }} ? '100' : '0') + '%'"></div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <div class="card-body p-4 p-md-5">
                        <form action="{{ route('lojista.maxdivulga.store') }}" method="POST" id="campaignForm">
                            @csrf

                            {{-- PASSO 1: Básico --}}
                            <div x-show="step === 1" x-transition>
                                <h5 class="font-weight-bolder mb-1">Passo 1: Definição Básica</h5>
                                <p class="text-muted text-sm mb-4">Qual o objetivo e os canais da sua divulgação?</p>

                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">Nome da Campanha *</label>
                                    <input type="text" name="name" class="form-control form-control-lg"
                                        placeholder="Ex: Feirão de Fim de Semana, Café da Manhã Especial..." required>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label class="form-label font-weight-bold">Tipo de Público</label>
                                        <select name="type" class="form-control" required>
                                            <option value="varejo">🛒 Varejo (Consumidor Final)</option>
                                            <option value="atacado">📦 Atacado (Revendedores)</option>
                                            <option value="venda_direta">⚡ Oferta Relâmpago</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label font-weight-bold">Programação</label>
                                        <select name="schedule_type" class="form-control" x-model="scheduleType" required>
                                            <option value="unique">🚀 Disparar Agora (Criar Imediatamente)</option>
                                            <option value="scheduled">⏱️ Programar / Repetir no Piloto Automático</option>
                                        </select>
                                    </div>
                                </div>

                                <div x-show="scheduleType === 'scheduled'" class="mb-4 p-3 border rounded"
                                    style="background:rgba(16,185,129,0.05); border-color:rgba(16,185,129,0.2)!important;"
                                    x-transition>
                                    <h6 class="font-weight-bold text-success mb-3"><i class="fas fa-robot me-1"></i> Piloto
                                        Automático de Promoções</h6>

                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-sm font-weight-bold">Dias de Disparo</label>
                                            <div class="d-flex flex-wrap gap-2">
                                                <template
                                                    x-for="(dayName, dayNum) in {1:'Seg', 2:'Ter', 3:'Qua', 4:'Qui', 5:'Sex', 6:'Sáb', 0:'Dom'}"
                                                    :key="dayNum">
                                                    <label class="btn btn-sm mb-0 px-2 py-1"
                                                        :class="scheduledDays.includes(dayNum.toString()) ? 'btn-success' : 'btn-outline-secondary'">
                                                        <input type="checkbox" name="scheduled_days[]" :value="dayNum"
                                                            class="d-none" x-model="scheduledDays"
                                                            :disabled="scheduleType !== 'scheduled'">
                                                        <span x-text="dayName"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-sm font-weight-bold">Horários</label>
                                            <div class="d-flex gap-2">
                                                <input type="time" class="form-control form-control-sm" x-model="newTime">
                                                <button type="button" class="btn btn-sm btn-dark mb-0"
                                                    @click="addTime()">Add</button>
                                            </div>
                                            <div class="mt-2 d-flex flex-wrap gap-2">
                                                <template x-for="time in scheduledTimes" :key="time">
                                                    <span class="badge bg-secondary d-flex align-items-center gap-1">
                                                        <span x-text="time"></span>
                                                        <input type="hidden" name="scheduled_times[]" :value="time"
                                                            :disabled="scheduleType !== 'scheduled'">
                                                        <i class="fas fa-times cursor-pointer ms-1" style="cursor:pointer;"
                                                            @click="removeTime(time)"></i>
                                                    </span>
                                                </template>
                                                <template x-if="scheduledTimes.length === 0">
                                                    <small class="text-muted">Nenhum horário adicionado.</small>
                                                </template>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="is_scheduled" value="1"
                                        :disabled="scheduleType !== 'scheduled'">
                                </div>

                                <div class="mb-3">
                                    <label class="form-label font-weight-bold">Canais de Divulgação</label>
                                    <div class="row px-2">
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="channels[]"
                                                    value="whatsapp" id="c_wpp" checked>
                                                <label class="form-check-label" for="c_wpp">💬 WhatsApp</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="channels[]"
                                                    value="instagram" id="c_ig">
                                                <label class="form-check-label" for="c_ig">📸 Instagram</label>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="channels[]"
                                                    value="facebook" id="c_fb">
                                                <label class="form-check-label" for="c_fb">👍 Facebook</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PASSO 2: Quantidade --}}
                            <div x-show="step === 2" style="display:none;" x-transition>
                                <h5 class="font-weight-bolder mb-1">Passo 2: Quantidade de Produtos</h5>
                                <p class="text-muted text-sm mb-4">Quantos produtos vão aparecer na sua arte? A IA adaptará
                                    o layout automaticamente.</p>

                                <input type="hidden" name="product_quantity" :value="productQty">

                                <div class="row g-3 mb-4">
                                    {{-- 1 produto --}}
                                    <div class="col-6 col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 text-center h-100"
                                            style="border-radius:12px; cursor:pointer; transition:all 0.2s;"
                                            :class="productQty == 1 ? 'border-primary shadow' : 'border-light'"
                                            @click="productQty = 1">
                                            <div style="font-size:2.5rem;">1️⃣</div>
                                            <h6 class="font-weight-bold mt-2 mb-1">1 Produto</h6>
                                            <p class="text-muted text-xs mb-0">Post de Destaque<br>Estilo Instagram</p>
                                            <small class="badge bg-gradient-info mt-2">Destaque Único</small>
                                        </div>
                                    </div>
                                    {{-- 5 produtos --}}
                                    <div class="col-6 col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 text-center h-100"
                                            style="border-radius:12px; cursor:pointer; transition:all 0.2s;"
                                            :class="productQty == 5 ? 'border-primary shadow' : 'border-light'"
                                            @click="productQty = 5">
                                            <div style="font-size:2.5rem;">5️⃣</div>
                                            <h6 class="font-weight-bold mt-2 mb-1">5 Produtos</h6>
                                            <p class="text-muted text-xs mb-0">Encarte Compacto<br>Redes Sociais</p>
                                            <small class="badge bg-gradient-success mt-2">Recomendado</small>
                                        </div>
                                    </div>
                                    {{-- 10 produtos --}}
                                    <div class="col-6 col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 text-center h-100"
                                            style="border-radius:12px; cursor:pointer; transition:all 0.2s;"
                                            :class="productQty == 10 ? 'border-primary shadow' : 'border-light'"
                                            @click="productQty = 10">
                                            <div style="font-size:2.5rem;">🔟</div>
                                            <h6 class="font-weight-bold mt-2 mb-1">10 Produtos</h6>
                                            <p class="text-muted text-xs mb-0">Encarte Completo<br>WhatsApp</p>
                                            <small class="badge bg-gradient-secondary mt-2">Padrão</small>
                                        </div>
                                    </div>
                                    {{-- 15 produtos --}}
                                    <div class="col-6 col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 text-center h-100"
                                            style="border-radius:12px; cursor:pointer; transition:all 0.2s;"
                                            :class="productQty == 15 ? 'border-primary shadow' : 'border-light'"
                                            @click="productQty = 15">
                                            <div style="font-size:2.5rem;">📋</div>
                                            <h6 class="font-weight-bold mt-2 mb-1">15 Produtos</h6>
                                            <p class="text-muted text-xs mb-0">Catálogo Extenso<br>PDF / Print</p>
                                            <small class="badge bg-gradient-warning mt-2">Catálogo</small>
                                        </div>
                                    </div>
                                    {{-- 20 produtos --}}
                                    <div class="col-6 col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 text-center h-100"
                                            style="border-radius:12px; cursor:pointer; transition:all 0.2s;"
                                            :class="productQty == 20 ? 'border-primary shadow' : 'border-light'"
                                            @click="productQty = 20">
                                            <div style="font-size:2.5rem;">📦</div>
                                            <h6 class="font-weight-bold mt-2 mb-1">20 Produtos</h6>
                                            <p class="text-muted text-xs mb-0">Lista Completa<br>Atacado / B2B</p>
                                            <small class="badge bg-gradient-dark mt-2">Atacado</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="alert alert-light border text-sm" style="border-radius:12px;">
                                    <i class="fas fa-info-circle text-info me-2"></i>
                                    <template x-if="productQty == 1">
                                        <span>🎯 <strong>Post de Destaque:</strong> O produto principal ocupa toda a arte
                                            com foto grande e preço em destaque — perfeito para promoção única no Instagram
                                            e Stories.</span>
                                    </template>
                                    <template x-if="productQty > 1">
                                        <span>📊 <strong>Catálogo com <span x-text="productQty"></span> produtos:</strong> A
                                            IA criará um layout de grade com todos os produtos, preços e imagens organizados
                                            profissionalmente.</span>
                                    </template>
                                </div>
                            </div>

                            {{-- PASSO 3: Produtos --}}
                            <div x-show="step === 3" style="display:none;" x-transition>
                                <h5 class="font-weight-bolder mb-1">Passo 3: Seleção de Produtos</h5>
                                <p class="text-muted text-sm mb-4">Escolha quais produtos aparecerão na campanha. Máximo:
                                    <strong x-text="productQty"></strong> produtos.
                                </p>

                                <div class="form-group mb-3">
                                    <label class="form-label font-weight-bold">Método de Seleção</label>
                                    <select name="product_selection_rule[type]" class="form-control" x-model="productRule"
                                        @change="autoProducts = []; selectedProducts = [];" required>
                                        <option value="best_sellers">⭐ Mais Vendidos Automaticamente</option>
                                        <option value="category">🏷️ Por Categoria (Ex: Açougue, Bebidas)</option>
                                        <option value="manual">🔍 Busca Manual (Escolher individualmente)</option>
                                    </select>
                                </div>

                                {{-- Categoria --}}
                                <div class="form-group mb-3" x-show="productRule === 'category'">
                                    <label class="form-label font-weight-bold">Nome da Categoria</label>
                                    <div class="input-group">
                                        <input type="text" name="product_selection_rule[value]" class="form-control"
                                            x-model="ruleValue" placeholder="Ex: Bebidas, Açougue, Frios...">
                                        <button class="btn btn-primary mb-0" type="button" @click="fetchAutoProducts()"
                                            :disabled="loadingProducts">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                </div>

                                {{-- Best sellers botão re-busca --}}
                                <div class="mb-3 d-flex align-items-center gap-2" x-show="productRule === 'best_sellers'">
                                    <button type="button" class="btn btn-sm btn-outline-info" @click="fetchAutoProducts()"
                                        :disabled="loadingProducts">
                                        <i class="fas fa-sync" :class="loadingProducts ? 'fa-spin' : ''"></i> Atualizar Mais
                                        Vendidos
                                    </button>
                                    <small class="text-muted">Buscando top <span x-text="productQty"></span> mais vendidos
                                        da loja</small>
                                </div>

                                {{-- Lista automática --}}
                                <div x-show="productRule !== 'manual'">
                                    <div x-show="loadingProducts" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status"></div>
                                        <p class="text-muted text-sm mt-2">Buscando produtos...</p>
                                    </div>
                                    <div x-show="!loadingProducts && autoProducts.length > 0">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <p class="text-xs text-muted mb-0">
                                                Exibindo <strong x-text="autoProducts.length"></strong> produtos. Escolha-os
                                                na lista com desconto ou clique para adicionar.
                                            </p>
                                        </div>
                                        <div class="border rounded"
                                            style="max-height:220px;overflow-y:auto;border-radius:10px!important;">
                                            <table class="table table-sm align-items-center mb-0">
                                                <template x-for="product in autoProducts" :key="product.id">
                                                    <tr :class="isSelected(product.id) ? 'bg-light' : ''">
                                                        <td class="py-2 px-3">
                                                            <span class="text-sm font-weight-bold"
                                                                x-text="product.nome"></span>
                                                        </td>
                                                        <td class="py-2 text-end px-3">
                                                            <span class="text-sm text-muted"
                                                                x-text="'R$ ' + parseFloat(product.preco).toFixed(2).replace('.',',')"></span>
                                                        </td>
                                                        <td class="py-2 px-3" style="width:100px;">
                                                            <button type="button" class="btn btn-xs mb-0"
                                                                :class="isSelected(product.id) ? 'btn-secondary' : 'btn-outline-success'"
                                                                @click="toggleAutoProduct(product)">
                                                                <span
                                                                    x-text="isSelected(product.id) ? 'Remover' : 'Adicionar'"></span>
                                                            </button>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </table>
                                        </div>
                                    </div>
                                    <div x-show="!loadingProducts && autoProducts.length === 0"
                                        class="alert alert-warning text-white text-sm mt-2">
                                        <i class="fas fa-exclamation-triangle"></i> Nenhum produto encontrado. Use o botão
                                        para buscar ou troque para busca manual.
                                    </div>
                                </div>

                                {{-- Busca Manual --}}
                                <div x-show="productRule === 'manual'" class="mt-2">
                                    <div class="input-group mb-3">
                                        <input type="text" class="form-control" placeholder="Digite o nome do produto..."
                                            x-model="searchQuery" @keyup.enter.prevent="fetchManualProducts()">
                                        <button class="btn btn-primary mb-0" type="button" @click="fetchManualProducts()"
                                            :disabled="loadingProducts">
                                            <i class="fas fa-search"></i> Buscar
                                        </button>
                                    </div>
                                    <div x-show="loadingProducts" class="text-center py-2">
                                        <div class="spinner-border spinner-border-sm text-primary" role="status"></div>
                                    </div>
                                    <div class="border rounded mb-3" style="max-height:220px;overflow-y:auto;"
                                        x-show="searchResults.length > 0">
                                        <table class="table table-sm mb-0">
                                            <tbody>
                                                <template x-for="product in searchResults" :key="product.id">
                                                    <tr>
                                                        <td class="px-3 py-2"><span class="text-sm"
                                                                x-text="product.nome"></span></td>
                                                        <td class="py-2 text-end px-3">
                                                            <span class="text-sm text-success"
                                                                x-text="'R$ ' + parseFloat(product.preco).toFixed(2).replace('.',',')"></span>
                                                        </td>
                                                        <td class="py-2 px-3" style="width:100px;">
                                                            <template x-if="!isSelected(product.id)">
                                                                <button type="button"
                                                                    class="btn btn-xs btn-outline-success mb-0"
                                                                    @click="addProduct(product)"
                                                                    :disabled="selectedProducts.length >= productQty">
                                                                    <i class="fas fa-plus"></i> Add
                                                                </button>
                                                            </template>
                                                            <template x-if="isSelected(product.id)">
                                                                <span class="badge bg-success">✓ Adicionado</span>
                                                            </template>
                                                        </td>
                                                    </tr>
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- COMPILADO FINAL DOS PRODUTOS SELECIONADOS + DESCONTOS --}}
                                <h6 class="mt-4 border-top pt-3 text-primary">🛒 Produtos Selecionados e Descontos (<span
                                        x-text="selectedProducts.length"></span> / <span x-text="productQty"></span>)</h6>

                                <div class="d-flex gap-2 align-items-center mb-3 p-2 bg-light rounded"
                                    x-show="selectedProducts.length > 0">
                                    <span class="text-sm font-weight-bold">Desconto em Massa:</span>
                                    <div class="input-group input-group-sm mb-0" style="width:120px;">
                                        <input type="number" class="form-control" placeholder="Ex: 10"
                                            x-model="globalDiscount">
                                        <span class="input-group-text">%</span>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-dark mb-0"
                                        @click="applyGlobalDiscount()">Aplicar a Todos</button>
                                </div>

                                <div class="border rounded" style="max-height:350px;overflow-y:auto;">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="bg-gray-100">
                                            <tr>
                                                <th class="text-xs font-weight-bolder text-uppercase text-secondary">Produto
                                                </th>
                                                <th
                                                    class="text-xs font-weight-bolder text-uppercase text-secondary text-center">
                                                    Desconto %</th>
                                                <th
                                                    class="text-xs font-weight-bolder text-uppercase text-secondary text-end">
                                                    Preços</th>
                                                <th class="text-xs font-weight-bolder text-uppercase text-secondary"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-if="selectedProducts.length === 0">
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3 text-sm">Adicione os
                                                        produtos acima.</td>
                                                </tr>
                                            </template>
                                            <template x-for="(product, index) in selectedProducts" :key="product.id">
                                                <tr>
                                                    <td class="px-3 py-2">
                                                        <input type="hidden" name="selected_products[]" :value="product.id">
                                                        <p class="text-sm font-weight-bold mb-0 text-wrap"
                                                            style="max-width:200px;" x-text="product.nome"></p>
                                                    </td>
                                                    <td class="py-2 text-center" style="width:120px;">
                                                        <div class="input-group input-group-sm mb-0 mx-auto">
                                                            <input type="number" :name="'discount_products['+product.id+']'"
                                                                class="form-control text-center" x-model="product.discount"
                                                                @input="calculateDiscounted(index)" placeholder="0" min="0"
                                                                max="99">
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-3 text-end">
                                                        <div class="d-flex flex-column">
                                                            <small class="text-decoration-line-through text-muted"
                                                                x-show="product.discount > 0"
                                                                x-text="'R$ ' + parseFloat(product.preco).toFixed(2).replace('.',',')"></small>
                                                            <span class="text-sm font-weight-bold text-success"
                                                                x-text="'R$ ' + getDiscountedPrice(product)"></span>
                                                        </div>
                                                    </td>
                                                    <td class="py-2 px-3 text-end" style="width:50px;">
                                                        <button type="button"
                                                            class="btn btn-xs btn-outline-danger mb-0 px-2"
                                                            @click="removeProduct(product.id)"><i
                                                                class="fas fa-trash"></i></button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            {{-- PASSO 4: Persona --}}
                            <div x-show="step === 4" style="display:none;" x-transition>
                                <h5 class="font-weight-bolder mb-1">Passo 4: Tom da IA</h5>
                                <p class="text-muted text-sm mb-4">Qual o <strong>estilo de linguagem</strong> que a IA deve
                                    usar para criar sua copy?</p>

                                <input type="hidden" name="persona" :value="persona">
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 h-100" @click="persona = 'urgencia'"
                                            :class="persona == 'urgencia' ? 'border-primary shadow' : 'border-light'">
                                            <h6 class="font-weight-bold text-sm">🔥 Urgência</h6>
                                            <p class="text-xs text-muted mb-0">"É SÓ HOJE!"</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 h-100" @click="persona = 'premium'"
                                            :class="persona == 'premium' ? 'border-primary shadow' : 'border-light'">
                                            <h6 class="font-weight-bold text-sm">💎 Premium</h6>
                                            <p class="text-xs text-muted mb-0">"Exclusivo para você."</p>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card cursor-pointer border-2 p-3 h-100" @click="persona = 'mercado'"
                                            :class="persona == 'mercado' ? 'border-primary shadow' : 'border-light'">
                                            <h6 class="font-weight-bold text-sm">🛒 Varejão</h6>
                                            <p class="text-xs text-muted mb-0">"Olha o preço passando!"</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card cursor-pointer border-2 p-3 h-100" @click="persona = 'emocional'"
                                            :class="persona == 'emocional' ? 'border-primary shadow' : 'border-light'">
                                            <h6 class="font-weight-bold text-sm">😍 Emocional</h6>
                                            <p class="text-xs text-muted mb-0">"Família e bons momentos."</p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="card cursor-pointer border-2 p-3 h-100"
                                            @click="persona = 'surpreendame'"
                                            :class="persona == 'surpreendame' ? 'border-primary shadow' : 'border-light'">
                                            <h6 class="font-weight-bold text-sm">🎲 Surpreenda-me</h6>
                                            <p class="text-xs text-muted mb-0">A IA escolhe baseada nos produtos.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- PASSO 5: Formato --}}
                            <div x-show="step === 5" style="display:none;" x-transition>
                                <h5 class="font-weight-bolder mb-1">Passo 5: Formato Final</h5>
                                <p class="text-muted text-sm mb-4">Qual tipo de mídia a IA deve gerar para você?</p>

                                <div class="form-group mb-4">
                                    <label class="form-label font-weight-bold">Formato da Publicação</label>
                                    <select name="format" class="form-control form-control-lg" x-model="formatFinal"
                                        required>
                                        <option value="image">🖼️ Imagem PNG (Ideal para Stories e Feed)</option>
                                        <option value="pdf">📄 Catálogo PDF (Ideal para WhatsApp Business)</option>
                                        <option value="audio">🔊 Áudio Locução (Para Rádio, Loja ou Carro de Som)</option>
                                        <option value="text">📝 Apenas o Texto da IA (Para copiar e colar)</option>
                                    </select>
                                </div>

                                <div class="row mb-4" x-show="formatFinal === 'audio'" x-transition>
                                    <div class="col-md-6 form-group">
                                        <label class="form-label font-weight-bold">Voz do Locutor</label>
                                        <select name="voice" class="form-control form-control-lg" x-model="voice"
                                            :required="formatFinal === 'audio'">
                                            <option value="pt-BR-FabioNeural">Fábio (Padrão/Masculino)</option>
                                            <option value="pt-BR-AntonioNeural">Antônio (Masculino Médio)</option>
                                            <option value="pt-BR-DonatoNeural">Donato (Rápido/Agudo)</option>
                                            <option value="pt-BR-HumbertoNeural">Humberto (Grave/Robusto)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 form-group">
                                        <label class="form-label font-weight-bold">Velocidade da Locução</label>
                                        <select name="audio_speed" class="form-control form-control-lg" x-model="audioSpeed"
                                            :required="formatFinal === 'audio'">
                                            <option value="0.9">Lenta (0.9x)</option>
                                            <option value="1.0">Normal (1.0x)</option>
                                            <option value="1.25">Rápida (1.25x)</option>
                                            <option value="1.5">Muito Rápida (1.5x - Feirão Promocional)</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group mb-3" x-show="formatFinal === 'image' || formatFinal === 'pdf'">
                                    <label class="form-label font-weight-bold">Tema Gráfico</label>
                                    <p class="text-xs text-muted mb-2">Layout visual da sua arte. A IA preencherá com os
                                        produtos e copy.</p>
                                    <select name="theme_id" class="form-control form-control-lg" required>
                                        @foreach($themes as $theme)
                                            <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            {{-- PASSO 6: Confirmação --}}
                            <div x-show="step === 6" style="display:none;" x-transition>
                                <div class="text-center py-4">
                                    <div style="font-size:4rem; margin-bottom:16px;">🧠</div>
                                    <h4 class="font-weight-bolder">Tudo Pronto para a IA Agir!</h4>
                                    <p class="text-muted px-md-5 mb-4">A Inteligência Artificial vai criar sua arte com
                                        produtos reais, detectar o tema da campanha, gerar headlines profissionais com
                                        gatilhos de venda e preparar o texto de acompanhamento para WhatsApp e Instagram.
                                    </p>

                                    <div class="row justify-content-center mb-4">
                                        <div class="col-md-8">
                                            <div class="card border-0"
                                                style="background:linear-gradient(135deg,#fff3e0,#fff8f0); border-radius:16px;">
                                                <div class="card-body py-3">
                                                    <div class="d-flex align-items-start mb-2">
                                                        <span class="me-2">🖼️</span>
                                                        <span class="text-sm">Arte profissional com <strong
                                                                x-text="productQty"></strong> produto(s)</span>
                                                    </div>
                                                    <div class="d-flex align-items-start mb-2">
                                                        <span class="me-2">✍️</span>
                                                        <span class="text-sm">Copy com gatilhos <strong
                                                                x-text="persona === 'urgencia' ? 'de urgência e escassez' : (persona === 'premium' ? 'premium' : (persona === 'mercado' ? 'de varejão' : 'emocionais'))"></strong></span>
                                                    </div>
                                                    <div class="d-flex align-items-start">
                                                        <span class="me-2">📲</span>
                                                        <span class="text-sm">Texto de acompanhamento para WhatsApp e redes
                                                            sociais</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info text-white text-sm" style="border-radius:12px;">
                                        <i class="fas fa-clock me-2"></i>
                                        O processo de geração pode levar entre <strong>15 a 45 segundos</strong>. Não feche
                                        a página!
                                    </div>
                                </div>
                            </div>

                            {{-- Navegação --}}
                            <div class="d-flex justify-content-between mt-4 pt-3 border-top">
                                <button type="button" class="btn btn-outline-secondary px-4" x-show="step > 1"
                                    @click="step--">
                                    <i class="fas fa-chevron-left me-2"></i> Voltar
                                </button>
                                <div x-show="step <= 1"></div>

                                <button type="button" class="btn px-4 font-weight-bold" x-show="step < 6"
                                    @click="nextStep()"
                                    style="background:linear-gradient(135deg,#0f3460,#16213e);color:#fff;border:none;border-radius:10px;">
                                    Avançar <i class="fas fa-chevron-right ms-2"></i>
                                </button>

                                <button type="submit" class="btn px-4 font-weight-bold" x-show="step === 6"
                                    style="background:linear-gradient(135deg,#10b981,#059669);color:#fff;border:none;border-radius:10px;"
                                    onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Gerando com IA...'">
                                    🚀 Confirmar e Gerar Campanha
                                </button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('wizardData', () => ({
                step: 1,
                scheduleType: 'unique',
                scheduledDays: [],
                scheduledTimes: [],
                newTime: '09:00',
                productQty: 10,
                productRule: 'best_sellers',
                ruleValue: '',
                persona: 'surpreendame',
                formatFinal: 'image',
                voice: 'pt-BR-FabioNeural',
                audioSpeed: '1.25',
                searchQuery: '',
                searchResults: [],
                selectedProducts: [],
                autoProducts: [],
                loadingProducts: false,
                globalDiscount: '',

                init() { },

                addTime() {
                    if (this.newTime && !this.scheduledTimes.includes(this.newTime)) {
                        this.scheduledTimes.push(this.newTime);
                    }
                },

                removeTime(time) {
                    this.scheduledTimes = this.scheduledTimes.filter(t => t !== time);
                },

                nextStep() {
                    if (this.step === 2 && this.productRule === 'best_sellers' && this.autoProducts.length === 0) {
                        this.fetchAutoProducts();
                    }
                    if (this.step < 6) this.step++;
                },

                fetchAutoProducts() {
                    this.loadingProducts = true;
                    this.autoProducts = [];
                    const url = `{{ route('lojista.maxdivulga.api_products') }}?rule=${this.productRule}&search=${encodeURIComponent(this.ruleValue)}&limit=${this.productQty}`;
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            this.autoProducts = Array.isArray(data) ? data : [];
                            // Popula selectedProducts com discount=0
                            this.selectedProducts = this.autoProducts.map(p => ({ ...p, discount: 0 }));
                            this.loadingProducts = false;
                        }).catch(err => {
                            console.error('Erro ao buscar produtos:', err);
                            this.loadingProducts = false;
                        });
                },

                fetchManualProducts() {
                    const termo = this.searchQuery.trim();
                    if (termo.length < 2) return;
                    this.loadingProducts = true;
                    const url = `{{ route('lojista.maxdivulga.api_products') }}?rule=search&search=${encodeURIComponent(termo)}`;
                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = Array.isArray(data) ? data : [];
                            this.loadingProducts = false;
                        }).catch(() => { this.loadingProducts = false; });
                },

                addProduct(product) {
                    if (this.selectedProducts.length >= this.productQty) {
                        alert(`Máximo de ${this.productQty} produtos atingido!`);
                        return;
                    }
                    if (!this.isSelected(product.id)) {
                        this.selectedProducts.push({ ...product, discount: 0 });
                    }
                },

                removeProduct(productId) {
                    this.selectedProducts = this.selectedProducts.filter(p => p.id !== productId);
                },

                toggleAutoProduct(product) {
                    const idx = this.selectedProducts.findIndex(p => p.id === product.id);
                    if (idx >= 0) {
                        this.selectedProducts.splice(idx, 1);
                    } else {
                        if (this.selectedProducts.length >= this.productQty) return;
                        this.selectedProducts.push({ ...product, discount: 0 });
                    }
                },

                isSelected(productId) {
                    return !!this.selectedProducts.find(p => p.id === productId);
                },

                applyGlobalDiscount() {
                    let disc = parseInt(this.globalDiscount);
                    if (isNaN(disc) || disc < 0) disc = 0;
                    this.selectedProducts.forEach(p => p.discount = disc);
                },

                calculateDiscounted(index) {
                    let disc = parseInt(this.selectedProducts[index].discount) || 0;
                    if (disc < 0) disc = 0;
                    if (disc > 100) disc = 100;
                    this.selectedProducts[index].discount = disc;
                },

                getDiscountedPrice(product) {
                    let p = parseFloat(product.preco);
                    let d = parseInt(product.discount) || 0;
                    if (d > 0 && d <= 100) {
                        p = p - (p * (d / 100));
                    }
                    return p.toFixed(2).replace('.', ',');
                }
            }));
        });
    </script>
@endsection