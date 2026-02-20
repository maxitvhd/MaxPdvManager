@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12 col-md-10 col-lg-8 mx-auto">
            <div class="card mb-4">

                <div class="card-header pb-0 border-bottom">
                    <h5 class="mb-0 text-primary">Assinar / Modificar Plano</h5>
                    <p class="text-sm">Personalize os recursos da loja <b>{{ $licenca->loja->nome ?? 'Sua Loja' }}</b> de
                        acordo com sua necessidade.</p>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('assinaturas.checkout', $licenca->id) }}" method="POST" id="formAssinatura">
                        @csrf

                        <!-- Planos -->
                        <h6 class="mt-2 mb-3">1. Escolha o Plano Principal</h6>
                        <div class="row">
                            @foreach($planos as $plano)
                                <div class="col-md-6 mb-3">
                                    <div class="card border border-2 shadow-none cursor-pointer h-100 plano-card {{ $loop->first ? 'border-primary' : '' }}"
                                        onclick="selectPlano({{ $plano->id }}, {{ $plano->valor }})">
                                        <div class="card-body p-3 text-center">
                                            <div class="form-check d-flex justify-content-center mb-2">
                                                <input class="form-check-input" type="radio" name="plano_id"
                                                    id="plano_{{ $plano->id }}" value="{{ $plano->id }}" {{ $loop->first ? 'checked' : '' }} onchange="calculateTotal()">
                                            </div>
                                            <h6 class="mb-1">{{ $plano->nome }}</h6>
                                            <h4 class="font-weight-bolder text-dark mb-1">R$
                                                {{ number_format($plano->valor, 2, ',', '.') }}</h4>
                                            <span class="text-xs">Válido por {{ $plano->meses_validade }} mês(es)</span><br>
                                            <span
                                                class="badge badge-sm bg-gradient-secondary mt-2">{{ $plano->limite_dispositivos }}
                                                Dispositivo(s) Inclusos</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Opcionais / Extras -->
                        @if($adicionais->count() > 0)
                            <h6 class="mt-4 mb-3">2. Expandir com Adicionais (Opcional)</h6>
                            <div class="table-responsive">
                                <table class="table align-items-center mb-0 border">
                                    <tbody>
                                        @foreach($adicionais as $ext)
                                            <tr>
                                                <td class="w-10">
                                                    <div class="form-check mb-0">
                                                        <input class="form-check-input extra-checkbox" type="checkbox"
                                                            name="adicionais[{{ $ext->id }}][ativo]" value="1"
                                                            id="extra_{{ $ext->id }}" data-id="{{ $ext->id }}"
                                                            data-valor="{{ $ext->valor }}" onchange="toggleExtra({{ $ext->id }})">
                                                        <!-- hidden ID field for mapping -->
                                                        <input type="hidden" name="adicionais[{{ $ext->id }}][id]"
                                                            value="{{ $ext->id }}">
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm" for="extra_{{ $ext->id }}">{{ $ext->nome }}</h6>
                                                        <small class="text-xs text-muted">{{ $ext->descricao }}</small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="text-xs font-weight-bold">R$
                                                        {{ number_format($ext->valor, 2, ',', '.') }}
                                                        @if($ext->tipo == 'dispositivo') / un @endif</span>
                                                </td>
                                                <td class="w-20 text-center">
                                                    @if($ext->tipo == 'dispositivo')
                                                        <input type="number" class="form-control form-control-sm extra-qtd text-center"
                                                            name="adicionais[{{ $ext->id }}][qtd]" id="qtd_{{ $ext->id }}" value="1"
                                                            min="1" disabled onchange="calculateTotal()">
                                                    @else
                                                        <span class="text-xs text-secondary">Único</span>
                                                        <input type="hidden" name="adicionais[{{ $ext->id }}][qtd]" value="1">
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif

                        <!-- Botão Checkout -->
                        <div class="d-flex align-items-center justify-content-between mt-5 pt-3 border-top">
                            <div>
                                <p class="text-sm mb-0">Total Selecionado</p>
                                <h3 class="font-weight-bolder text-primary mb-0" id="totalDisplay">R$ 0,00</h3>
                            </div>
                            <button type="submit" class="btn bg-gradient-success btn-lg mb-0" id="btnCheckout">
                                <i class="fas fa-lock me-2"></i> Ir para o Pagamento
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        function selectPlano(planoId) {
            document.getElementById('plano_' + planoId).checked = true;

            // Remove borda primária de todos
            document.querySelectorAll('.plano-card').forEach(el => {
                el.classList.remove('border-primary');
            });

            // Adiciona borda no selecionado
            document.getElementById('plano_' + planoId).closest('.plano-card').classList.add('border-primary');

            calculateTotal();
        }

        function toggleExtra(extraId) {
            let checkbox = document.getElementById('extra_' + extraId);
            let qtdInput = document.getElementById('qtd_' + extraId);

            if (qtdInput) {
                qtdInput.disabled = !checkbox.checked;
                if (!checkbox.checked) qtdInput.value = 1;
            }

            calculateTotal();
        }

        function calculateTotal() {
            let total = 0;

            // Somar plano principal
            let planos = document.getElementsByName('plano_id');
            planos.forEach(radio => {
                if (radio.checked) {
                    // pegar o valor q esta no onclick. Ou recuperar do span
                    // Vamos extrair do texto por ser mais facil:
                    let card = radio.closest('.plano-card');
                    let valorText = card.querySelector('h4').innerText.replace('R$ ', '').replace('.', '').replace(',', '.');
                    total += parseFloat(valorText);
                }
            });

            // Somar adicionais/extras
            let extras = document.querySelectorAll('.extra-checkbox:checked');
            extras.forEach(check => {
                let valorExtra = parseFloat(check.dataset.valor);
                let extraId = check.dataset.id;
                let qtdInput = document.getElementById('qtd_' + extraId);
                let qtd = 1;

                if (qtdInput && !qtdInput.disabled) {
                    qtd = parseInt(qtdInput.value) || 1;
                }

                total += (valorExtra * qtd);
            });

            // Atualizar interface
            document.getElementById('totalDisplay').innerText = total.toLocaleString('pt-BR', { style: 'currency', currency: 'BRL' });
        }

        // Inicializar somatório ao carregar
        document.addEventListener('DOMContentLoaded', function () {
            calculateTotal();
        });
    </script>
@endpush