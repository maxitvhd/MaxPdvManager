@extends('layouts.user_type.auth')

@section('content')

  <main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <div class="container-fluid py-4">

      <div class="row">
        <div class="col-12">
          <div class="card mb-4">
            
            <div class="card-header pb-0">
              <div class="d-flex justify-content-between align-items-center">
                  <h5><i class="fas fa-box-open me-2"></i>Lista de Produtos</h5>
                  <a href="{{ route('produtos.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                    <i class="fas fa-plus me-2"></i> Novo Produto
                  </a>
              </div>
            </div>

            <div class="card-body px-0 pt-0 pb-2">
              
              <div class="p-3 pb-0">
                  <form method="GET" action="{{ route('produtos.index') }}">
                    <div class="input-group">
                        <span class="input-group-text text-body"><i class="fas fa-search" aria-hidden="true"></i></span>
                        <input type="text" name="search" class="form-control" placeholder="Digite o nome ou código de barras..." value="{{ request('search') }}">
                    </div>
                  </form>
              </div>

              <div class="table-responsive p-0 mt-3">
                <table class="table align-items-center mb-0">
                  <thead>
                    <tr>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Produto</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Preço</th>
                      <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Estoque</th>
                      <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status Validade</th>
                      <th class="text-secondary opacity-7">Ações</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($produtos as $produto)
                    <tr>
                      <td>
                        <div class="d-flex px-2 py-1">
                          <div>
                            @php
                                // Tenta resolver a imagem: link externo ou arquivo local
                                $imgSrc = asset('assets/img/no-product.png'); // Placeholder padrão
                                
                                if($produto->imagem) {
                                    if(filter_var($produto->imagem, FILTER_VALIDATE_URL)) {
                                        $imgSrc = $produto->imagem;
                                    } elseif($produto->loja) {
                                        // Caminho baseado na lógica do Controller: public/lojas/CODIGO/produtos/IMG
                                        $imgSrc = asset('storage/lojas/' . $produto->loja->codigo . '/produtos/' . $produto->imagem);
                                    }
                                }
                            @endphp
                            <img src="{{ $imgSrc }}" class="avatar avatar-sm me-3 border-radius-lg" alt="produto" style="object-fit: contain; background: #f8f9fa;">
                          </div>
                          <div class="d-flex flex-column justify-content-center">
                            <h6 class="mb-0 text-sm">{{ $produto->nome }}</h6>
                            <p class="text-xs text-secondary mb-0">
                                <i class="fas fa-barcode me-1"></i> {{ $produto->codigo_barra }}
                            </p>
                          </div>
                        </div>
                      </td>

                      <td>
                        <p class="text-xs font-weight-bold mb-0">R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                        @if($produto->preco_atacado > 0)
                            <p class="text-xs text-secondary mb-0">Atacado: R$ {{ number_format($produto->preco_atacado, 2, ',', '.') }}</p>
                        @endif
                      </td>

                      <td>
                        <p class="text-xs font-weight-bold mb-0 {{ $produto->estoque <= $produto->estoque_minimo ? 'text-danger' : 'text-dark' }}">
                            {{ $produto->estoque }} un
                        </p>
                      </td>

                      <td class="align-middle text-center text-sm">
                        @php
                            $dataValidade = $produto->dataValidadeMaisProxima ? $produto->dataValidadeMaisProxima->format('d/m/Y') : '---';
                        @endphp

                        @if($produto->validadeStatus == 'red')
                            <span class="badge badge-sm bg-gradient-danger">Vencido ({{ $dataValidade }})</span>
                        @elseif($produto->validadeStatus == 'orange')
                            <span class="badge badge-sm bg-gradient-warning">Vence em breve ({{ $dataValidade }})</span>
                        @elseif($produto->validadeStatus == 'green')
                            <span class="badge badge-sm bg-gradient-success">Válido ({{ $dataValidade }})</span>
                        @elseif($produto->validadeStatus == 'blue')
                            <span class="badge badge-sm bg-gradient-info">Longa Duração</span>
                        @else
                            <span class="badge badge-sm bg-gradient-secondary">Sem Lote/Validade</span>
                        @endif
                      </td>

                      <td class="align-middle">
                        
                        <a href="{{ route('produtos.show', $produto->id) }}" class="text-secondary font-weight-bold text-xs me-3" 
                           data-bs-toggle="tooltip" data-bs-title="Visualizar Detalhes">
                          <i class="fas fa-eye text-lg"></i>
                        </a>

                        <a href="{{ route('produtos.edit', $produto->id) }}" class="text-secondary font-weight-bold text-xs me-3" 
                           data-bs-toggle="tooltip" data-bs-title="Editar Produto">
                          <i class="fas fa-pencil-alt text-lg"></i>
                        </a>

                        <form action="{{ route('produtos.destroy', $produto->id) }}" method="POST" class="d-inline delete-form">
                            @csrf
                            @method('DELETE')
                            <a href="javascript:;" class="text-danger font-weight-bold text-xs delete-btn" 
                               data-bs-toggle="tooltip" data-bs-title="Excluir Produto">
                              <i class="fas fa-trash text-lg"></i>
                            </a>
                        </form>

                      </td>
                    </tr>
                    @endforeach

                    @if($produtos->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <span class="text-sm text-secondary">Nenhum produto encontrado.</span>
                            </td>
                        </tr>
                    @endif
                  </tbody>
                </table>
              </div>

              <div class="d-flex justify-content-center p-3">
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
        
        // Ativa Tooltips do Bootstrap
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
          return new bootstrap.Tooltip(tooltipTriggerEl)
        })

        // SweetAlert para Deletar
        document.querySelectorAll('.delete-btn').forEach(button => {
            button.addEventListener('click', function(event) {
                event.preventDefault();
                const form = this.closest('form');
                
                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Você não poderá reverter isso! O estoque e histórico deste produto serão perdidos.',
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
            });
        });

        // Script de Busca em Tempo Real (AJAX)
        // Nota: Esse script assume que a rota retorna a View completa. 
        // O ideal para performance seria a rota retornar apenas o HTML da tabela ou JSON.
        // Mas para manter compatibilidade com seu código atual:
        const searchInput = document.querySelector('input[name="search"]');
        let timeout = null; // Debounce para não fazer requisição a cada tecla instantaneamente

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
                    const newTableBody = doc.querySelector('tbody');
                    const newPagination = doc.querySelector('.pagination'); // Se houver paginação

                    if(newTableBody) {
                        document.querySelector('tbody').innerHTML = newTableBody.innerHTML;
                    }
                    
                    // Atualiza ícones de ação e tooltips após o AJAX
                    // Reanexar eventos de delete pode ser necessário se não usar delegação de eventos no body
                });
            }, 500); // Espera 500ms após parar de digitar
        });
    });
</script>
@endpush