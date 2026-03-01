@extends('layouts.user_type.auth')

@section('content')
<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg">
  <div class="container-fluid py-4">

    <div class="row mb-3">
      <div class="col-12 d-flex justify-content-between">
        <div>
          <nav><ol class="breadcrumb bg-transparent mb-1">
            <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.index') }}">MaxBank</a></li>
            <li class="breadcrumb-item text-sm"><a href="{{ route('bank.clientes.show', $cliente->codigo) }}">{{ $cliente->nome }}</a></li>
            <li class="breadcrumb-item text-sm active">Documentos</li>
          </ol></nav>
          <h4 class="mb-0"><i class="fas fa-folder-open me-2 text-warning"></i>Documentos do Cliente</h4>
          <p class="text-sm text-muted mb-0">{{ $cliente->nome }} — {{ $cliente->codigo }}</p>
        </div>
        <a href="{{ route('bank.clientes.show', $cliente->codigo) }}" class="btn btn-outline-secondary btn-sm">
          <i class="fas fa-arrow-left me-1"></i> Voltar
        </a>
      </div>
    </div>

    @php
      use Illuminate\Support\Facades\Storage;
      $docs = [
        'foto_perfil'      => ['label' => 'Foto de Perfil',         'icon' => 'fa-user-circle'],
        'foto_cpf'         => ['label' => 'CPF',                    'icon' => 'fa-id-card'],
        'foto_habilitacao' => ['label' => 'CNH / Habilitação',      'icon' => 'fa-car'],
        'foto_comprovante' => ['label' => 'Comprovante Residência',  'icon' => 'fa-home'],
      ];
    @endphp

    <div class="row">
      @foreach($docs as $campo => $info)
        <div class="col-md-6 col-lg-3 mb-4">
          <div class="card h-100">
            <div class="card-header pb-0 text-center">
              <i class="fas {{ $info['icon'] }} text-secondary fa-2x mb-1 d-block"></i>
              <h6 class="text-sm mb-0">{{ $info['label'] }}</h6>
            </div>
            <div class="card-body text-center">
              @if($cliente->$campo && Storage::disk('local')->exists($cliente->$campo))
                <div class="border rounded p-1 mb-2" style="background:#f8f9fa;">
                  <img src="{{ route('bank.doc.view', ['codigo' => $cliente->codigo, 'doc' => $campo]) }}"
                       alt="{{ $info['label'] }}" class="img-fluid rounded" style="max-height:200px;object-fit:cover;">
                </div>
                <span class="badge bg-gradient-success text-xs"><i class="fas fa-check me-1"></i>Enviado</span>
              @else
                <div class="d-flex align-items-center justify-content-center border rounded mb-2"
                     style="height:150px;background:#f8f9fa;">
                  <div class="text-center text-secondary">
                    <i class="fas fa-image fa-3x mb-2 d-block opacity-5"></i>
                    <span class="text-xs">Não enviado</span>
                  </div>
                </div>
                <span class="badge bg-gradient-secondary text-xs">Pendente</span>
              @endif
            </div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="alert alert-info">
      <i class="fas fa-info-circle me-2"></i>
      Os documentos são enviados pelo próprio cliente via portal bancário. Para solicitar reenvio, altere o status do cliente para <strong>"Aguard. Documentos"</strong>.
    </div>

  </div>
</main>
@endsection
