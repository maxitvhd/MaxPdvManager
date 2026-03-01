@extends('bank.portal.layout')
@section('titulo', 'Minhas Faturas')

@section('bank-content')

<div class="mb-4">
  <h2 class="bank-h2">Minhas Faturas</h2>
  <p style="color:var(--bank-muted);font-size:0.85rem;">Histórico de movimentações da sua conta</p>
</div>

{{-- Resumo financeiro --}}
@php
  $totalAberto = $clienteLogado->transacoes()->where('tipo','debito')->sum('valor') 
               - $clienteLogado->transacoes()->whereIn('tipo',['pagamento','estorno'])->sum('valor');
  $totalAberto = max(0, $totalAberto);
@endphp

<div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem;margin-bottom:1.5rem;">
  <div class="bank-card" style="border-color:rgba(239,68,68,0.3);text-align:center;">
    <p class="bank-label mb-1">Em Aberto</p>
    <h3 style="color:#ef4444;font-weight:700;margin:0;">R$ {{ number_format($totalAberto, 2, ',', '.') }}</h3>
    @if($diasAtraso > 0)
      <span class="bank-badge bank-badge-danger" style="margin-top:0.4rem;">
        <i class="fas fa-clock"></i>{{ $diasAtraso }} dia{{ $diasAtraso > 1 ? 's' : '' }} atraso
      </span>
    @endif
  </div>
  <div class="bank-card" style="border-color:rgba(16,185,129,0.3);text-align:center;">
    <p class="bank-label mb-1">Disponível</p>
    <h3 style="color:#10b981;font-weight:700;margin:0;">R$ {{ number_format($clienteLogado->saldo_disponivel, 2, ',', '.') }}</h3>
    <span class="bank-badge bank-badge-success" style="margin-top:0.4rem;">
      <i class="fas fa-check"></i>Limite
    </span>
  </div>
</div>

{{-- Abas --}}
<div class="bank-tabs mb-3">
  <a href="{{ route('banco.faturas', ['aba' => 'abertas']) }}"
     class="bank-tab {{ $aba === 'abertas' ? 'active' : '' }}">
    <i class="fas fa-exclamation-circle me-1"></i>Em Aberto
  </a>
  <a href="{{ route('banco.faturas', ['aba' => 'pagas']) }}"
     class="bank-tab {{ $aba === 'pagas' ? 'active' : '' }}">
    <i class="fas fa-check-circle me-1"></i>Pagas
  </a>
</div>

{{-- Pagar saldo (só na aba aberta) --}}
@if($aba === 'abertas' && $totalAberto > 0)
<div class="bank-card mb-3" style="border-color:rgba(59,130,246,0.3);">
  <h3 class="bank-h3 mb-3"><i class="fas fa-hand-holding-usd me-2" style="color:#3b82f6;"></i>Pagar Saldo</h3>
  <form action="{{ route('banco.pagar') }}" method="POST" id="formPagar">
    @csrf
    <div class="bank-input-group">
      <label class="bank-input-label">Valor a Pagar (R$)</label>
      <input type="number" name="valor" class="bank-input" step="0.01" min="0.01"
             max="{{ $totalAberto }}" value="{{ number_format($totalAberto, 2, '.', '') }}" required>
    </div>
    <div class="bank-input-group">
      <label class="bank-input-label">Confirme seu PIN</label>
      <input type="password" name="pin_confirm" class="bank-input" placeholder="••••" maxlength="8" required>
    </div>
    @if(session('error'))
      <div class="bank-alert bank-alert-danger"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>
    @endif
    <button type="submit" class="bank-btn bank-btn-success bank-btn-full"
            onclick="return confirm('Confirma o pagamento?')">
      <i class="fas fa-check-circle me-2"></i>Confirmar Pagamento
    </button>
  </form>
</div>
@endif

{{-- Lista de transações --}}
<div class="bank-card">
  @if($transacoes->isEmpty())
    <div style="text-align:center;padding:2rem 0;color:var(--bank-muted);">
      <i class="fas {{ $aba === 'pagas' ? 'fa-check-circle' : 'fa-receipt' }}" style="font-size:2rem;opacity:0.4;margin-bottom:0.5rem;display:block;"></i>
      <p style="font-size:0.85rem;">
        {{ $aba === 'pagas' ? 'Nenhum pagamento registrado.' : 'Nenhum débito em aberto. 🎉' }}
      </p>
    </div>
  @else
    @foreach($transacoes as $tx)
      @php $isCredit = in_array($tx->tipo, ['credito','pagamento','estorno']); @endphp
      <div class="bank-tx-item">
        <div class="bank-tx-icon {{ $isCredit ? 'credit' : 'debit' }}">
          <i class="fas {{ $isCredit ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
        </div>
        <div class="bank-tx-info">
          <div class="bank-tx-name">{{ $tx->tipoLabel() }}</div>
          @if($tx->descricao ?? null)
            <div class="bank-tx-date" style="font-size:0.7rem;">{{ $tx->descricao }}</div>
          @endif
          <div class="bank-tx-date">{{ $tx->data_hora ? $tx->data_hora->format('d/m/Y H:i') : '—' }}</div>
        </div>
        <div class="bank-tx-amount {{ $isCredit ? 'credit' : 'debit' }}">
          {{ $isCredit ? '+' : '-' }} R$ {{ number_format($tx->valor, 2, ',', '.') }}
        </div>
      </div>
    @endforeach

    @if($transacoes->hasPages())
      <div style="display:flex;justify-content:center;margin-top:1rem;gap:0.5rem;">
        @if($transacoes->onFirstPage())
          <span class="bank-btn bank-btn-outline bank-btn-sm" style="opacity:0.4;">← Anterior</span>
        @else
          <a href="{{ $transacoes->previousPageUrl() }}" class="bank-btn bank-btn-outline bank-btn-sm">← Anterior</a>
        @endif
        <span style="padding:0.4rem 0.85rem;font-size:0.78rem;color:var(--bank-muted);">
          {{ $transacoes->currentPage() }}/{{ $transacoes->lastPage() }}
        </span>
        @if($transacoes->hasMorePages())
          <a href="{{ $transacoes->nextPageUrl() }}" class="bank-btn bank-btn-outline bank-btn-sm">Próxima →</a>
        @else
          <span class="bank-btn bank-btn-outline bank-btn-sm" style="opacity:0.4;">Próxima →</span>
        @endif
      </div>
    @endif
  @endif
</div>

@endsection
