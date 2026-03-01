@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">

    <div class="row mb-3">
      <div class="col-12 d-flex justify-content-between align-items-start">
        <div>
          <nav><ol class="breadcrumb bg-transparent mb-1">
            <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.index') }}">MaxBank</a></li>
            <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.show', $cliente->codigo) }}">{{ $cliente->nome }}</a></li>
            <li class="breadcrumb-item text-sm active">Transações</li>
          </ol></nav>
          <h4 class="mb-0"><i class="fas fa-exchange-alt me-2 text-info"></i>Histórico de Transações</h4>
          <p class="text-sm text-muted mb-0">{{ $cliente->nome }} — {{ $cliente->codigo }}</p>
        </div>
        <a href="{{ route('bank.clientes.show', $cliente->codigo) }}" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left me-1"></i> Voltar ao Perfil
        </a>
      </div>
    </div>

    {{-- Resumo financeiro --}}
    @php
      $totalDebito  = $cliente->transacoes()->where('tipo','debito')->sum('valor');
      $totalCredito = $cliente->transacoes()->whereIn('tipo',['pagamento','estorno','credito'])->sum('valor');
    @endphp
    <div class="row mb-4">
      <div class="col-md-3">
        <div class="card text-center py-3">
          <p class="text-xs text-muted mb-1">Total Debitado</p>
          <h5 class="text-danger mb-0">R$ {{ number_format($totalDebito, 2, ',', '.') }}</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center py-3">
          <p class="text-xs text-muted mb-1">Total Pago/Creditado</p>
          <h5 class="text-success mb-0">R$ {{ number_format($totalCredito, 2, ',', '.') }}</h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center py-3">
          <p class="text-xs text-muted mb-1">Saldo Devedor</p>
          <h5 class="{{ $cliente->credito_usado > 0 ? 'text-danger' : 'text-success' }} mb-0">
            R$ {{ number_format($cliente->credito_usado, 2, ',', '.') }}
          </h5>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card text-center py-3">
          <p class="text-xs text-muted mb-1">Limite Disponível</p>
          <h5 class="text-primary mb-0">R$ {{ number_format($cliente->saldo_disponivel, 2, ',', '.') }}</h5>
        </div>
      </div>
    </div>

    {{-- Filtros --}}
    <div class="card mb-3">
      <div class="card-body py-2">
        <form method="GET" class="row g-2 align-items-end">
          <div class="col-md-3">
            <label class="form-label text-xs font-weight-bold text-uppercase">Tipo</label>
            <select name="tipo" class="form-control form-control-sm" onchange="this.form.submit()">
              <option value="">Todos</option>
              <option value="debito" {{ request('tipo') === 'debito' ? 'selected' : '' }}>Débito (Compra)</option>
              <option value="pagamento" {{ request('tipo') === 'pagamento' ? 'selected' : '' }}>Pagamento</option>
              <option value="credito" {{ request('tipo') === 'credito' ? 'selected' : '' }}>Crédito</option>
              <option value="ajuste" {{ request('tipo') === 'ajuste' ? 'selected' : '' }}>Ajuste</option>
              <option value="estorno" {{ request('tipo') === 'estorno' ? 'selected' : '' }}>Estorno</option>
            </select>
          </div>
        </form>
      </div>
    </div>

    {{-- Tabela de transações --}}
    <div class="card">
      <div class="card-body px-0 pb-2 pt-0">
        <div class="table-responsive p-0">
          <table class="table align-items-center mb-0">
            <thead>
              <tr>
                <th class="text-uppercase text-secondary text-xxs opacity-7">Tipo</th>
                <th class="text-uppercase text-secondary text-xxs opacity-7">Valor</th>
                <th class="text-uppercase text-secondary text-xxs opacity-7">Venda</th>
                <th class="text-uppercase text-secondary text-xxs opacity-7">Descrição / Operador</th>
                <th class="text-uppercase text-secondary text-xxs opacity-7">Data/Hora</th>
              </tr>
            </thead>
            <tbody>
              @forelse($transacoes as $tx)
              <tr>
                <td>
                  <span class="badge bg-gradient-{{ $tx->tipoColor() }}">
                    <i class="fas {{ $tx->tipoIcon() }} me-1"></i>{{ $tx->tipoLabel() }}
                  </span>
                </td>
                <td class="font-weight-bold {{ in_array($tx->tipo, ['credito','pagamento','estorno']) ? 'text-success' : 'text-danger' }}">
                  {{ in_array($tx->tipo, ['credito','pagamento','estorno']) ? '+' : '-' }}
                  R$ {{ number_format($tx->valor, 2, ',', '.') }}
                </td>
                <td class="text-xs text-secondary">{{ $tx->venda_codigo ?: '—' }}</td>
                <td class="text-xs">
                  {{ $tx->descricao ?? '—' }}<br>
                  <span class="text-muted">{{ $tx->usuario_codigo ?: '—' }}</span>
                </td>
                <td class="text-xs text-secondary">{{ $tx->data_hora ? $tx->data_hora->format('d/m/Y H:i') : '—' }}</td>
              </tr>
              @empty
              <tr>
                <td colspan="5" class="text-center py-4">
                  <i class="fas fa-exchange-alt text-secondary text-xl d-block mb-2 opacity-5"></i>
                  Nenhuma transação registrada.
                </td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>
        @if($transacoes->hasPages())
          <div class="d-flex justify-content-center mt-3">{{ $transacoes->links() }}</div>
        @endif
      </div>
    </div>

  </div>
</main>
@endsection
