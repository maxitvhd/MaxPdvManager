@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-white">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif
  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6>Meus Players (Dispositivos)</h6>
              <p class="text-sm mb-0">Gerencie os terminais onde sua programação será exibida.</p>
            </div>
            <div class="col-md-4 text-end">
              <button type="button" class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                <i class="fas fa-plus me-1"></i> Novo Player
              </button>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome do Player</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código / Token</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Última Conexão</th>
                  <th class="text-secondary opacity-7">Ações</th>
                </tr>
              </thead>
              <tbody>
                @forelse($players as $player)
                <tr>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center gap-2">
                      <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-sm">
                        <i class="ni ni-tv-2 text-white opacity-10"></i>
                      </div>
                      <div>
                        <h6 class="mb-0 text-sm">{{ $player->name }}</h6>
                        <p class="text-xs text-secondary mb-0">ID: #{{ $player->id }}</p>
                      </div>
                    </div>
                  </td>
                  <td>
                    @if($player->status === 'pending')
                      <span class="badge badge-sm bg-gradient-info">Código: {{ $player->pairing_code }}</span>
                    @else
                      <span class="text-xs font-weight-bold text-success"><i class="fas fa-check-circle me-1"></i>Token Ativo</span>
                    @endif
                  </td>
                  <td class="align-middle text-center">
                    @if($player->status === 'online')
                      <span class="badge badge-sm bg-gradient-success">Online</span>
                    @elseif($player->status === 'offline')
                      <span class="badge badge-sm bg-gradient-secondary">Offline</span>
                    @else
                      <span class="badge badge-sm bg-gradient-warning">Aguardando Pareamento</span>
                    @endif
                  </td>
                  <td class="align-middle text-center">
                    <span class="text-secondary text-xs">
                      {{ $player->last_seen_at ? $player->last_seen_at->diffForHumans() : 'Nunca' }}
                    </span>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex gap-1 px-2">
                      {{-- Editar --}}
                      <button class="btn btn-link text-warning p-1 mb-0 btn-edit-player" title="Editar"
                        data-id="{{ $player->id }}" 
                        data-name="{{ $player->name }}">
                        <i class="fas fa-edit"></i>
                      </button>
                      {{-- Visualizar QR/Código --}}
                      @if($player->status === 'pending')
                      <button class="btn btn-link text-info p-1 mb-0 btn-view-pairing" title="Ver código de pareamento"
                        data-code="{{ $player->pairing_code }}" 
                        data-name="{{ $player->name }}">
                        <i class="fas fa-qrcode"></i>
                      </button>
                      @endif
                      {{-- Excluir --}}
                      <form action="{{ route('lojista.tvdoor.players.destroy', $player->id) }}" method="POST" id="delete-player-{{ $player->id }}">
                        @csrf @method('DELETE')
                        <button type="button" class="btn btn-link text-danger p-1 mb-0" onclick="confirmDelete('delete-player-{{ $player->id }}', 'Deseja excluir este player? Ele deixará de sincronizar imediatamente.')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr>
                  <td colspan="5" class="text-center py-4 text-sm text-secondary">
                    Nenhum player cadastrado. Clique em "+ Novo Player" para começar.
                  </td>
                </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ===== Modal: Adicionar Player ===== --}}
<div class="modal fade" id="addPlayerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-plus-circle me-2 text-primary"></i>Novo Player</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('lojista.tvdoor.players.store') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Nome do Dispositivo</label>
            <input type="text" class="form-control" name="name" placeholder="Ex: TV Recepção, Painel Entrada" required>
          </div>
          <p class="text-xs text-muted mt-2">Um código de 6 dígitos será gerado para parear o dispositivo.</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn bg-gradient-primary">Gerar Código de Pareamento</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== Modal: Editar Player ===== --}}
<div class="modal fade" id="editPlayerModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2 text-warning"></i>Editar Player</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editPlayerForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="form-group">
            <label class="form-label">Nome do Dispositivo</label>
            <input type="text" class="form-control" id="editPlayerName" name="name" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn bg-gradient-warning">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ===== Modal: Ver Código de Pareamento ===== --}}
<div class="modal fade" id="pairingModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-qrcode me-2 text-info"></i>Código de Pareamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center py-4">
        <p class="text-sm mb-2">Player: <strong id="pairingPlayerName"></strong></p>
        <div style="font-size:3rem; font-weight:900; letter-spacing:12px; background:linear-gradient(135deg,#667eea,#764ba2); -webkit-background-clip:text; -webkit-text-fill-color:transparent;" id="pairingCode"></div>
        <p class="text-xs text-muted mt-3">Abra o player em <code>seusite.com/tvdoor/player.html</code> e digite este código.</p>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ---- Listeners para Editar ----
    document.querySelectorAll('.btn-edit-player').forEach(btn => {
        btn.addEventListener('click', function() {
            openEditPlayer(this.dataset.id, this.dataset.name);
        });
    });

    // ---- Listeners para Pareamento ----
    document.querySelectorAll('.btn-view-pairing').forEach(btn => {
        btn.addEventListener('click', function() {
            viewPairingCode(this.dataset.code, this.dataset.name);
        });
    });

    // ---- Auto-abrir se vier via GET ----
    @isset($player)
    openEditPlayer({{ $player->id }}, '{{ addslashes($player->name) }}');
    @endisset
});

function openEditPlayer(id, name) {
    document.getElementById('editPlayerName').value = name;
    document.getElementById('editPlayerForm').action = `/lojista/tvdoor/players/${id}`;
    new bootstrap.Modal(document.getElementById('editPlayerModal')).show();
}

function viewPairingCode(code, name) {
    document.getElementById('pairingCode').innerText = code;
    document.getElementById('pairingPlayerName').innerText = name;
    new bootstrap.Modal(document.getElementById('pairingModal')).show();
}
</script>
@endsection
