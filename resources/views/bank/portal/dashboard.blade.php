@extends('bank.portal.layout')
@section('titulo', 'Minha Conta')

@section('bank-content')
<div class="fade-up">

  {{-- Saudação --}}
  <div class="mb-4">
    <p class="bank-label mb-1">Bom dia,</p>
    <h2 class="bank-h2">{{ explode(' ', $clienteLogado->nome)[0] }} 👋</h2>
    <p style="color:var(--bank-muted);font-size:0.82rem;">{{ $clienteLogado->codigo }}</p>
  </div>

  {{-- Card Saldo Principal --}}
  <div class="bank-card bank-card-gradient fade-up fade-up-1 mb-4">
    <div style="display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:1.5rem;">
      <div>
        <p class="bank-label mb-1">Saldo Disponível</p>
        <h1 class="bank-h1" style="font-size:2.2rem;">R$ {{ number_format($clienteLogado->saldo_disponivel, 2, ',', '.') }}</h1>
      </div>
      @php
        $st = $clienteLogado->status;
        $class = in_array($st,['pag_atrasado','bloqueado','cobranca','juridico']) ? 'bank-badge-danger' :
                 ($st === 'ativo' ? 'bank-badge-success' : 'bank-badge-warning');
      @endphp
      <span class="bank-badge {{ $class }}">
        <i class="fas {{ $clienteLogado->statusIcon() }}"></i>{{ $clienteLogado->statusLabel() }}
      </span>
    </div>

    {{-- Barra de crédito --}}
    <div style="margin-bottom:0.5rem;">
      <div style="display:flex;justify-content:space-between;margin-bottom:0.3rem;">
        <span class="bank-label">Limite Utilizado</span>
        <span style="font-size:0.75rem;color:var(--bank-muted);">{{ $clienteLogado->percentual_uso }}%</span>
      </div>
      <div class="bank-progress">
        @php $pct = $clienteLogado->percentual_uso; @endphp
        <div class="bank-progress-bar" style="width:{{ $pct }}%; background:{{ $pct >= 90 ? 'linear-gradient(90deg,#ef4444,#dc2626)' : ($pct >= 60 ? 'linear-gradient(90deg,#f59e0b,#d97706)' : 'linear-gradient(90deg,#3b82f6,#8b5cf6)') }}"></div>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:1fr 1fr;gap:1rem;margin-top:1rem;">
      <div>
        <p class="bank-label mb-0">Limite Total</p>
        <p style="font-size:0.95rem;font-weight:600;color:var(--bank-text);">R$ {{ number_format($clienteLogado->limite_credito, 2, ',', '.') }}</p>
      </div>
      <div>
        <p class="bank-label mb-0">Débito em Aberto</p>
        <p style="font-size:0.95rem;font-weight:600;color:#ef4444;">R$ {{ number_format($clienteLogado->credito_usado, 2, ',', '.') }}</p>
      </div>
    </div>
  </div>

  {{-- Alerta de atraso --}}
  @if($clienteLogado->status === 'pag_atrasado')
  <div class="bank-alert bank-alert-danger fade-up fade-up-2">
    <i class="fas fa-exclamation-triangle"></i>
    <div>
      <strong>Pagamento em atraso!</strong><br>
      Regularize seu débito para evitar restrições na conta.
      <a href="{{ route('banco.faturas') }}" style="color:#ef4444;font-weight:700;margin-left:0.5rem;">Ver Fatura →</a>
    </div>
  </div>
  @endif

  {{-- Ações rápidas --}}
  <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:0.75rem;margin-bottom:1.5rem;" class="fade-up fade-up-2">
    <a href="{{ route('banco.faturas') }}" class="bank-card bank-card-sm" style="text-decoration:none;text-align:center;">
      <i class="fas fa-file-invoice-dollar" style="font-size:1.5rem;color:#3b82f6;margin-bottom:0.4rem;display:block;"></i>
      <span style="font-size:0.82rem;font-weight:600;color:var(--bank-text);">Minhas Faturas</span>
    </a>
    <a href="{{ route('banco.perfil') }}" class="bank-card bank-card-sm" style="text-decoration:none;text-align:center;">
      <i class="fas fa-user-circle" style="font-size:1.5rem;color:#10b981;margin-bottom:0.4rem;display:block;"></i>
      <span style="font-size:0.82rem;font-weight:600;color:var(--bank-text);">Meu Perfil</span>
    </a>
  </div>

  {{-- Extrato recente --}}
  <div class="bank-card fade-up fade-up-3">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.75rem;">
      <h3 class="bank-h3">Extrato Recente</h3>
      <a href="{{ route('banco.faturas') }}" style="font-size:0.8rem;color:var(--bank-primary);text-decoration:none;font-weight:600;">Ver Tudo →</a>
    </div>

    @if($transacoesRecentes->isEmpty())
      <p style="text-align:center;color:var(--bank-muted);padding:1.5rem 0;font-size:0.85rem;">
        <i class="fas fa-receipt d-block mb-2" style="font-size:1.5rem;opacity:0.4;"></i>
        Nenhuma movimentação ainda.
      </p>
    @else
      @foreach($transacoesRecentes as $tx)
        @php $isCredit = in_array($tx->tipo, ['credito','pagamento','estorno']); @endphp
        <div class="bank-tx-item">
          <div class="bank-tx-icon {{ $isCredit ? 'credit' : 'debit' }}">
            <i class="fas {{ $isCredit ? 'fa-arrow-down' : 'fa-arrow-up' }}"></i>
          </div>
          <div class="bank-tx-info">
            <div class="bank-tx-name">{{ $tx->tipoLabel() }}</div>
            <div class="bank-tx-date">{{ $tx->data_hora ? $tx->data_hora->format('d/m/Y H:i') : '—' }}</div>
          </div>
          <div class="bank-tx-amount {{ $isCredit ? 'credit' : 'debit' }}">
            {{ $isCredit ? '+' : '-' }} R$ {{ number_format($tx->valor, 2, ',', '.') }}
          </div>
        </div>
      @endforeach
    @endif
  </div>

  {{-- Saldo devedor e pagar --}}
  @if($saldoDevedor > 0)
  <div class="bank-card fade-up fade-up-4" style="border-color:rgba(239,68,68,0.3);">
    <div style="display:flex;justify-content:space-between;align-items:center;">
      <div>
        <p class="bank-label mb-0">Saldo Devedor Total</p>
        <h3 class="bank-h3" style="color:#ef4444;">R$ {{ number_format($saldoDevedor, 2, ',', '.') }}</h3>
      </div>
      <a href="{{ route('banco.faturas') }}" class="bank-btn bank-btn-danger bank-btn-sm"
         style="background:linear-gradient(135deg,#ef4444,#dc2626);color:#fff;box-shadow:0 4px 12px rgba(239,68,68,0.3);">
        <i class="fas fa-hand-holding-usd me-1"></i>Pagar Agora
      </a>
    </div>
  </div>
  @endif

</div>
@endsection
