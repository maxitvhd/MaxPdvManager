@extends('layouts.user_type.auth')

@section('content')

  <div class="row mb-3">
    <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
            <h3 class="font-weight-bolder mb-0 text-capitalize">{{ $loja->nome }}</h3>
            <p class="mb-0 text-sm">Visão geral de hoje: {{ \Carbon\Carbon::now()->format('d/m/Y') }}</p>
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
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Faturamento Hoje</p>
                <h5 class="font-weight-bolder mb-0">
                  R$ {{ number_format($vendasHoje, 2, ',', '.') }}
                  <span class="{{ $crescimentoDiario >= 0 ? 'text-success' : 'text-danger' }} text-sm font-weight-bolder">
                    {{ $crescimentoDiario >= 0 ? '+' : '' }}{{ number_format($crescimentoDiario, 0) }}%
                  </span>
                </h5>
                <small class="text-xs text-secondary">vs. semana passada</small>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                <i class="ni ni-money-coins text-lg opacity-10" aria-hidden="true"></i>
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
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Lucro Estimado</p>
                <h5 class="font-weight-bolder mb-0 text-success">
                  R$ {{ number_format($lucroHoje, 2, ',', '.') }}
                </h5>
                <small class="text-xs text-secondary">Baseado no custo</small>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                <i class="ni ni-diamond text-lg opacity-10" aria-hidden="true"></i>
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
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Total Vendas</p>
                <h5 class="font-weight-bolder mb-0">
                  {{ $qtdVendasHoje }}
                </h5>
                <small class="text-xs text-secondary">Tickets emitidos</small>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                <i class="ni ni-cart text-lg opacity-10" aria-hidden="true"></i>
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
                <p class="text-sm mb-0 text-capitalize font-weight-bold">Ticket Médio</p>
                <h5 class="font-weight-bolder mb-0">
                  R$ {{ number_format($ticketMedio, 2, ',', '.') }}
                </h5>
                <small class="text-xs text-secondary">Por cliente</small>
              </div>
            </div>
            <div class="col-4 text-end">
              <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                <i class="ni ni-chart-bar-32 text-lg opacity-10" aria-hidden="true"></i>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
    <div class="col-lg-7 mb-lg-0 mb-4">
      <div class="card z-index-2">
        <div class="card-header pb-0">
          <h6>Performance Financeira (7 Dias)</h6>
          <p class="text-sm">
            <i class="fa fa-arrow-up text-success"></i>
            <span class="font-weight-bold">Faturamento</span> vs <span class="font-weight-bold text-success">Lucro</span>
          </p>
        </div>
        <div class="card-body p-3">
          <div class="chart">
            <canvas id="chart-line" class="chart-canvas" height="300"></canvas>
          </div>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card h-100">
        <div class="card-header pb-0 p-3">
          <div class="d-flex justify-content-between">
            <h6 class="mb-0">Mix de Produtos (Por Faturamento)</h6>
          </div>
        </div>
        <div class="card-body p-3">
            <div class="chart mb-3">
                <canvas id="chart-categorias" class="chart-canvas" height="200"></canvas>
            </div>
            <ul class="list-group">
                @foreach($categorias as $cat)
                <li class="list-group-item border-0 d-flex justify-content-between ps-0 mb-2 border-radius-lg">
                  <div class="d-flex align-items-center">
                    <div class="icon icon-shape icon-sm me-3 bg-gradient-dark shadow text-center">
                      <i class="ni ni-tag text-white opacity-10"></i>
                    </div>
                    <div class="d-flex flex-column">
                      <h6 class="mb-1 text-dark text-sm">{{ $cat->categoria }}</h6>
                    </div>
                  </div>
                  <div class="d-flex align-items-center text-dark text-sm font-weight-bold">
                    R$ {{ number_format($cat->total, 2, ',', '.') }}
                  </div>
                </li>
                @endforeach
            </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="row mt-4">
      <div class="col-12">
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h6>Top 5 Produtos Mais Vendidos (Volume)</h6>
          </div>
          <div class="card-body px-0 pt-0 pb-2">
            <div class="table-responsive p-0">
              <table class="table align-items-center justify-content-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Quantidade</th>
                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Tendência</th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($topProdutos as $prod)
                  <tr>
                    <td>
                      <div class="d-flex px-2">
                        <div>
                          <img src="../assets/img/small-logos/icon-bulb.svg" class="avatar avatar-sm rounded-circle me-2" alt="spotify">
                        </div>
                        <div class="my-auto">
                          <h6 class="mb-0 text-sm">{{ $prod->produto_nome }}</h6>
                        </div>
                      </div>
                    </td>
                    <td>
                      <p class="text-sm font-weight-bold mb-0">{{ intval($prod->qtd) }} Un.</p>
                    </td>
                    <td>
                      <span class="text-xs font-weight-bold text-success">+ Alta demanda</span>
                    </td>
                    <td class="align-middle">
                        <div class="progress-wrapper w-75 mx-auto">
                            <div class="progress-info">
                              <div class="progress-percentage">
                                <span class="text-xs font-weight-bold">{{ intval($prod->qtd) }}</span>
                              </div>
                            </div>
                            <div class="progress">
                              <div class="progress-bar bg-gradient-info" style="width: 80%;"></div>
                            </div>
                        </div>
                    </td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
  </div>

@endsection

@push('dashboard')
  <script>
    // --- 1. PREPARAÇÃO DE DADOS ---
    // Gráfico de Linha (Faturamento vs Lucro)
    var labelsSemana = @json($historico7Dias->pluck('data')->map(fn($d) => \Carbon\Carbon::parse($d)->format('d/m')));
    var dadosFat = @json($historico7Dias->pluck('faturamento'));
    
    // Alinhando o lucro com as datas (caso algum dia tenha venda mas custo zero ou vice versa)
    // Para simplificar, assumimos que o Controller ordenou ambos por data.
    var dadosLucro = @json($lucro7Dias->pluck('lucro'));

    // Gráfico de Pizza (Categorias)
    var catLabels = @json($categorias->pluck('categoria'));
    var catDados = @json($categorias->pluck('total'));


    window.onload = function() {
      var ctx2 = document.getElementById("chart-line").getContext("2d");

      var gradientStroke1 = ctx2.createLinearGradient(0, 230, 0, 50);
      gradientStroke1.addColorStop(1, 'rgba(203,12,159,0.2)');
      gradientStroke1.addColorStop(0.2, 'rgba(72,72,176,0.0)');
      gradientStroke1.addColorStop(0, 'rgba(203,12,159,0)'); //purple colors

      var gradientStroke2 = ctx2.createLinearGradient(0, 230, 0, 50);
      gradientStroke2.addColorStop(1, 'rgba(20,23,39,0.2)');
      gradientStroke2.addColorStop(0.2, 'rgba(72,72,176,0.0)');
      gradientStroke2.addColorStop(0, 'rgba(20,23,39,0)'); //purple colors

      new Chart(ctx2, {
        type: "line",
        data: {
          labels: labelsSemana,
          datasets: [
            {
              label: "Faturamento",
              tension: 0.4,
              borderWidth: 0,
              pointRadius: 0,
              borderColor: "#cb0c9f",
              borderWidth: 3,
              backgroundColor: gradientStroke1,
              fill: true,
              data: dadosFat,
              maxBarThickness: 6
            },
            {
              label: "Lucro Estimado",
              tension: 0.4,
              borderWidth: 0,
              pointRadius: 0,
              borderColor: "#82d616", // Verde
              borderWidth: 3,
              backgroundColor: gradientStroke2, // Sem preenchimento ou leve
              fill: true,
              data: dadosLucro,
              maxBarThickness: 6
            },
          ],
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          plugins: { legend: { display: true } },
          interaction: { intersect: false, mode: 'index' },
          scales: {
            y: { grid: { borderDash: [5, 5] }, ticks: { display: true, padding: 10, color: '#b2b9bf' } },
            x: { grid: { display: false }, ticks: { display: true, color: '#b2b9bf', padding: 20 } },
          },
        },
      });

      // GRÁFICO DE CATEGORIAS (Doughnut)
      var ctxCat = document.getElementById("chart-categorias").getContext("2d");
      new Chart(ctxCat, {
        type: "doughnut",
        data: {
            labels: catLabels,
            datasets: [{
                data: catDados,
                backgroundColor: ['#3A416F', '#cb0c9f', '#17c1e8', '#82d616', '#f53939'],
                borderWidth: 0
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } } // Legenda oculta pois já tem a lista embaixo
        }
      });
    }
  </script>
@endpush