@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
    <div class="container-fluid py-4">

      <div class="row">
        <div class="col-12">
          <div class="card mb-4 shadow-sm border-0 rounded-4">
            
            <div class="card-header pb-0 bg-transparent border-0 mt-2 px-4">
              <div class="d-flex justify-content-between align-items-center">
                  <h5 class="mb-0 text-dark font-weight-bolder">
                      <i class="fas fa-boxes text-primary me-2"></i>Catálogo de Produtos
                  </h5>
                  <a href="{{ route('produtos.create') }}" class="btn bg-gradient-primary btn-sm mb-0 px-4">
                    <i class="fas fa-plus me-2"></i> Novo Produto
                  </a>
              </div>
            </div>

            <div class="card-body px-0 pt-2 pb-2">
              
              <div class="px-4 pb-3 pt-2">
                  <form method="GET" action="{{ route('produtos.index') }}">
                    <div class="input-group input-group-outline shadow-sm">
                        <span class="input-group-text bg-light text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Buscar por nome, código de barras ou marca..." value="{{ request('search') }}">
                    </div>
                  </form>
              </div>

              <div class="table-responsive p-0">
                <table class="table align-items-center mb-0 table-hover">
                  <thead class="bg-light">
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-4">Produto & Código</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Preço (R$)</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estoque</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Validade / Status</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($produtos as $produto)
                    <tr>
                      <td>
                        <div class="d-flex px-3 py-1 align-items-center">
                          <div class="me-3 shadow-sm rounded-3 bg-white p-1" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                            @php
                                $imgLoja = asset('storage/lojas/' . ($produto->loja->codigo ?? Auth::user()->codigo) . '/produtos/' . $produto->imagem);
                                $imgGlobal = asset('storage/produtos_full/' . $produto->imagem);
                                $imgFallback = 'https://via.placeholder.com/100x100.png?text=Sem+Foto';
                            @endphp
                            
                            @if($produto->imagem)
                                <img src="{{ $imgLoja }}" 
                                     alt="produto" 
                                     class="img-fluid rounded-2" 
                                     style="max-height: 40px; max-width: 40px; object-fit: contain;"
                                     onerror="this.onerror=null; this.src='{{ $imgGlobal }}'; if(this.src=='{{ $imgGlobal }}') this.src='{{ $imgFallback }}';">
                            @else
                                <i class="fas fa-box text-secondary fa-lg"></i>
                            @endif
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm font-weight-bold text-dark text-truncate" style="max-width: 250px;" title="{{ $produto->nome }}">
                                {{ $produto->nome }}
                            </h6>
                            <p class="text-xs text-muted mb-0">
                                <i class="fas fa-barcode me-1"></i> {{ $produto->codigo_barra }}
                            </p>
                          </div>
                        </div>
                      </td>

                      <td>
                        <p class="text-sm font-weight-bold text-success mb-0">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                        @if($produto->preco_atacado > 0)
                            <p class="text-xs text-muted mb-0" title="Preço no Atacado">
                                <i class="fas fa-layer-group text-xxs me-1"></i>R$ {{ number_format($produto->preco_atacado, 2, ',', '.') }}
                            </p>
                        @endif
                      </td>

                      <td>
                        @php
                            $isEstoqueBaixo = $produto->estoque <= $produto->estoque_minimo;
                        @endphp
                        <span class="badge badge-sm {{ $isEstoqueBaixo ? 'bg-gradient-danger' : 'bg-light text-dark border' }}">
                            {{ $produto->estoque }} un
                        </span>
                        @if($isEstoqueBaixo)
                            <p class="text-xxs text-danger mb-0 mt-1"><i class="fas fa-exclamation-triangle me-1"></i>Estoque Baixo</p>
                        @endif
                      </td>

                      <td class="align-middle text-center text-sm">
                        @php
                            $dataValidade = $produto->dataValidadeMaisProxima ? $produto->dataValidadeMaisProxima->format('d/m/y') : '---';
                        @endphp

                        @if($produto->validadeStatus == 'red')
                            <span class="badge badge-sm bg-gradient-danger"><i class="fas fa-times-circle me-1"></i>Vencido ({{ $dataValidade }})</span>
                        @elseif($produto->validadeStatus == 'orange')
                            <span class="badge badge-sm bg-gradient-warning"><i class="fas fa-clock me-1"></i>Vence breve ({{ $dataValidade }})</span>
                        @elseif($produto->validadeStatus == 'green')
                            <span class="badge badge-sm bg-gradient-success"><i class="fas fa-check-circle me-1"></i>No Prazo ({{ $dataValidade }})</span>
                        @elseif($produto->validadeStatus == 'blue')
                            <span class="badge badge-sm bg-gradient-info">Longo Prazo</span>
                        @else
                            <span class="text-xs text-muted">Sem Lote</span>
                        @endif
                      </td>

                      <td class="align-middle text-center">
                        <div class="d-flex justify-content-center align-items-center">
                            <a href="{{ route('produtos.show', $produto->id) }}" class="btn btn-link text-info px-2 mb-0" 
                               data-bs-toggle="tooltip" data-bs-title="Visualizar Detalhes">
                              <i class="fas fa-eye text-lg"></i>
                            </a>

                            <a href="{{ route('produtos.edit', $produto->id) }}" class="btn btn-link text-dark px-2 mb-0" 
                               data-bs-toggle="tooltip" data-bs-title="Editar Produto">
                              <i class="fas fa-edit text-lg"></i>
                            </a>

                            <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="btn btn-link text-danger px-2 mb-0 delete-btn" 
                                   data-bs-toggle="tooltip" data-bs-title="Excluir Produto">
                                  <i class="fas fa-trash-alt text-lg"></i>
                                </button>
                            </form>
                        </div>
                      </td>
                    </tr>
                    @endforeach

                    @if($produtos->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center">
                                    <i class="fas fa-search fa-3x text-light mb-3"></i>
                                    <h6 class="text-secondary font-weight-normal">Nenhum produto encontrado.</h6>
                                    <p class="text-sm text-muted">Tente buscar por outro termo ou cadastre um novo produto.</p>
                                </div>
                            </td>
                        </tr>
                    @endif
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-center p-4">
                  {{ $produtos->links() }}
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
    document.addEventListener('DOMContentLoaded', function() {
        
        // Inicializa os Tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // SweetAlert para Exclusão
        document.body.addEventListener('click', function(event) {
            // Delegação de evento (funciona mesmo após o AJAX)
            let btn = event.target.closest('.delete-btn');
            if (btn) {
                event.preventDefault();
                const form = btn.closest('form');
                
                Swal.fire({
                    title: 'Excluir Produto?',
                    text: 'Esta ação removerá o produto do estoque. Você não poderá reverter!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ea0606',
                    cancelButtonColor: '#828282',
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            }
        });

        // Busca Ajax Inteligente
        const searchInput = document.querySelector('input[name="search"]');
        let timeout = null;

        if(searchInput) {
            searchInput.addEventListener('input', function() {
                clearTimeout(timeout);
                const search = this.value;

                timeout = setTimeout(() => {
                    fetch(`{{ route('produtos.index') }}?search=${search}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        const parser = new DOMParser();
                        const doc = parser.parseFromString(html, 'text/html');
                        
                        // Atualiza a tabela
                        const newTableBody = doc.querySelector('tbody');
                        if(newTableBody) {
                            document.querySelector('tbody').innerHTML = newTableBody.innerHTML;
                        }
                        
                        // Atualiza a paginação (se houver)
                        const currentPagination = document.querySelector('.pagination');
                        const newPagination = doc.querySelector('.pagination');
                        
                        if(newPagination && currentPagination) {
                            currentPagination.parentNode.innerHTML = newPagination.parentNode.innerHTML;
                        } else if (!newPagination && currentPagination) {
                            currentPagination.innerHTML = ''; // Limpa se não tiver mais páginas
                        }

                        // Re-inicializa os tooltips para os novos elementos injetados
                        var newTooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
                        newTooltipTriggerList.map(function (tooltipTriggerEl) {
                            return new bootstrap.Tooltip(tooltipTriggerEl);
                        });
                    })
                    .catch(error => console.error('Erro na busca:', error));
                }, 400); 
            });
        }
    });
</script>
@endpush