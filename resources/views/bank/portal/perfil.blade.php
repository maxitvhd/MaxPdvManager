@extends('bank.portal.layout')
@section('titulo', 'Meu Perfil')

@section('bank-content')

<div class="mb-4">
  <h2 class="bank-h2">Meu Perfil</h2>
  <p style="color:var(--bank-muted);font-size:0.85rem;">Gerencie seus dados pessoais e documentos</p>
</div>

@if(session('success'))
  <div class="bank-alert bank-alert-success"><i class="fas fa-check-circle"></i>{{ session('success') }}</div>
@endif
@if(session('error'))
  <div class="bank-alert bank-alert-danger"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>
@endif

{{-- Status da conta --}}
<div class="bank-card mb-4" style="display:flex;align-items:center;gap:1rem;">
  <div class="bank-avatar" style="width:52px;height:52px;font-size:1.4rem;border-radius:50%;">
    {{ strtoupper(substr($clienteLogado->nome, 0, 1)) }}
  </div>
  <div>
    <h3 class="bank-h3 mb-0">{{ $clienteLogado->nome }}</h3>
    <p style="color:var(--bank-muted);font-size:0.78rem;margin:0;">{{ $clienteLogado->codigo }}</p>
    <span class="bank-badge bank-badge-{{ $clienteLogado->status === 'ativo' ? 'success' : 'warning' }}" style="margin-top:0.3rem;">
      {{ $clienteLogado->statusLabel() }}
    </span>
  </div>
</div>

{{-- Alterar dados --}}
<div class="bank-card mb-4">
  <h3 class="bank-h3 mb-3"><i class="fas fa-pencil-alt me-2" style="color:#3b82f6;"></i>Alterar Dados</h3>
  <form action="{{ route('banco.perfil.update') }}" method="POST">
    @csrf @method('PUT')
    <div class="bank-input-group">
      <label class="bank-input-label">E-mail</label>
      <input type="email" name="email" class="bank-input" value="{{ $clienteLogado->email }}" placeholder="email@exemplo.com">
    </div>
    <div class="bank-input-group">
      <label class="bank-input-label">Telefone / WhatsApp</label>
      <input type="text" name="telefone" class="bank-input" value="{{ $clienteLogado->telefone }}" placeholder="(11) 99999-9999">
    </div>
    <div class="bank-divider"></div>
    <p class="bank-label mb-2">Alterar PIN (deixe em branco para manter)</p>
    <div class="bank-input-group">
      <label class="bank-input-label">Novo PIN</label>
      <input type="password" name="pin_novo" class="bank-input" placeholder="4 a 8 dígitos" maxlength="8" inputmode="numeric">
    </div>
    <div class="bank-input-group">
      <label class="bank-input-label">Confirmar Novo PIN</label>
      <input type="password" name="pin_confirmation" class="bank-input" placeholder="Repita o PIN" maxlength="8" inputmode="numeric">
    </div>
    <div class="bank-divider"></div>
    <div class="bank-input-group">
      <label class="bank-input-label">PIN Atual (confirmação de segurança) *</label>
      <input type="password" name="pin_atual" class="bank-input" placeholder="PIN atual" maxlength="8" required inputmode="numeric">
    </div>
    <button type="submit" class="bank-btn bank-btn-primary bank-btn-full">
      <i class="fas fa-save me-2"></i>Salvar Alterações
    </button>
  </form>
</div>

{{-- Upload de documentos --}}
<div class="bank-card mb-4">
  <h3 class="bank-h3 mb-1"><i class="fas fa-folder-open me-2" style="color:#f59e0b;"></i>Documentos</h3>
  <p style="color:var(--bank-muted);font-size:0.78rem;margin-bottom:1rem;">Envie seus documentos para aprovação da conta</p>

  {{-- Status dos docs --}}
  @php
    use Illuminate\Support\Facades\Storage;
    $docStatus = [
      'foto_cpf'         => [$clienteLogado->foto_cpf, 'CPF', 'fa-id-card'],
      'foto_habilitacao' => [$clienteLogado->foto_habilitacao, 'CNH / Habilitação', 'fa-car'],
      'foto_comprovante' => [$clienteLogado->foto_comprovante, 'Comprovante', 'fa-home'],
      'foto_perfil'      => [$clienteLogado->foto_perfil, 'Foto de Perfil', 'fa-user-circle'],
    ];
  @endphp
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;margin-bottom:1rem;">
    @foreach($docStatus as $campo => [$path, $label, $icon])
    <div style="display:flex;align-items:center;gap:0.5rem;padding:0.5rem 0.75rem;border-radius:8px;background:rgba(255,255,255,0.03);border:1px solid var(--bank-border);">
      <i class="fas {{ $icon }}" style="color:{{ $path ? '#10b981' : '#94a3b8' }};width:16px;text-align:center;"></i>
      <span style="font-size:0.75rem;flex:1;">{{ $label }}</span>
      @if($path)
        <i class="fas fa-check-circle" style="color:#10b981;font-size:0.75rem;"></i>
      @else
        <i class="fas fa-clock" style="color:#94a3b8;font-size:0.75rem;"></i>
      @endif
    </div>
    @endforeach
  </div>

  <form action="{{ route('banco.documentos') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @foreach([
      ['foto_cpf',         'Foto do CPF (frente)',             'fa-id-card'],
      ['foto_habilitacao', 'Foto da CNH / Habilitação',         'fa-car'],
      ['foto_comprovante', 'Comprovante de Residência',          'fa-home'],
      ['foto_perfil',      'Foto de Perfil (selfie atual)',      'fa-user-circle'],
    ] as [$campo, $label, $icon])
    <div class="bank-input-group">
      <label class="bank-input-label"><i class="fas {{ $icon }} me-1"></i>{{ $label }}</label>
      <input type="file" name="{{ $campo }}" class="bank-input" accept="image/*" capture="environment"
             style="padding:0.5rem;">
    </div>
    @endforeach

    <div class="bank-alert bank-alert-info" style="margin-bottom:1rem;">
      <i class="fas fa-info-circle"></i>
      <span>Após enviar os documentos, aguarde a aprovação da loja para liberar sua conta completa.</span>
    </div>

    <button type="submit" class="bank-btn bank-btn-primary bank-btn-full">
      <i class="fas fa-cloud-upload-alt me-2"></i>Enviar Documentos
    </button>
  </form>
</div>

{{-- Dados do contrato --}}
<div class="bank-card">
  <h3 class="bank-h3 mb-3"><i class="fas fa-info-circle me-2" style="color:#94a3b8;"></i>Dados da Conta</h3>
  <div class="bank-divider" style="margin-top:0;"></div>
  @foreach([
    ['Código', $clienteLogado->codigo],
    ['Usuário', $clienteLogado->usuario ?: '—'],
    ['Limite de Crédito', 'R$ '.number_format($clienteLogado->limite_credito, 2, ',', '.')],
    ['Fechamento Fatura', 'Dia '.$clienteLogado->dia_fechamento.' do mês'],
    ['Cliente desde', $clienteLogado->created_at->format('d/m/Y')],
  ] as [$k, $v])
  <div style="display:flex;justify-content:space-between;padding:0.5rem 0;border-bottom:1px solid var(--bank-border);">
    <span class="bank-label" style="margin:0;">{{ $k }}</span>
    <span class="bank-value">{{ $v }}</span>
  </div>
  @endforeach
</div>

@endsection
