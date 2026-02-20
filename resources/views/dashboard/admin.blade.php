@extends('layouts.user_type.auth')

@section('content')

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <div>
                <h3 class="font-weight-bolder mb-0 text-capitalize">Dashboard do Sistema</h3>
                <p class="mb-0 text-sm">Controle Global da Plataforma SaaS</p>
            </div>
            @include('components.loja-selector')
        </div>
    </div>

    <div class="row">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Faturamento (Mês)</p>
                                <h5 class="font-weight-bolder mb-0 text-success">
                                    R$ {{ number_format($pagamentosMes, 2, ',', '.') }}
                                </h5>
                                <small class="text-xs text-secondary">Apenas pagamentos confirmados</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                <i class="fas fa-money-bill-wave text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Receita Líquida (Mês)</p>
                                <h5 class="font-weight-bolder mb-0 text-info">
                                    R$ {{ number_format($totalLiquidoMes, 2, ',', '.') }}
                                </h5>
                                <small class="text-xs text-secondary">Subtraindo estornos</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                <i class="fas fa-chart-line text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Lojas Ativas</p>
                                <h5 class="font-weight-bolder mb-0">
                                    {{ $lojasAtivas }}
                                </h5>
                                <small class="text-xs text-secondary">Terminais em operação</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                <i class="fas fa-store text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold">Licenças a Vencer</p>
                                <h5 class="font-weight-bolder mb-0 text-warning">
                                    {{ $lojasVencer }}
                                </h5>
                                <small class="text-xs text-secondary">Nos próximos 7 dias</small>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                <i class="fas fa-exclamation-circle text-lg opacity-10" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-lg-12 mb-lg-0 mb-4">
            <div class="card z-index-2">
                <div class="card-header pb-0">
                    <h6>Receita de Assinaturas (Últimos 15 Dias)</h6>
                </div>
                <div class="card-body p-3">
                    <div class="chart">
                        <canvas id="chart-admin" class="chart-canvas" height="300"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@push('dashboard')
    <script>
        var labelsGlobal = @json($historicoGlobal->pluck('data')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
        var dadosGlobal = @json($historicoGlobal->pluck('total'));

        window.onload = function () {
            var ctx2 = document.getElementById("chart-admin").getContext("2d");

            var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
            gradientStroke1.addColorStop(1, 'rgba(23, 193, 232, 0.2)');
            gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
            gradientStroke1.addColorStop(0, 'rgba(23, 193, 232, 0)');

            new Chart(ctx2, {
                type: "line",
                data: {
                    labels: labelsGlobal,
                    datasets: [
                        {
                            label: "Receita (R$)",
                            tension: 0.4,
                            borderWidth: 0,
                            pointRadius: 0,
                            borderColor: "#17c1e8",
                            borderWidth: 3,
                            backgroundColor: gradientStroke1,
                            fill: true,
                            data: dadosGlobal,
                            maxBarThickness: 6
                        }
                    ],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    interaction: { intersect: false, mode: 'index' },
                    scales: {
                        y: { grid: { borderDash: [5, 5] }, ticks: { display: true, padding: 10, color: '#b2b9bf' } },
                        x: { grid: { display: false }, ticks: { display: true, color: '#b2b9bf', padding: 20 } },
                    },
                },
            });
        }
    </script>
@endpush