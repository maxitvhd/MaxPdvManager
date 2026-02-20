@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

      @if(session('success'))
        <div class="alert alert-success text-white" role="alert">
          <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger text-white" role="alert">
          <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
      @endif

      <div class="row">
        <div class="col-12">
          <div class="card mb-4">

            <div class="card-header pb-0 d-flex justify-content-between align-items-center">
              <h6>Lista de Lojas</h6>
              <a href="{{ route('lojas.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                <i class="fas fa-plus me-2"></i> Nova Loja
              </a>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Loja / Responsável
                      </th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contato</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status
                      </th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Criação
                      </th>
                      <th class="text-secondary opacity-7">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($lojas as $loja)
                      <tr>
                        <td>
                          <div class="d-flex px-2 py-1">
                            <div>
                              <div
                                class="avatar avatar-sm me-3 bg-gradient-dark d-flex align-items-center justify-content-center rounded-circle">
                                <i class="fas fa-store text-white"></i>
                              </div>
                            </div>
                            <div class="d-flex flex-column justify-content-center">
                              <h6 class="mb-0 text-sm">{{ $loja->nome }}</h6>
                              <p class="text-xs text-secondary mb-0">{{ $loja->email }}</p>
                              @if($loja->cnpj)
                                <p class="text-xs text-secondary mb-0">CNPJ: {{ $loja->cnpj }}</p>
                              @endif
                            </div>
                          </div>
                        </td>

                        <td>
                          <p class="text-xs font-weight-bold mb-0">{{ $loja->telefone }}</p>
                          <p class="text-xs text-secondary mb-0">{{ $loja->cidade }} - {{ $loja->estado }}</p>
                        </td>

                        <td class="align-middle text-center text-sm">
                          @if($loja->status == 'ativo' || $loja->status == 1)
                            <span class="badge badge-sm bg-gradient-success">Ativa</span>
                          @else
                            <span class="badge badge-sm bg-gradient-secondary">Inativa</span>
                          @endif

                          @php
                            $licenca = \App\Models\Licenca::where('loja_id', $loja->id)->first();
                            $grace = $licenca && $licenca->data_inativacao_grace_period
                              ? \Carbon\Carbon::parse($licenca->data_inativacao_grace_period) : null;
                            $atrasada = $grace && $grace->isFuture();
                            $inativada = $grace && $grace->isPast() && $licenca->status === 'inativo';
                          @endphp
                          @if($atrasada)
                            <br><small class="text-warning font-weight-bold"><i class="fas fa-exclamation-triangle"></i>
                              Pagamento Atrasado (Corte em {{$grace->diffInDays()}} dias)</small>
                          @elseif($inativada)
                            <br><small class="text-danger font-weight-bold"><i class="fas fa-ban"></i> Suspensa por
                              Inadimplência</small>
                          @endif
                        </td>

                        <td class="align-middle text-center">
                          <span class="text-secondary text-xs font-weight-bold">
                            {{ $loja->created_at->format('d/m/Y') }}
                          </span>
                        </td>

                        <td class="align-middle">

                          <a href="{{ route('lojas.edit', $loja->codigo) }}"
                            class="text-secondary font-weight-bold text-xs me-3" data-bs-toggle="tooltip"
                            data-bs-title="Editar Loja">
                            <i class="fas fa-pencil-alt text-lg"></i>
                          </a>

                          <a href="{{ route('funcionarios.index', ['loja' => $loja->codigo]) }}"
                            class="text-info font-weight-bold text-xs me-3" data-bs-toggle="tooltip"
                            data-bs-title="Gerenciar Equipe">
                            <i class="fas fa-users-cog text-lg"></i>
                          </a>

                          <a href="{{ route('licencas.index') }}" class="text-warning font-weight-bold text-xs me-3"
                            data-bs-toggle="tooltip" data-bs-title="Gerenciar Licenças">
                            <i class="fas fa-id-card text-lg"></i>
                          </a>

                          <a href="{{ route('pagamentos.faturas', ['loja_codigo' => $loja->codigo]) }}"
                            class="text-success font-weight-bold text-xs me-3" data-bs-toggle="tooltip"
                            data-bs-title="Faturas e Pagamentos">
                            <i class="fas fa-file-invoice-dollar text-lg"></i>
                          </a>

                          <form action="{{ route('lojas.destroy', $loja->codigo) }}" method="POST"
                            class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <a href="javascript:;" class="text-danger font-weight-bold text-xs delete-btn"
                              data-bs-toggle="tooltip" data-bs-title="Excluir Loja">
                              <i class="fas fa-trash text-lg"></i>
                            </a>
                          </form>

                        </td>
                      </tr>
                    @endforeach

                    @if($lojas->isEmpty())
                      <tr>
                        <td colspan="5" class="text-center py-4">
                          <p class="text-sm mb-0">Nenhuma loja encontrada.</p>
                          <a href="{{ route('lojas.create') }}" class="btn btn-sm btn-outline-primary mt-2">
                            Criar primeira loja
                          </a>
                        </td>
                      </tr>
                    @endif

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </main>

@endsection

@push('scripts')
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function () {

      // Inicializa tooltips do Bootstrap (opcional, para ficar bonito ao passar o mouse)
      var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
      var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
      })

      // SweetAlert para Excluir
      const deleteBtns = document.querySelectorAll('.delete-btn');
      deleteBtns.forEach(btn => {
        btn.addEventListener('click', function () {
          const form = this.closest('form');

          Swal.fire({
            title: 'Tem certeza?',
            text: "Ao excluir a loja, todos os dados vinculados a ela serão perdidos!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ea0606',
            cancelButtonColor: '#828282',
            confirmButtonText: 'Sim, excluir loja!',
            cancelButtonText: 'Cancelar'
          }).then((result) => {
            if (result.isConfirmed) {
              form.submit();
            }
          })
        });
      });
    });
  </script>
@endpush