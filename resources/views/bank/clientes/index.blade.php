@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">

    {{-- Cabeçalho --}}
    <div class="row mb-4">
      <div class="col-12 d-flex justify-content-between align-items-center">
        <div>
          <h4 class="mb-1 text-dark"><i class="fas fa-university me-2 text-primary"></i>MaxBank — Clientes</h4>
          <p class="text-sm text-muted mb-0">Gerencie contas de crédito dos clientes</p>
        </div>
        <a href="{{ route('bank.clientes.create') }}{{ $lojaAtual ? '?loja_codigo='.$lojaAtual->codigo : '' }}"
           class="btn bg-gradient-primary btn-sm">
          <i class="fas fa-user-plus me-1"></i> Novo Cliente
        </a>
      </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
      <div class="alert alert-success text-white alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close text-white" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger text-white alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close text-white" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Filtros --}}
    <div class="card mb-4">
      <div class="card-body py-3">
        <form method="GET" action="{{ route('bank.clientes.index') }}" class="row g-2 align-items-end">
          {{-- Filtro por loja --}}
          @if($lojas->count() > 1)
          <div class="col-md-3">
            <label class="form-label text-xs font-weight-bold text-uppercase">Loja</label>
            <select name="loja_codigo" class="form-control form-control-sm" onchange="this.form.submit()">
              @foreach($lojas as $loja)
                <option value="{{ $loja->codigo }}" {{ $lojaAtual && $lojaAtual->id === $loja->id ? 'selected' : '' }}>
                  {{ $loja->nome }}
                </option>
              @endforeach
            </select>
          </div>
          @else
            <input type="hidden" name="loja_codigo" value="{{ optional($lojaAtual)->codigo }}">
          @endif

          {{-- Busca --}}
          <div class="col-md-4">
            <label class="form-label text-xs font-weight-bold text-uppercase">Buscar</label>
            <input type="text" name="busca" class="form-control form-control-sm"
                   placeholder="Nome, código, usuário, telefone..."
                   value="{{ request('busca') }}">
          </div>

          {{-- Status --}}
          <div class="col-md-3">
            <label class="form-label text-xs font-weight-bold text-uppercase">Status</label>
            <select name="status" class="form-control form-control-sm">
              <option value="">Todos</option>
              @foreach(['esp_facial'=>'Aguard. Facial', 'esp_documentos'=>'Aguard. Docs', 'esp_dados'=>'Aguard. Dados', 'processando'=>'Em Análise', 'ativo'=>'Ativo', 'bloqueado'=>'Bloqueado', 'pag_atrasado'=>'Pag. Atrasado', 'cobranca'=>'Cobrança', 'juridico'=>'Jurídico', 'cancelado'=>'Cancelado'] as $val => $lbl)
                <option value="{{ $val }}" {{ request('status') === $val ? 'selected' : '' }}>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <button type="submit" class="btn bg-gradient-primary btn-sm w-100">
              <i class="fas fa-search me-1"></i> Filtrar
            </button>
          </div>
        </form>
      </div>
    </div>

    {{-- Tabela de clientes --}}
    <div class="card">
      <div class="card-body px-0 pt-0 pb-2">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cliente / Código</th>
                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Contato</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Crédito</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Cadastro</th>
                <th class="text-secondary opacity-7">Ações</th>
              </tr>
            </thead>
            <tbody>
              @forelse($clientes as $cliente)
              <tr>
                <td>
                  <div class="d-flex px-2 py-1">
                    <div class="me-3">
                      @if($cliente->foto_perfil && Storage::disk('local')->exists($cliente->foto_perfil))
                        <img src="{{ route('bank.clientes.show', $cliente->codigo) }}" class="avatar avatar-sm rounded-circle" alt="{{ $cliente->nome }}">
                      @else
                        <div class="avatar avatar-sm bg-gradient-primary d-flex align-items-center justify-content-center rounded-circle text-white font-weight-bold">
                          {{ strtoupper(substr($cliente->nome, 0, 1)) }}
                        </div>
                      @endif
                    </div>
                    <div class="d-flex flex-column justify-content-center">
                      <h6 class="mb-0 text-sm">{{ $cliente->nome }}</h6>
                      <p class="text-xs text-secondary mb-0">{{ $cliente->codigo }}</p>
                    </div>
                  </div>
                </td>
                <td>
                  <p class="text-xs font-weight-bold mb-0">{{ $cliente->email ?: '—' }}</p>
                  <p class="text-xs text-secondary mb-0">{{ $cliente->telefone ?: '—' }}</p>
                </td>
                <td class="text-center">
                  @php
                    $pct = $cliente->percentual_uso;
                    $barColor = $pct >= 90 ? 'danger' : ($pct >= 60 ? 'warning' : 'success');
                  @endphp
                  <p class="text-xs font-weight-bold mb-0">
                    R$ {{ number_format($cliente->credito_usado, 2, ',', '.') }} /
                    R$ {{ number_format($cliente->limite_credito, 2, ',', '.') }}
                  </p>
                  <div class="progress" style="height:4px;">
                    <div class="progress-bar bg-{{ $barColor }}" style="width:{{ $pct }}%"></div>
                  </div>
                  <p class="text-xxs text-secondary mb-0">{{ $pct }}% usado</p>
                </td>
                <td class="align-middle text-center text-sm">
                  <span class="badge badge-sm bg-gradient-{{ $cliente->statusColor() }}">
                    <i class="fas {{ $cliente->statusIcon() }} me-1"></i>{{ $cliente->statusLabel() }}
                  </span>
                </td>
                <td class="align-middle text-center">
                  <span class="text-secondary text-xs">{{ $cliente->created_at->format('d/m/Y') }}</span>
                </td>
                <td class="align-middle">
                  <a href="{{ route('bank.clientes.show', $cliente->codigo) }}"
                     class="text-primary font-weight-bold text-xs me-3" title="Ver Perfil">
                    <i class="fas fa-eye text-lg"></i>
                  </a>
                  <a href="{{ route('bank.clientes.edit', $cliente->codigo) }}"
                     class="text-secondary font-weight-bold text-xs me-3" title="Editar">
                    <i class="fas fa-pencil-alt text-lg"></i>
                  </a>
                  <a href="{{ route('bank.clientes.transacoes', $cliente->codigo) }}"
                     class="text-info font-weight-bold text-xs me-3" title="Transações">
                    <i class="fas fa-exchange-alt text-lg"></i>
                  </a>
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center py-4">
                  <i class="fas fa-users text-secondary text-3xl mb-3 d-block opacity-5"></i>
                  <p class="text-sm mb-2">Nenhum cliente encontrado.</p>
                  @if($lojaAtual)
                    <a href="{{ route('bank.clientes.create', ['loja_codigo' => $lojaAtual->codigo]) }}"
                       class="btn btn-sm btn-outline-primary">
                      <i class="fas fa-user-plus me-1"></i> Criar primeiro cliente
                    </a>
                  @endif
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        {{-- Paginação --}}
        @if($clientes->hasPages())
        <div class="d-flex justify-content-center mt-3">
          {{ $clientes->links() }}
        </div>
        @endif
      </div>
    </div>

  </div>
</main>
@endsection
