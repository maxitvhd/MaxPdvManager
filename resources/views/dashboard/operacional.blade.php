@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <h3>Análise Operacional: <span class="text-info">{{ $loja->nome }}</span></h3>
            @include('components.loja-selector')
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-lg-8 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Fluxo por Horário (Picos de Venda)</h6>
                    <p class="text-sm">Identifique o melhor horário para almoço e trocas de turno.</p>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-horas" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Produtividade da Equipe</h6>
                </div>
                <div class="card-body p-3">
                    <div class="table-responsive">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Operador</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tempo Médio</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Vendas</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($produtividade as $p)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $p->operador }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ number_format($p->tempo_medio, 1) }} seg</p>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="badge badge-sm bg-gradient-success">{{ $p->vendas_qtd }}</span>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="3" class="text-center text-sm">Sem dados</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Vendas por Dia da Semana</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="chart-dias" class="chart-canvas" height="250"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card h-100">
                <div class="card-header pb-0">
                    <h6>Métodos de Pagamento</h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="chart-pagamentos" class="chart-canvas" height="250"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('dashboard')
<script>
    // --- 1. Gráfico de Horas ---
    var ctxHora = document.getElementById("chart-horas").getContext("2d");
    new Chart(ctxHora, {
        type: "bar",
        data: {
            labels: @json($fluxoHorario->pluck('hora')->map(fn($h) => $h.":00")),
            datasets: [{
                label: "Vendas",
                weight: 5,
                borderWidth: 0,
                borderRadius: 4,
                backgroundColor: '#3A416F',
                data: @json($fluxoHorario->pluck('total_vendas')),
                fill: false
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { grid: { borderDash: [5, 5] } },
                x: { grid: { display: false } },
            },
        },
    });

    // --- 2. Gráfico de Dias da Semana ---
    var diasNomes = {1: 'Dom', 2: 'Seg', 3: 'Ter', 4: 'Qua', 5: 'Qui', 6: 'Sex', 7: 'Sáb'};
    var dadosDias = @json($diasSemana);
    var labelsDias = dadosDias.map(d => diasNomes[d.dia]);
    
    var ctxDia = document.getElementById("chart-dias").getContext("2d");
    new Chart(ctxDia, {
        type: "line",
        data: {
            labels: labelsDias,
            datasets: [{
                label: "Total (R$)",
                tension: 0.4,
                borderColor: "#cb0c9f",
                borderWidth: 3,
                backgroundColor: 'rgba(203,12,159,0.2)',
                fill: true,
                data: dadosDias.map(d => d.total),
            }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });

    // --- 3. Gráfico de Pagamentos (Pizza) ---
    var ctxPag = document.getElementById("chart-pagamentos").getContext("2d");
    new Chart(ctxPag, {
        type: "doughnut",
        data: {
            labels: @json($pagamentos->pluck('metodo_pagamento')),
            datasets: [{
                data: @json($pagamentos->pluck('valor')),
                backgroundColor: ['#17c1e8', '#cb0c9f', '#3A416F', '#a8b8d8', '#82d616'],
                borderWidth: 0
            }],
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
@endsection