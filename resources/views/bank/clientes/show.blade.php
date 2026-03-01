@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">

    {{-- Breadcrumb --}}
    <div class="row mb-3">
      <div class="col-12 d-flex justify-content-between align-items-start">
        <div>
          <nav><ol class="breadcrumb bg-transparent mb-1 pb-0 pt-0">
            <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.index') }}">MaxBank</a></li>
            <li class="breadcrumb-item text-sm active">{{ $cliente->nome }}</li>
          </ol></nav>
          <h5 class="mb-0 text-dark">Conta de {{ $cliente->nome }}</h5>
          <p class="text-xs text-muted mb-0">Código: <strong>{{ $cliente->codigo }}</strong></p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <a href="{{ route('bank.clientes.edit', $cliente->codigo) }}" class="btn btn-outline-secondary btn-sm">
            <i class="fas fa-pencil-alt me-1"></i> Editar
          </a>
          <a href="{{ route('bank.clientes.transacoes', $cliente->codigo) }}" class="btn btn-outline-info btn-sm">
            <i class="fas fa-exchange-alt me-1"></i> Transações
          </a>
          <a href="{{ route('bank.clientes.documentos', $cliente->codigo) }}" class="btn btn-outline-warning btn-sm">
            <i class="fas fa-folder-open me-1"></i> Documentos
          </a>
        </div>
      </div>
    </div>

    {{-- Alertas --}}
    @if(session('success'))
      <div class="alert alert-success text-white alert-dismissible fade show">
        <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
        {{-- Exibir link de ativação se disponível --}}
        @if(session('link_ativacao'))
          <div class="mt-2 p-2 bg-white rounded">
            <strong class="text-dark d-block mb-1"><i class="fas fa-link me-1"></i>Link de Ativação (válido 72h):</strong>
            <div class="input-group input-group-sm">
              <input type="text" class="form-control text-dark" id="linkAtivacao" value="{{ session('link_ativacao') }}" readonly>
              <button class="btn btn-primary btn-sm" onclick="copiarLink()"><i class="fas fa-copy"></i></button>
            </div>
            <small class="text-muted">Envie este link para o cliente configurar o facial e PIN</small>
          </div>
        @endif
        <button type="button" class="btn-close text-white" data-bs-dismiss="alert"></button>
      </div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger text-white alert-dismissible fade show">
        <i class="fas fa-exclamation-triangle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close text-white" data-bs-dismiss="alert"></button>
      </div>
    @endif

    {{-- Link de ativação se salvo no perfil --}}
    @if($linkAtivacao && $cliente->link_expires_at && $cliente->link_expires_at->isFuture())
      <div class="alert alert-warning">
        <i class="fas fa-hourglass-half me-2"></i>
        <strong>Link de ativação pendente</strong> (expira em {{ $cliente->link_expires_at->diffForHumans() }})
        <div class="input-group input-group-sm mt-2" style="max-width:500px;">
          <input type="text" class="form-control" id="linkAtivacaoSalvo" value="{{ $linkAtivacao }}" readonly>
          <button class="btn btn-warning btn-sm" onclick="navigator.clipboard.writeText(document.getElementById('linkAtivacaoSalvo').value)">
            <i class="fas fa-copy"></i>
          </button>
        </div>
      </div>
    @endif

    <div class="row">

      {{-- Card Saldo + Status --}}
      <div class="col-lg-4">

        {{-- Status e ações rápidas --}}
        <div class="card mb-4">
          <div class="card-body text-center">
            <div class="avatar avatar-xl bg-gradient-primary rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3" style="width:80px;height:80px;">
              <span class="text-white font-weight-bold" style="font-size:2rem;">{{ strtoupper(substr($cliente->nome, 0, 1)) }}</span>
            </div>
            <h5 class="mb-1">{{ $cliente->nome }}</h5>
            <p class="text-sm text-muted mb-2">@{{ $cliente->usuario }}</p>
            <span class="badge badge-lg bg-gradient-{{ $cliente->statusColor() }} mb-3">
              <i class="fas {{ $cliente->statusIcon() }} me-1"></i>{{ $cliente->statusLabel() }}
            </span>

            {{-- Aprovar conta --}}
            @if(in_array($cliente->status, ['processando', 'esp_documentos', 'esp_dados']) && $podeAlterarSensiveis)
              <form action="{{ route('bank.clientes.aprovar', $cliente->codigo) }}" method="POST" class="mb-2">
                @csrf
                <button class="btn bg-gradient-success btn-sm w-100">
                  <i class="fas fa-check-circle me-1"></i> Aprovar Conta
                </button>
              </form>
            @endif

            {{-- Alterar status --}}
            @if($podeAlterarSensiveis)
            <form action="{{ route('bank.clientes.status', $cliente->codigo) }}" method="POST" class="d-flex gap-1" id="formStatus">
              @csrf
              <select name="status" class="form-control form-control-sm">
                @foreach(['esp_facial'=>'Aguard. Facial','esp_documentos'=>'Aguard. Docs','processando'=>'Em Análise','ativo'=>'Ativo','bloqueado'=>'Bloqueado','pag_atrasado'=>'Pag. Atrasado','cobranca'=>'Cobrança','juridico'=>'Jurídico','cancelado'=>'Cancelado'] as $val => $lbl)
                  <option value="{{ $val }}" {{ $cliente->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                @endforeach
              </select>
              <button type="submit" class="btn btn-outline-primary btn-sm">OK</button>
            </form>
            @endif

            {{-- Reenviar link --}}
            <form action="{{ route('bank.clientes.reenviar_link', $cliente->codigo) }}" method="POST" class="mt-2">
              @csrf
              <button class="btn btn-outline-warning btn-sm w-100">
                <i class="fas fa-link me-1"></i> Reenviar Link de Ativação
              </button>
            </form>
          </div>
        </div>

        {{-- Card Carteira --}}
        <div class="card mb-4">
          <div class="card-header pb-0">
            <h6 class="mb-0"><i class="fas fa-wallet me-2 text-success"></i>Carteira de Crédito</h6>
          </div>
          <div class="card-body">
            <div class="row text-center">
              <div class="col-4">
                <p class="text-xs text-muted mb-0">Limite</p>
                <h6 class="mb-0 text-success">R$ {{ number_format($cliente->limite_credito, 2, ',', '.') }}</h6>
              </div>
              <div class="col-4">
                <p class="text-xs text-muted mb-0">Usado</p>
                <h6 class="mb-0 text-danger">R$ {{ number_format($cliente->credito_usado, 2, ',', '.') }}</h6>
              </div>
              <div class="col-4">
                <p class="text-xs text-muted mb-0">Disponível</p>
                <h6 class="mb-0 text-primary">R$ {{ number_format($cliente->saldo_disponivel, 2, ',', '.') }}</h6>
              </div>
            </div>
            @php $pct = $cliente->percentual_uso; @endphp
            <div class="progress mt-3" style="height:8px;">
              <div class="progress-bar bg-{{ $pct >= 90 ? 'danger' : ($pct >= 60 ? 'warning' : 'success') }}" style="width:{{ $pct }}%"></div>
            </div>
            <p class="text-xs text-muted mt-1 mb-0 text-end">{{ $pct }}% do limite utilizado</p>

            {{-- Adicionar crédito --}}
            <hr class="horizontal dark">
            <form action="{{ route('bank.clientes.credito', $cliente->codigo) }}" method="POST">
              @csrf
              <div class="mb-2">
                <label class="form-label text-xs font-weight-bold text-uppercase">Tipo de Operação</label>
                <select name="tipo" class="form-control form-control-sm">
                  <option value="pagamento">Registrar Pagamento</option>
                  <option value="credito">Adicionar Crédito (Limite)</option>
                  <option value="ajuste">Ajuste Manual</option>
                </select>
              </div>
              <div class="mb-2">
                <label class="form-label text-xs font-weight-bold text-uppercase">Valor (R$)</label>
                <input type="number" name="valor" class="form-control form-control-sm" min="0.01" step="0.01" required>
              </div>
              <div class="mb-2">
                <label class="form-label text-xs font-weight-bold text-uppercase">Descrição</label>
                <input type="text" name="descricao" class="form-control form-control-sm" placeholder="Opcional...">
              </div>
              <button class="btn bg-gradient-success btn-sm w-100">
                <i class="fas fa-plus-circle me-1"></i> Registrar Operação
              </button>
            </form>
          </div>
        </div>

      </div>

      {{-- Dados do cliente + Transações recentes --}}
      <div class="col-lg-8">

        {{-- Dados pessoais --}}
        <div class="card mb-4">
          <div class="card-header pb-0 d-flex justify-content-between">
            <h6 class="mb-0"><i class="fas fa-id-card me-2 text-primary"></i>Dados Pessoais</h6>
            <a href="{{ route('bank.clientes.edit', $cliente->codigo) }}" class="btn btn-sm btn-outline-secondary">
              <i class="fas fa-pencil-alt me-1"></i> Editar
            </a>
          </div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-6">
                <table class="table table-sm table-borderless">
                  <tr><td class="text-xs text-muted fw-bold">E-mail:</td><td class="text-sm">{{ $cliente->email ?: '—' }}</td></tr>
                  <tr><td class="text-xs text-muted fw-bold">Telefone:</td><td class="text-sm">{{ $cliente->telefone ?: '—' }}</td></tr>
                  <tr><td class="text-xs text-muted fw-bold">Usuário:</td><td class="text-sm">{{ $cliente->usuario ?: '—' }}</td></tr>
                  <tr><td class="text-xs text-muted fw-bold">CPF:</td><td class="text-sm">{{ $cliente->cpf ?: '—' }}</td></tr>
                </table>
              </div>
              <div class="col-md-6">
                <table class="table table-sm table-borderless">
                  <tr><td class="text-xs text-muted fw-bold">Endereço:</td><td class="text-sm">{{ $cliente->endereco ?: '—' }}</td></tr>
                  <tr><td class="text-xs text-muted fw-bold">Bairro:</td><td class="text-sm">{{ $cliente->bairro ?: '—' }}</td></tr>
                  <tr><td class="text-xs text-muted fw-bold">Cidade/UF:</td><td class="text-sm">{{ $cliente->cidade ? $cliente->cidade.'/'.($cliente->estado ?: '') : '—' }}</td></tr>
                  <tr><td class="text-xs text-muted fw-bold">CEP:</td><td class="text-sm">{{ $cliente->cep ?: '—' }}</td></tr>
                </table>
              </div>
            </div>
            <div class="row mt-2">
              <div class="col-md-4">
                <p class="text-xs text-muted mb-0 fw-bold">Loja</p>
                <p class="text-sm mb-0">{{ optional($cliente->loja)->nome }}</p>
              </div>
              <div class="col-md-4">
                <p class="text-xs text-muted mb-0 fw-bold">Fec. Fatura</p>
                <p class="text-sm mb-0">Dia {{ $cliente->dia_fechamento }}</p>
              </div>
              <div class="col-md-4">
                <p class="text-xs text-muted mb-0 fw-bold">Cadastrado em</p>
                <p class="text-sm mb-0">{{ $cliente->created_at->format('d/m/Y \à\s H:i') }}</p>
              </div>
            </div>
          </div>
        </div>

        {{-- Transações recentes --}}
        <div class="card">
          <div class="card-header pb-0 d-flex justify-content-between">
            <h6 class="mb-0"><i class="fas fa-exchange-alt me-2 text-info"></i>Transações Recentes</h6>
            <a href="{{ route('bank.clientes.transacoes', $cliente->codigo) }}" class="btn btn-sm btn-outline-info">Ver Todas</a>
          </div>
          <div class="card-body px-0 pb-2 pt-0">
            @if($transacoesRecentes->isEmpty())
              <p class="text-sm text-center text-muted py-3">Sem transações ainda.</p>
            @else
            <div class="table-responsive">
              <table class="table table-sm align-items-center mb-0">
                <thead>
                  <tr>
                    <th class="text-uppercase text-secondary text-xxs opacity-7">Tipo</th>
                    <th class="text-uppercase text-secondary text-xxs opacity-7">Valor</th>
                    <th class="text-uppercase text-secondary text-xxs opacity-7">Data</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($transacoesRecentes as $tx)
                  <tr>
                    <td class="text-sm">
                      <i class="fas {{ $tx->tipoIcon() }} me-1"></i>
                      {{ $tx->tipoLabel() }}
                    </td>
                    <td class="text-sm font-weight-bold {{ in_array($tx->tipo, ['credito','pagamento','estorno']) ? 'text-success' : 'text-danger' }}">
                      {{ in_array($tx->tipo, ['credito','pagamento','estorno']) ? '+' : '-' }}
                      R$ {{ number_format($tx->valor, 2, ',', '.') }}
                    </td>
                    <td class="text-xs text-secondary">{{ $tx->data_hora->format('d/m/Y H:i') }}</td>
                  </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
            @endif
          </div>
        </div>

      </div>
    </div>

  </div>
</main>
@endsection

@push('scripts')
<script>
  function copiarLink() {
    const el = document.getElementById('linkAtivacao');
    el.select();
    navigator.clipboard.writeText(el.value).then(() => {
      Swal.fire({ icon: 'success', title: 'Copiado!', text: 'Link copiado para a área de transferência.', timer: 1500, showConfirmButton: false });
    });
  }
</script>
@endpush
