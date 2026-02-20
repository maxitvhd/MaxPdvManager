@extends('layouts.user_type.auth')

@section('content')

    <div class="row align-items-center justify-content-center mt-5">
        <div class="col-lg-5 col-12 mx-auto">
            <div class="card h-100 p-3 shadow-lg">
                <div class="card-body position-relative z-index-1 d-flex flex-column h-100 p-4 text-center">

                    @if($status === 'sucesso')
                        <div class="icon icon-shape icon-xl mb-4 bg-gradient-success shadow text-center mx-auto border-radius-md rounded-circle"
                            style="width: 80px; height: 80px">
                            <i class="fas fa-check text-white mt-3" style="font-size: 30px"></i>
                        </div>
                        <h4 class="text-dark font-weight-bolder mb-2">Pagamento Aprovado!</h4>
                        <p class="text-secondary text-sm">Pronto, sua licença e serviços foram reestabelecidos / renovados e já
                            constam em nosso sistema.</p>
                    @elseif($status === 'falha')
                        <div class="icon icon-shape icon-xl mb-4 bg-gradient-danger shadow text-center mx-auto border-radius-md rounded-circle"
                            style="width: 80px; height: 80px">
                            <i class="fas fa-times text-white mt-3" style="font-size: 30px"></i>
                        </div>
                        <h4 class="text-dark font-weight-bolder mb-2">Ops! Pagamento Recusado.</h4>
                        <p class="text-secondary text-sm">Houve um problema ao processar seu cartão ou fatura, por favor, tente
                            novamente ou fale conosco.</p>
                    @elseif($status === 'pendente')
                        <div class="icon icon-shape icon-xl mb-4 bg-gradient-warning shadow text-center mx-auto border-radius-md rounded-circle"
                            style="width: 80px; height: 80px">
                            <i class="fas fa-clock text-white mt-3" style="font-size: 30px"></i>
                        </div>
                        <h4 class="text-dark font-weight-bolder mb-2">Em Análise...</h4>
                        <p class="text-secondary text-sm">Seu pagamento está sendo processado pelo banco ou operadora de cartão
                            e deve ser confirmado em breve.</p>
                    @endif

                    <div class="mt-5">
                        <a href="{{ route('pagamentos.faturas') }}" class="btn bg-gradient-dark btn-md mb-0">Ver Minhas
                            Faturas</a>
                    </div>

                </div>
            </div>
        </div>
    </div>

@endsection