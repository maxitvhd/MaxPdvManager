@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Meus Players (Dispositivos)</h6>
                            <p class="text-sm mb-0">Gerencie os terminais onde sua programação será exibida.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn bg-gradient-primary btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addPlayerModal">
                                + Novo Player
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Código/Token</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Última Conexão</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($players as $player)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="icon icon-shape icon-sm bg-gradient-primary shadow text-center border-radius-sm me-3">
                                                <i class="ni ni-tv-2 text-white opacity-10"></i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $player->name }}</h6>
                                                <p class="text-xs text-secondary mb-0">ID: #{{ $player->id }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if($player->status == 'pending')
                                            <span class="badge badge-sm bg-gradient-info">Código: {{ $player->pairing_code }}</span>
                                        @else
                                            <span class="text-xs font-weight-bold">Token Ativo</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        @if($player->status == 'online')
                                            <span class="badge badge-sm bg-gradient-success">Online</span>
                                        @elseif($player->status == 'offline')
                                            <span class="badge badge-sm bg-gradient-secondary">Offline</span>
                                        @else
                                            <span class="badge badge-sm bg-gradient-warning">Aguardando Pareamento</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $player->last_seen_at ? $player->last_seen_at->diffForHumans() : 'Nunca' }}
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('lojista.tvdoor.players.destroy', $player->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja remover este player?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0">
                                                <i class="far fa-trash-alt me-2"></i>Excluir
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-sm mb-0">Nenhum player cadastrado. Clique em "+ Novo Player" para começar.</p>
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

<!-- Modal Adicionar Player -->
<div class="modal fade" id="addPlayerModal" tabindex="-1" role="dialog" aria-labelledby="addPlayerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addPlayerModalLabel">Adicionar Novo Player</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('lojista.tvdoor.players.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="player-name" class="col-form-label">Nome do Dispositivo:</label>
                        <input type="text" class="form-control" id="player-name" name="name" placeholder="Ex: TV Recepção, Painel Entrada" required>
                    </div>
                    <p class="text-xs text-muted mt-2">
                        Após cadastrar, o sistema gerará um código de 6 dígitos que você deve digitar no seu terminal de vídeo para parear o dispositivo.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Gerar Código de Pareamento</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
