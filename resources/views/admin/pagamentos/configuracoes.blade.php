@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">

                <div class="card-header pb-0 border-bottom">
                    <h6 class="mb-0">Configurações de Pagamento (Admin)</h6>
                    <p class="text-sm">Configure as credenciais do Mercado Pago e as regras do módulo financeiro desta
                        plataforma.</p>
                </div>

                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success text-white">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ url('pagamentos/configuracoes') }}" method="POST">
                        @csrf

                        <h6 class="font-weight-bolder mb-3 text-uppercase text-xs">Integração Mercado Pago</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mercadopago_public_key" class="form-control-label">Public Key</label>
                                    <input class="form-control" type="text" id="mercadopago_public_key"
                                        name="mercadopago_public_key"
                                        value="{{ old('mercadopago_public_key', $config->mercadopago_public_key ?? '') }}"
                                        placeholder="APP_USR-...">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="mercadopago_access_token" class="form-control-label">Access Token</label>
                                    <input class="form-control" type="text" id="mercadopago_access_token"
                                        name="mercadopago_access_token"
                                        value="{{ old('mercadopago_access_token', $config->mercadopago_access_token ?? '') }}"
                                        placeholder="APP_USR-...">
                                </div>
                            </div>
                        </div>

                        <hr class="horizontal dark mt-4 mb-4">

                        <h6 class="font-weight-bolder mb-3 text-uppercase text-xs">Regras de Negócio</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email_recebimento" class="form-control-label">Email de Recebimento
                                        (Notificação Interna)</label>
                                    <input class="form-control" type="email" id="email_recebimento" name="email_recebimento"
                                        value="{{ old('email_recebimento', $config->email_recebimento ?? '') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="carencia_dias" class="form-control-label">Dias de Carência (Grace Period)
                                        após o Vencimento</label>
                                    <input class="form-control" type="number" id="carencia_dias" name="carencia_dias"
                                        value="{{ old('carencia_dias', $config->carencia_dias ?? 10) }}" min="0">
                                    <small class="text-xs text-muted">A licença do usuário só será suspensa se a fatura
                                        continuar em aberto após passar essa quantidade de dias do vencimento.</small>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn bg-gradient-dark btn-md mb-0">Salvar Configurações</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection