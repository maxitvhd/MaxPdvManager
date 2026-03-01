@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">

    <div class="row mb-3">
      <div class="col-12">
        <nav aria-label="breadcrumb">
          <ol class="breadcrumb bg-transparent mb-0 pb-0 pt-0">
            <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.index') }}">MaxBank</a></li>
            <li class="breadcrumb-item text-sm active">Novo Cliente</li>
          </ol>
        </nav>
        <h4 class="mb-1 text-dark"><i class="fas fa-user-plus me-2 text-primary"></i>Criar Cliente & Carteira</h4>
        <p class="text-sm text-muted mb-0">Preencha os dados e defina o limite de crédito da carteira</p>
      </div>
    </div>

    @if(session('error'))
      <div class="alert alert-danger text-white">{{ session('error') }}</div>
    @endif

    <form action="{{ route('bank.clientes.store') }}" method="POST">
      @csrf
      <div class="row">

        {{-- Dados Pessoais --}}
        <div class="col-lg-7">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6><i class="fas fa-user me-2 text-primary"></i>Dados do Cliente</h6>
            </div>
            <div class="card-body">

              {{-- Loja --}}
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Loja *</label>
                <select name="loja_codigo" class="form-control" required>
                  @foreach($lojas as $lj)
                    <option value="{{ $lj->codigo }}" {{ $lojaAtual && $lojaAtual->id === $lj->id ? 'selected' : '' }}>
                      {{ $lj->nome }}
                    </option>
                  @endforeach
                </select>
                @error('loja_codigo') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
              </div>

              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Nome Completo *</label>
                  <input type="text" name="nome" class="form-control @error('nome') is-invalid @enderror"
                         value="{{ old('nome') }}" required placeholder="Ex.: João da Silva">
                  @error('nome') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">CPF</label>
                  <input type="text" name="cpf" class="form-control" value="{{ old('cpf') }}"
                         placeholder="000.000.000-00" id="cpfInput">
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">E-mail</label>
                  <input type="email" name="email" class="form-control" value="{{ old('email') }}"
                         placeholder="email@exemplo.com">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Telefone / WhatsApp</label>
                  <input type="text" name="telefone" class="form-control" value="{{ old('telefone') }}"
                         placeholder="(11) 99999-9999">
                </div>
              </div>

              {{-- Endereço --}}
              <hr class="horizontal dark">
              <p class="text-xs font-weight-bold text-uppercase text-secondary mb-2">Endereço</p>

              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Logradouro</label>
                <input type="text" name="endereco" class="form-control" value="{{ old('endereco') }}"
                       placeholder="Rua, Av., número...">
              </div>
              <div class="row">
                <div class="col-md-5 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Bairro</label>
                  <input type="text" name="bairro" class="form-control" value="{{ old('bairro') }}">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Cidade</label>
                  <input type="text" name="cidade" class="form-control" value="{{ old('cidade') }}">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">CEP</label>
                  <input type="text" name="cep" class="form-control" value="{{ old('cep') }}"
                         placeholder="00000-000" id="cepInput">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Estado</label>
                <select name="estado" class="form-control">
                  <option value="">Selecione...</option>
                  @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                    <option value="{{ $uf }}" {{ old('estado') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
        </div>

        {{-- Carteira de Crédito --}}
        <div class="col-lg-5">
          <div class="card mb-4">
            <div class="card-header pb-0">
              <h6><i class="fas fa-wallet me-2 text-success"></i>Carteira de Crédito</h6>
            </div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Limite de Crédito (R$) *</label>
                <div class="input-group">
                  <span class="input-group-text">R$</span>
                  <input type="number" name="limite_credito" class="form-control @error('limite_credito') is-invalid @enderror"
                         value="{{ old('limite_credito', 0) }}" min="0" step="0.01" required>
                </div>
                @error('limite_credito') <div class="text-danger text-xs mt-1">{{ $message }}</div> @enderror
              </div>

              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Dia de Fechamento da Fatura</label>
                <input type="number" name="dia_fechamento" class="form-control"
                       value="{{ old('dia_fechamento', 1) }}" min="1" max="28" required>
                <small class="text-muted">Dia do mês em que a fatura fecha (1-28)</small>
              </div>

              <div class="alert alert-info text-sm px-3 py-2 mt-4">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Próximos passos:</strong><br>
                Após criar o cliente, um link de ativação será gerado. O cliente deverá acessá-lo para configurar o <strong>reconhecimento facial</strong> e o <strong>PIN de acesso</strong>.
              </div>

              <div class="alert alert-warning text-sm px-3 py-2">
                <i class="fas fa-shield-alt me-2"></i>
                A conta só ficará <strong>ativa</strong> após aprovação do gerente, verificação dos documentos e configuração do PIN/facial.
              </div>
            </div>
          </div>

          <div class="d-flex gap-2">
            <a href="{{ route('bank.clientes.index') }}" class="btn btn-outline-secondary w-50">
              <i class="fas fa-arrow-left me-1"></i> Cancelar
            </a>
            <button type="submit" class="btn bg-gradient-primary w-50">
              <i class="fas fa-save me-1"></i> Criar Cliente
            </button>
          </div>
        </div>

      </div>
    </form>
  </div>
</main>
@endsection

@push('scripts')
<script>
  // Máscara CPF
  document.getElementById('cpfInput')?.addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'');
    v = v.replace(/(\d{3})(\d)/,'$1.$2');
    v = v.replace(/(\d{3})(\d)/,'$1.$2');
    v = v.replace(/(\d{3})(\d{1,2})$/,'$1-$2');
    this.value = v;
  });
  // Máscara CEP
  document.getElementById('cepInput')?.addEventListener('input', function() {
    let v = this.value.replace(/\D/g,'').substring(0,8);
    v = v.replace(/^(\d{5})(\d)/,'$1-$2');
    this.value = v;
  });
</script>
@endpush
