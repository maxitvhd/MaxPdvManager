@extends('layouts.user_type.auth')

@section('content')
    <div class="row" x-data="wizardData()">
        <div class="col-12 col-lg-8 m-auto">
            <div class="card mb-4">
                <div class="card-header text-center pb-0">
                    <h4 class="mb-0">Criar Nova Campanha IA</h4>
                    <p class="text-sm">Assistente Inteligente MaxDivulga</p>
                </div>

                <div class="card-body">
                    <form action="{{ route('lojista.maxdivulga.store') }}" method="POST" id="campaignForm">
                        @csrf

                        <!-- Progress Bar -> now 5 steps maximum -->
                        <div class="progress mb-4" style="height: 10px;">
                            <div class="progress-bar bg-gradient-info" role="progressbar"
                                :style="'width: ' + ((step/5)*100) + '%;'"></div>
                        </div>

                        <!-- Step 1: Tipo e Canais -->
                        <div x-show="step === 1" x-transition>
                            <h5 class="font-weight-bolder">Passo 1: Definição Básica</h5>
                            <p class="text-sm">Qual o objetivo e canais da sua divulgação?</p>

                            <div class="form-group mb-3">
                                <label>Nome da Campanha</label>
                                <input type="text" name="name" class="form-control" placeholder="Ex: Feirão Fim de Semana"
                                    required>
                            </div>

                            <div class="form-group mb-3">
                                <label>Tipo de Catálogo</label>
                                <select name="type" class="form-control" required>
                                    <option value="varejo">Varejo (Consumidor Final)</option>
                                    <option value="atacado">Atacado (Revendedores)</option>
                                    <option value="venda_direta">Venda Direta / Oferta Relâmpago</option>
                                </select>
                            </div>

                            <label>Canais de Divulgação</label>
                            <div class="row mb-3 px-2">
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="whatsapp"
                                            id="c_wpp" checked>
                                        <label class="form-check-label" for="c_wpp">WhatsApp</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="instagram"
                                            id="c_ig">
                                        <label class="form-check-label" for="c_ig">Instagram</label>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="channels[]" value="facebook"
                                            id="c_fb">
                                        <label class="form-check-label" for="c_fb">Facebook</label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group mb-3">
                                <label>Programação (Agendamento)</label>
                                <select name="schedule_type" class="form-control" required>
                                    <option value="unique">Única Mão (Gerar Agora)</option>
                                    <option value="daily">Repetir Diariamente</option>
                                    <option value="weekly">Repetir Semanalmente (Toda Sexta, p.ex.)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Produtos e Regras -->
                        <div x-show="step === 2" style="display: none;" x-transition>
                            <h5 class="font-weight-bolder">Passo 2: Seleção de Produtos e Descontos</h5>
                            <p class="text-sm">Como deseja escolher os produtos para a IA divulgar?</p>

                            <div class="form-group mb-3">
                                <label>Regra de Seleção de Produtos</label>
                                <select name="product_selection_rule[type]" class="form-control" x-model="productRule"
                                    required>
                                    <option value="best_sellers">10 Mais Vendidos Automaticamente</option>
                                    <option value="category">Por Categoria (Ex: Açougue, Hortifruti)</option>
                                    <option value="manual">Busca Manual Ativa (Pesquisar e Adicionar Individualmente)
                                    </option>
                                </select>
                            </div>

                            <div class="form-group mb-3" x-show="productRule === 'category'">
                                <label>Nome da Categoria</label>
                                <input type="text" name="product_selection_rule[value]" class="form-control"
                                    x-model="ruleValue" placeholder="Ex: Bebidas">
                            </div>

                            <!-- Seleção Manual VIP -->
                            <div x-show="productRule === 'manual'" class="mt-4 p-3 border rounded bg-light">
                                <h6 class="text-primary"><i class="fas fa-search"></i> Explorador de Produtos</h6>
                                <p class="text-xs mb-2">Digite o nome do produto para procurar em sua base e adicione um por
                                    um clicando no botão abaixo.</p>

                                <div class="input-group mb-0">
                                    <input type="text" class="form-control" placeholder="Buscar por nome ou código..."
                                        x-model="searchQuery" @keyup.enter.prevent="fetchManualProducts()">
                                    <button class="btn btn-primary mb-0" type="button" @click="fetchManualProducts()"
                                        :disabled="loadingProducts">Buscar</button>
                                </div>
                                <div x-show="loadingProducts" class="text-xs text-primary mt-1 mb-2">Buscando na base de
                                    dados...</div>

                                <!-- Search Results -->
                                <div class="table-responsive bg-white border rounded mb-4 mt-3"
                                    style="max-height: 200px; overflow-y: auto;" x-show="searchResults.length > 0">
                                    <table class="table table-sm align-items-center mb-0">
                                        <tbody>
                                            <template x-for="product in searchResults" :key="product.id">
                                                <tr>
                                                    <td class="px-3 py-2"><span class="text-xs font-weight-bold"
                                                            x-text="product.nome"></span></td>
                                                    <td class="py-2"><span class="text-xs text-secondary"
                                                            x-text="'R$ ' + product.preco"></span></td>
                                                    <td class="text-end px-3 py-2">
                                                        <template x-if="!selectedProducts.find(p => p.id === product.id)">
                                                            <button type="button"
                                                                class="btn btn-xs btn-outline-success mb-0 py-1 px-3"
                                                                @click="addProduct(product)"><i class="fas fa-plus"></i>
                                                                Adicionar</button>
                                                        </template>
                                                        <template x-if="selectedProducts.find(p => p.id === product.id)">
                                                            <span class="badge badge-sm bg-gradient-success"><i
                                                                    class="fas fa-check"></i> Na Lista</span>
                                                        </template>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>

                                <h6 class="mt-4">📋 Sua Lista de Selecionados (<span
                                        x-text="selectedProducts.length"></span>)</h6>
                                <p class="text-xs text-muted">Esses são os produtos que você garantiu que estarão nesta
                                    campanha:</p>
                                <div class="table-responsive bg-white border rounded"
                                    style="max-height: 250px; overflow-y: auto;">
                                    <table class="table table-sm align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 px-3">
                                                    Produto</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Preço Atual</th>
                                                <th class="px-3"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-if="selectedProducts.length === 0">
                                                <tr>
                                                    <td colspan="3" class="text-xs text-center py-4 text-secondary">Nenhum
                                                        produto adicionado. Você ainda não escolheu nada!</td>
                                                </tr>
                                            </template>
                                            <template x-for="product in selectedProducts" :key="product.id">
                                                <tr>
                                                    <td class="px-3 py-2">
                                                        <input type="hidden" name="selected_products[]" :value="product.id">
                                                        <span class="text-xs font-weight-bold" x-text="product.nome"></span>
                                                    </td>
                                                    <td class="py-2"><span class="text-xs text-secondary"
                                                            x-text="'R$ ' + product.preco"></span></td>
                                                    <td class="text-end px-3 py-2">
                                                        <button type="button"
                                                            class="btn btn-xs btn-outline-danger mb-0 py-1 px-2"
                                                            @click="removeProduct(product.id)"><i class="fas fa-trash"></i>
                                                            Remover</button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr class="mt-4 mb-3">

                            <div class="form-group mb-3">
                                <label class="text-info font-weight-bold"><i class="fas fa-tag"></i> Desconto Mágico na
                                    Campanha (%)</label>
                                <p class="text-xs text-muted">A Inteligência Artificial aplicará esse desconto visualmente
                                    na arte e no texto (De/Por).</p>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">%</span>
                                    <input type="number" name="discount_rules[percentage]"
                                        class="form-control border border-left-0" placeholder="Ex: 5, 10, 50..." value="0"
                                        min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3 (antigo 4): IA Persona -->
                        <div x-show="step === 3" style="display: none;" x-transition>
                            <h5 class="font-weight-bolder">Passo 3: Copywriting e Tom da IA</h5>
                            <p class="text-sm">Como o seu anúncio deve "soar"? A Inteligência Artificial usará os melhores
                                gatilhos e palavras persuasivas.</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'urgencia' ? 'border-primary shadow-sm bg-light' : ''"
                                        @click="persona = 'urgencia'">
                                        <h6>🔥 Escassez / Urgência</h6>
                                        <p class="text-xs mb-0">"É SÓ HOJE!! Corre que já tá acabando!" Focado em liquidação
                                            e medo de perder a oferta.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'premium' ? 'border-primary shadow-sm bg-light' : ''"
                                        @click="persona = 'premium'">
                                        <h6>💎 Premium / Exclusivo</h6>
                                        <p class="text-xs mb-0">"O melhor selecionado para você." Focado em produtos de alto
                                            valor agregado e sofisticação.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'mercado' ? 'border-primary shadow-sm bg-light' : ''"
                                        @click="persona = 'mercado'">
                                        <h6>🛒 Locutor de Varejão</h6>
                                        <p class="text-xs mb-0">"Alô dona de casa! Olha a oferta passando!" Textos
                                            populares, ágeis e diretos ao preço.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'emocional' ? 'border-primary shadow-sm bg-light' : ''"
                                        @click="persona = 'emocional'">
                                        <h6>😍 Gatilho Emocional</h6>
                                        <p class="text-xs mb-0">"Você merece esse conforto." Focado em mexer com os
                                            sentimentos do cliente antes de mostrar o preço.</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="persona" :value="persona">
                        </div>

                        <!-- Step 4 (antigo 5): Formato Final e Tema -->
                        <div x-show="step === 4" style="display: none;" x-transition>
                            <h5 class="font-weight-bolder">Passo 4: Acabamento e Renderização</h5>
                            <p class="text-sm">Qual vai ser o formato da mídia gerada?</p>

                            <div class="form-group mb-3">
                                <label>Formato Final da Publicação</label>
                                <select name="format" class="form-control" x-model="formatFinal" required>
                                    <option value="image">Imagem Impressionante (PNG ideal p/ Stories/Feed)</option>
                                    <option value="pdf">Catálogo PDF em Páginas (Ideal p/ Venda Direta no WhatsApp)</option>
                                    <option value="text">Apenas Texto da IA (Aquele textão vendedor para copiar)</option>
                                </select>
                            </div>

                            <div class="form-group mb-3 mt-4" x-show="formatFinal === 'image' || formatFinal === 'pdf'">
                                <label>Tema Gráfico Base</label>
                                <p class="text-xs text-muted mb-2">A arte será construída utilizando este molde e a IA
                                    preencherá as frases de impacto.</p>
                                <select name="theme_id" class="form-control form-control-lg bg-light border-0 px-3">
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Step 5 (antigo 6): Confirmação -->
                        <div x-show="step === 5" style="display: none;" x-transition>
                            <div class="text-center py-4">
                                <h3 class="text-primary mb-3"><i class="fas fa-magic fa-2x"></i></h3>
                                <h4 class="font-weight-bolder">Tudo Pronto para a Magia!</h4>
                                <p class="px-5">Nossa Inteligência Artificial entrará em ação logo após o seu clique. Ela
                                    preparará as artes, encaixará os produtos perfeitamente, criará as cópias persuasivas e
                                    entregará o pacote pronto.</p>
                                <div class="alert alert-secondary text-white mx-4 mt-3 text-sm">
                                    <i class="fas fa-clock"></i> O processo completo de geração de Imagem/PDF via IA pode
                                    demorar de 10 a 40 segundos, dependendo da quantidade de itens. Fique na página!
                                </div>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="row mt-4 pt-3 border-top">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary px-4" x-show="step > 1"
                                    @click="step--"><i class="fas fa-chevron-left me-2"></i> Voltar</button>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn bg-gradient-dark px-4" x-show="step < 5"
                                    @click="nextStep()">Avançar <i class="fas fa-chevron-right ms-2"></i></button>
                                <button type="submit" class="btn bg-gradient-success px-4" x-show="step === 5"
                                    onclick="this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span> Gerando com IA...'"><i
                                        class="fas fa-sparkles me-1"></i> Confirmar e Gerar
                                    Campanha</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('wizardData', () => ({
                step: 1,
                productRule: 'best_sellers',
                ruleValue: '',
                persona: 'urgencia',
                formatFinal: 'image',

                searchQuery: '',
                searchResults: [],
                selectedProducts: [],
                loadingProducts: false,

                nextStep() {
                    if (this.step < 5) this.step++;
                },

                fetchManualProducts() {
                    const termo = this.searchQuery.trim();
                    if (termo.length < 2) return;
                    this.loadingProducts = true;

                    const url = `{{ route('lojista.maxdivulga.api_products') }}?rule=search&search=${encodeURIComponent(termo)}`;

                    fetch(url)
                        .then(res => res.json())
                        .then(data => {
                            this.searchResults = data;
                            this.loadingProducts = false;
                        }).catch(err => {
                            console.error('Erro ao buscar produtos:', err);
                            this.loadingProducts = false;
                        });
                },

                addProduct(product) {
                    if (!this.selectedProducts.find(p => p.id === product.id)) {
                        this.selectedProducts.push({ ...product }); // clona pra evitar refs cruzadas
                    }
                },

                removeProduct(productId) {
                    this.selectedProducts = this.selectedProducts.filter(p => p.id !== productId);
                }
            }))
        });
    </script>
@endsection