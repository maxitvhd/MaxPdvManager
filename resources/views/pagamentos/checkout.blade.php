@extends('layouts.user_type.auth')

@section('content')

    <div class="row mt-4">
        <div class="col-lg-6 col-12 mx-auto">
            <div class="card h-100 p-3">
                <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-3 text-center">

                    <div
                        class="icon icon-shape icon-lg mb-4 bg-gradient-success shadow text-center mx-auto border-radius-md">
                        <i class="ni ni-credit-card text-white opacity-10"></i>
                    </div>

                    <h5 class="text-dark font-weight-bolder mb-2 pt-2">Checkout Mercado Pago - PIX</h5>
                    <p class="text-secondary text-sm">Abra o app do seu banco e escaneie o código abaixo (ou copie a chave
                        PIX Copia e Cola).</p>

                    <div class="bg-gray-100 border-radius-lg my-4 p-4 row text-center">
                        <div class="col-12 text-center mx-auto">
                            @if(isset($pix['point_of_interaction']['transaction_data']['qr_code_base64']))
                                <img src="data:image/jpeg;base64,{{ $pix['point_of_interaction']['transaction_data']['qr_code_base64'] }}"
                                    class="img-fluid border-radius-lg border border-2 shadow-sm" style="max-width: 200px"
                                    alt="QR Code PIX">
                            @else
                                <span class="text-danger">Erro ao gerar código PIX. Tente novamente mais tarde.</span>
                                <!-- Exibir erro para debug se necessário: {{ json_encode($pix) }} -->
                            @endif
                        </div>
                        <div class="col-12 mt-3 text-center">
                            <p class="text-sm font-weight-bold mb-1">Total a Pagar:</p>
                            <h4 class="text-success mb-0">R$ {{ number_format($pagamento->valor, 2, ',', '.') }}</h4>
                        </div>
                    </div>

                    @if(isset($pix['point_of_interaction']['transaction_data']['qr_code']))
                        <div class="form-group mb-4">
                            <label>PIX Copia e Cola:</label>
                            <textarea class="form-control text-sm" rows="3"
                                readonly>{{ $pix['point_of_interaction']['transaction_data']['qr_code'] }}</textarea>
                            <small class="text-muted text-xs mt-1">Copie o código acima na área "PIX Retira/Copia e Cola" do seu
                                Banco.</small>
                        </div>
                    @endif

                    <a class="btn bg-gradient-dark text-white text-sm font-weight-bold mb-0 mt-auto"
                        href="{{ route('pagamentos.faturas') }}">
                        Voltar para Faturas
                        <i class="fas fa-undo-alt text-sm ms-1" aria-hidden="true"></i>
                    </a>

                </div>
            </div>
        </div>
    </div>

@endsection