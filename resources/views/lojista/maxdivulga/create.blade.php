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

                        <!-- Progress Bar -->
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
                                    <option value="daily">Repetir Diariamente (A IA fará criativos novos)</option>
                                    <option value="weekly">Repetir Semanalmente (Ex: Toda Sexta)</option>
                                </select>
                            </div>
                        </div>

                        <!-- Step 2: Produtos e Regras -->
                        <div x-show="step === 2" style="display: none;" x-transition>
                            <h5 class="font-weight-bolder">Passo 2: Produtos e Descontos</h5>
                            <p class="text-sm">Quais produtos a IA deve incluir?</p>

                            <div class="form-group mb-3">
                                <label>Regra de Seleção de Produtos</label>
                                <select name="product_selection_rule[type]" class="form-control" x-model="productRule"
                                    required>
                                    <option value="best_sellers">10 Mais Vendidos Automaticamente</option>
                                    <option value="category">Por Categoria (Ex: Açougue, Hortifruti)</option>
                                    <option value="manual">Escolher Manualmente Agora</option>
                                </select>
                            </div>

                            <div class="form-group mb-3" x-show="productRule === 'category'">
                                <label>Digite a Categoria</label>
                                <input type="text" name="product_selection_rule[value]" class="form-control"
                                    placeholder="Açougue">
                            </div>

                            <div class="form-group mb-3">
                                <label>Regra de Desconto Geral (Deixe 0 para nenhum)</label>
                                <div class="input-group">
                                    <span class="input-group-text">%</span>
                                    <input type="number" name="discount_rules[percentage]" class="form-control"
                                        placeholder="10" value="0" min="0">
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: IA Persona -->
                        <div x-show="step === 3" style="display: none;" x-transition>
                            <h5 class="font-weight-bolder">Passo 3: Copywriting e Persona IA</h5>
                            <p class="text-sm">Como o anúncio deve "soar"? A IA usará gatilhos de vendas.</p>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'urgencia' ? 'border-primary' : ''"
                                        @click="persona = 'urgencia'">
                                        <h6>🔥 Escassez/Urgência</h6>
                                        <p class="text-xs mb-0">"É SÓ HOJE!! Corre que já tá acabando!" Focado em liquidação
                                            e medo de perder a oferta.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'premium' ? 'border-primary' : ''" @click="persona = 'premium'">
                                        <h6>💎 Premium / Exclusivo</h6>
                                        <p class="text-xs mb-0">"O melhor selecionado para você." Focado em roupas,
                                            perfumaria e clientes alto padrão.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'mercado' ? 'border-primary' : ''" @click="persona = 'mercado'">
                                        <h6>🛒 Locutor de Mercado</h6>
                                        <p class="text-xs mb-0">"Alô dona de casa! Olha a oferta passando!" Textos no estilo
                                            popular e direto.</p>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="card cursor-pointer border px-3 py-2"
                                        :class="persona == 'emocional' ? 'border-primary' : ''"
                                        @click="persona = 'emocional'">
                                        <h6>😍 Gatilho Emocional</h6>
                                        <p class="text-xs mb-0">"Você merece esse conforto." Focado em mexer com os
                                            sentimentos antes do preço.</p>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="persona" :value="persona">
                        </div>

                        <!-- Step 4: Formato Final e Tema -->
                        <div x-show="step === 4" style="display: none;" x-transition>
                            <h5 class="font-weight-bolder">Passo 4: Formato e Acabamento</h5>

                            <div class="form-group mb-3">
                                <label>Formato Final da Publicação</label>
                                <select name="format" class="form-control" x-model="formatFinal" required>
                                    <option value="image">Imagem (PNG ideal p/ Stories/Feed/Status)</option>
                                    <option value="pdf">Catálogo PDF (Ideal p/ WhatsApp)</option>
                                    <option value="text">Apenas Texto IA (Copiar e colar)</option>
                                    <option value="audio">Áudio Locução (MP3 via IA de Voz)</option>
                                </select>
                            </div>

                            <div class="form-group mb-3" x-show="formatFinal === 'image' || formatFinal === 'pdf'">
                                <label>Tema Base</label>
                                <select name="theme_id" class="form-control">
                                    @foreach($themes as $theme)
                                        <option value="{{ $theme->id }}">{{ $theme->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Step 5: Confirmação -->
                        <div x-show="step === 5" style="display: none;" x-transition>
                            <div class="text-center py-4">
                                <h3 class="text-primary"><i class="fas fa-robot"></i></h3>
                                <h4 class="font-weight-bolder">Tudo Pronto!</h4>
                                <p>A Inteligência Artificial vai preparar as artes, textos e compilar seus produtos.
                                    Pressione "Gerar Campanha" para finalizar.</p>
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="row mt-4">
                            <div class="col-6">
                                <button type="button" class="btn btn-outline-secondary" x-show="step > 1"
                                    @click="step--">Voltar</button>
                            </div>
                            <div class="col-6 text-end">
                                <button type="button" class="btn bg-gradient-dark" x-show="step < 5"
                                    @click="step++">Avançar</button>
                                <button type="submit" class="btn bg-gradient-success" x-show="step === 5">✨ Gerar Campanha
                                    c/ IA</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- AlpineJS CDN (se já tiver no projeto não precisaria, mas garantimos isolado para a tela) -->
    <script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('wizardData', () => ({
                step: 1,
                productRule: 'best_sellers',
                persona: 'urgencia',
                formatFinal: 'image'
            }))
        });
    </script>
@endsection