@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">

    <div class="row mb-3">
      <div class="col-12">
        <nav><ol class="breadcrumb bg-transparent mb-1 pb-0 pt-0">
          <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.index') }}">MaxBank</a></li>
          <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.show', $cliente->codigo) }}">{{ $cliente->nome }}</a></li>
          <li class="breadcrumb-item text-sm active">Editar</li>
        </ol></nav>
        <h4 class="mb-0"><i class="fas fa-user-edit me-2 text-primary"></i>Editar Cliente</h4>
      </div>
    </div>

    @if(session('error'))<div class="alert alert-danger text-white">{{ session('error') }}</div>@endif

    @if(!$podeAlterarSensiveis)
    <div class="alert alert-info text-sm mb-4">
      <i class="fas fa-info-circle me-2"></i>
      Você pode atualizar <strong>nome, e-mail e telefone</strong>. Dados sensíveis (limite, status, endereço, CPF) requerem permissão de gerente.
    </div>
    @endif

    <form action="{{ route('bank.clientes.update', $cliente->codigo) }}" method="POST">
      @csrf @method('PUT')
      <div class="row">
        <div class="col-lg-8">
          <div class="card mb-4">
            <div class="card-header pb-0"><h6>Dados Pessoais</h6></div>
            <div class="card-body">
              <div class="row">
                <div class="col-md-8 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Nome *</label>
                  <input type="text" name="nome" class="form-control" value="{{ old('nome', $cliente->nome) }}" required>
                </div>
                @if($podeAlterarSensiveis)
                <div class="col-md-4 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">CPF</label>
                  <input type="text" name="cpf" class="form-control" value="{{ old('cpf', $cliente->cpf) }}">
                </div>
                @endif
              </div>
              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">E-mail</label>
                  <input type="email" name="email" class="form-control" value="{{ old('email', $cliente->email) }}">
                </div>
                <div class="col-md-6 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Telefone</label>
                  <input type="text" name="telefone" class="form-control" value="{{ old('telefone', $cliente->telefone) }}">
                </div>
              </div>

              @if($podeAlterarSensiveis)
              <hr class="horizontal dark">
              <p class="text-xs font-weight-bold text-uppercase text-secondary mb-2">Endereço</p>
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Logradouro</label>
                <input type="text" name="endereco" class="form-control" value="{{ old('endereco', $cliente->endereco) }}">
              </div>
              <div class="row">
                <div class="col-md-5 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Bairro</label>
                  <input type="text" name="bairro" class="form-control" value="{{ old('bairro', $cliente->bairro) }}">
                </div>
                <div class="col-md-4 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">Cidade</label>
                  <input type="text" name="cidade" class="form-control" value="{{ old('cidade', $cliente->cidade) }}">
                </div>
                <div class="col-md-3 mb-3">
                  <label class="form-label text-xs font-weight-bold text-uppercase">CEP</label>
                  <input type="text" name="cep" class="form-control" value="{{ old('cep', $cliente->cep) }}">
                </div>
              </div>
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Estado</label>
                <select name="estado" class="form-control">
                  @foreach(['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'] as $uf)
                    <option value="{{ $uf }}" {{ ($cliente->estado === $uf) ? 'selected' : '' }}>{{ $uf }}</option>
                  @endforeach
                </select>
              </div>
              @endif
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          @if($podeAlterarSensiveis)
          <div class="card mb-4">
            <div class="card-header pb-0"><h6><i class="fas fa-wallet me-2 text-success"></i>Carteira & Status</h6></div>
            <div class="card-body">
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Limite de Crédito (R$)</label>
                <input type="number" name="limite_credito" class="form-control" value="{{ old('limite_credito', $cliente->limite_credito) }}" min="0" step="0.01">
              </div>
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Dia de Fechamento</label>
                <input type="number" name="dia_fechamento" class="form-control" value="{{ old('dia_fechamento', $cliente->dia_fechamento) }}" min="1" max="28">
              </div>
              <div class="mb-3">
                <label class="form-label text-xs font-weight-bold text-uppercase">Status</label>
                <select name="status" class="form-control">
                  @foreach(['esp_facial'=>'Aguard. Facial','esp_documentos'=>'Aguard. Docs','esp_dados'=>'Aguard. Dados','processando'=>'Em Análise','ativo'=>'Ativo','bloqueado'=>'Bloqueado','pag_atrasado'=>'Pag. Atrasado','cobranca'=>'Cobrança','juridico'=>'Jurídico','cancelado'=>'Cancelado'] as $val => $lbl)
                    <option value="{{ $val }}" {{ $cliente->status === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>
          @endif

          <div class="d-flex flex-column gap-2">
            <button type="submit" class="btn bg-gradient-primary">
              <i class="fas fa-save me-1"></i> Salvar Alterações
            </button>
            <a href="{{ route('bank.clientes.show', $cliente->codigo) }}" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-1"></i> Cancelar
            </a>
          </div>
        </div>
      </div>
    </form>
  </div>
</main>
@endsection
