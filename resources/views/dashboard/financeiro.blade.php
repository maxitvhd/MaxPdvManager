@extends('layouts.user_type.auth')

@section('content')

<div class="container-fluid py-4">
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="font-weight-bolder text-white mb-0" style="text-shadow: 0px 1px 3px rgba(0,0,0,0.5);">
                    Gestão Financeira & Crédito
                </h3>
                <p class="text-white text-sm opacity-8">Análise de inadimplência e fluxo de recebimentos</p>
            </div>
            @include('components.loja-selector')
        </div>
    </div>

    <div class="row">
        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <div class="card bg-gradient-danger shadow-danger">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-white opacity-8">Total a Receber (Fiado)</p>
                                <h4 class="font-weight-bolder mb-0 text-white">
                                    R$ {{ number_format($totalReceber, 2, ',', '.') }}
                                </h4>
                                <small class="text-white font-weight-bold">Saldo total em aberto na praça</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                <i class="ni ni-money-coins text-lg text-danger opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
            <div class="card bg-gradient-success shadow-success">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-white opacity-8">Recuperado este Mês</p>
                                <h4 class="font-weight-bolder mb-0 text-white">
                                    R$ {{ number_format($totalRecebidoMes, 2, ',', '.') }}
                                </h4>
                                <small class="text-white font-weight-bold">Pagamentos de dívidas recebidos</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-white shadow text-center border-radius-md">
                                <i class="ni ni-check-bold text-lg text-success opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Fluxo de Crédito (30 Dias)</h6>
                    <p class="text-sm">
                        <i class="fa fa-arrow-up text-danger"></i> Vendas a Prazo vs 
                        <i class="fa fa-arrow-down text-success"></i> Pagamentos Recebidos
                    </p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-fluxo" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-7 mb-lg-0 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0 p-3">
                    <div class="d-flex justify-content-between">
                        <h6 class="mb-0 text-danger">⚠️ Régua de Cobrança (Maiores Devedores)</h6>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Situação</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cobrar</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($maioresDevedores as $cliente)
                                    @php
                                        $porc = $cliente->limite_credito > 0 ? ($cliente->credito_usado / $cliente->limite_credito) * 100 : 100;
                                        $cor = $porc > 90 ? 'danger' : ($porc > 70 ? 'warning' : 'info');
                                        $foneLimpo = preg_replace('/[^0-9]/', '', $cliente->telefone);
                                        $msg = "Olá {$cliente->nome}, aqui é do {$loja->nome}. Identificamos um saldo pendente de R$ " . number_format($cliente->credito_usado, 2, ',', '.') . ". Podemos agendar o pagamento?";
                                        $zap = "https://wa.me/55{$foneLimpo}?text=" . urlencode($msg);
                                    @endphp
                                <tr>
                                    <td class="w-30">
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm text-truncate" style="max-width: 150px;">{{ $cliente->nome }}</h6>
                                                <p class="text-xs text-secondary mb-0">{{ $cliente->telefone ?: 'S/ Fone' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <span class="text-sm font-weight-bold text-danger mb-1">
                                                R$ {{ number_format($cliente->credito_usado, 2, ',', '.') }}
                                            </span>
                                            <div class="d-flex align-items-center">
                                                <span class="text-xs me-2">{{ number_format($porc, 0) }}% do limite</span>
                                                <div class="progress w-100" style="height: 4px;">
                                                    <div class="progress-bar bg-gradient-{{ $cor }}" style="width: {{ $porc }}%"></div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        @if($cliente->telefone && strlen($foneLimpo) >= 10)
                                            <a href="{{ $zap }}" target="_blank" class="btn btn-sm btn-outline-success btn-icon-only rounded-circle mb-0" data-bs-toggle="tooltip" title="Cobrar no WhatsApp">
                                                <i class="fab fa-whatsapp text-lg"></i>
                                            </a>
                                        @else
                                            <button disabled class="btn btn-sm btn-outline-secondary btn-icon-only rounded-circle mb-0">
                                                <i class="fas fa-phone-slash"></i>
                                            </button>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-sm py-3">Nenhuma dívida crítica.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header pb-0 p-3 bg-gradient-faded-light">
                    <h6 class="mb-0 text-success">🏆 Melhores Pagadores (Ranking)</h6>
                    <p class="text-xs text-secondary mb-0">Clientes que mais pagaram dívidas recentemente</p>
                </div>
                <div class="card-body p-3">
                    <ul class="list-group">
                        @forelse($melhoresPagadores as $pagador)
                        <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                            <div class="d-flex align-items-center">
                                <button class="btn btn-icon-only btn-rounded btn-outline-success mb-0 me-3 btn-sm d-flex align-items-center justify-content-center">
                                    <i class="fas fa-crown"></i>
                                </button>
                                <div class="d-flex flex-column">
                                    <h6 class="mb-1 text-dark text-sm">{{ $pagador->nome }}</h6>
                                    <span class="text-xs text-secondary">
                                        {{ $pagador->qtd_pagamentos }} pagamentos realizados
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex flex-column align-items-end">
                                <span class="text-success text-gradient text-sm font-weight-bold">
                                    + R$ {{ number_format($pagador->total_pago, 2, ',', '.') }}
                                </span>
                                <span class="text-xxs text-secondary">
                                    Último: {{ \Carbon\Carbon::parse($pagador->ultimo_pagamento)->format('d/m') }}
                                </span>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-sm py-3">Sem histórico de pagamentos recente.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('dashboard')
<script>
    // Configuração do Gráfico de Fluxo de Caixa
    var ctxFluxo = document.getElementById("chart-fluxo").getContext("2d");

    // Cores Gradients
    var gradVenda = ctxFluxo.createLinearGradient(0, 230, 0, 50);
    gradVenda.addColorStop(1, 'rgba(234, 6, 6, 0.2)'); // Vermelho
    gradVenda.addColorStop(0, 'rgba(234, 6, 6, 0)');

    var gradPag = ctxFluxo.createLinearGradient(0, 230, 0, 50);
    gradPag.addColorStop(1, 'rgba(23, 193, 232, 0.2)'); // Azul/Verde
    gradPag.addColorStop(0, 'rgba(23, 193, 232, 0)');

    var labels = @json($fluxoCaixa->pluck('data')->map(function($date){ return \Carbon\Carbon::parse($date)->format('d/m'); }));
    var dataVendas = @json($fluxoCaixa->pluck('total_vendido'));
    var dataPagos = @json($fluxoCaixa->pluck('total_pago'));

    new Chart(ctxFluxo, {
        type: "line",
        data: {
            labels: labels,
            datasets: [
                {
                    label: "Vendas a Prazo (Aumenta Dívida)",
                    tension: 0.4,
                    borderColor: "#ea0606", // Vermelho
                    borderWidth: 2,
                    backgroundColor: gradVenda,
                    fill: true,
                    data: dataVendas,
                    maxBarThickness: 6
                },
                {
                    label: "Pagamentos Recebidos (Diminui Dívida)",
                    tension: 0.4,
                    borderColor: "#17c1e8", // Azul
                    borderWidth: 2,
                    backgroundColor: gradPag,
                    fill: true,
                    data: dataPagos,
                    maxBarThickness: 6
                }
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: { borderDash: [5, 5] },
                    ticks: { display: true, padding: 10, color: '#b2b9bf' }
                },
                x: {
                    grid: { display: false },
                    ticks: { display: true, color: '#b2b9bf', padding: 20 }
                },
            },
        },
    });
</script>
@endpush