@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">

  {{-- Flash messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show text-white">{{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card mb-4">
        <div class="card-header pb-0">
          <div class="row align-items-center">
            <div class="col-md-8">
              <h6>Agendamentos da TvDoor</h6>
              <p class="text-sm mb-0">Defina o que será exibido em cada player e em qual horário.</p>
            </div>
            <div class="col-md-4 text-end">
              <button type="button" class="btn bg-gradient-success btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="fas fa-plus me-1"></i> Novo Agendamento
              </button>
            </div>
          </div>
        </div>
        <div class="card-body px-0 pt-0 pb-2">
          <div class="table-responsive p-0">
            <table class="table align-items-center mb-0">
              <thead>
                <tr>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Player</th>
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Conteúdo</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dias / Horários</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Resolução</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prioridade</th>
                  <th class="text-secondary opacity-7">Ações</th>
                </tr>
              </thead>
              <tbody>
                @forelse($schedules as $schedule)
                <tr>
                  <td>
                    <div class="d-flex px-3 py-1 align-items-center gap-2">
                      <i class="fas fa-desktop text-primary"></i>
                      <span class="text-sm font-weight-bold">{{ $schedule->player->name ?? 'N/A' }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center gap-2">
                      @php $type = class_basename($schedule->schedulable_type ?? ''); @endphp
                      <span class="badge badge-sm bg-gradient-{{ $type === 'TvDoorLayout' ? 'info' : ($type === 'TvDoorMedia' ? 'success' : 'primary') }}">{{ $type }}</span>
                      <span class="text-sm">{{ $schedule->schedulable->name ?? '-' }}</span>
                    </div>
                  </td>
                  <td class="align-middle text-center">
                    <p class="text-xs font-weight-bold mb-0">
                      @foreach($schedule->days ?? [] as $d)
                        @php $labels = ['mon'=>'Seg','tue'=>'Ter','wed'=>'Qua','thu'=>'Qui','fri'=>'Sex','sat'=>'Sáb','sun'=>'Dom']; @endphp
                        <span class="badge bg-secondary me-1 text-xxs">{{ $labels[$d] ?? $d }}</span>
                      @endforeach
                    </p>
                    <p class="text-xxs text-secondary mb-0">{{ $schedule->start_time }} – {{ $schedule->end_time }}</p>
                  </td>
                  <td class="align-middle text-center">
                    <span class="badge badge-sm bg-gradient-dark">{{ $schedule->resolution ?? '-' }}</span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="badge badge-sm bg-gradient-warning">{{ $schedule->priority ?? 0 }}</span>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex gap-1 px-2">
                      {{-- Preview --}}
                      <button class="btn btn-link text-info p-1 mb-0" title="Pré-visualizar"
                        onclick="previewSchedule('{{ $schedule->schedulable->name ?? 'N/A' }}', '{{ $type }}', '{{ $schedule->start_time }}', '{{ $schedule->end_time }}')">
                        <i class="fas fa-eye"></i>
                      </button>
                      {{-- Editar --}}
                      <button class="btn btn-link text-warning p-1 mb-0" title="Editar"
                        onclick="openEdit({{ $schedule->id }}, {{ $schedule->player_id }}, '{{ $schedule->schedulable_id }}|{{ $schedule->schedulable_type }}', {{ json_encode($schedule->days ?? []) }}, '{{ $schedule->start_time }}', '{{ $schedule->end_time }}', {{ $schedule->priority ?? 0 }}, '{{ $schedule->resolution ?? '' }}')">
                        <i class="fas fa-edit"></i>
                      </button>
                      {{-- Excluir --}}
                      <form action="{{ route('lojista.tvdoor.schedules.destroy', $schedule->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-1 mb-0" onclick="return confirm('Excluir este agendamento?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-sm text-secondary">Nenhum agendamento criado.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ========== Modal Novo Agendamento ========== -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title">Novo Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('lojista.tvdoor.schedules.store') }}" method="POST">
        @csrf
        <input type="hidden" name="schedulable_id"   id="new_schedulable_id">
        <input type="hidden" name="schedulable_type" id="new_schedulable_type">
        <div class="modal-body">
          <div class="row">
            <!-- Coluna Esquerda -->
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Player</label>
                <select name="player_id" class="form-control" required>
                  @foreach($players as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                  @endforeach
                </select>
              </div>

              <div class="form-group mb-3">
                <label class="form-label fw-bold">Resolução do Player</label>
                <select name="resolution" class="form-control" required>
                  <option value="1920x1080">Full HD (1920×1080)</option>
                  <option value="1280x720">HD Ready (1280×720)</option>
                  <option value="3840x2160">4K Ultra HD (3840×2160)</option>
                  <option value="1080x1920">Vertical 9:16 (1080×1920)</option>
                  <option value="1080x1080">Quadrado (1080×1080)</option>
                </select>
              </div>

              <div class="form-group mb-3">
                <label class="form-label fw-bold">Dias da Semana</label>
                <div class="d-flex flex-wrap gap-2">
                  @foreach(['mon'=>'Seg','tue'=>'Ter','wed'=>'Qua','thu'=>'Qui','fri'=>'Sex','sat'=>'Sáb','sun'=>'Dom'] as $val => $label)
                    <div class="form-check">
                      <input class="form-check-input" type="checkbox" name="days[]" value="{{ $val }}" id="new-day-{{ $val }}" checked>
                      <label class="form-check-label text-xs" for="new-day-{{ $val }}">{{ $label }}</label>
                    </div>
                  @endforeach
                </div>
              </div>

              <div class="row">
                <div class="col-6">
                  <div class="form-group mb-3">
                    <label class="form-label fw-bold">Início</label>
                    <input type="time" name="start_time" class="form-control" value="08:00" required>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group mb-3">
                    <label class="form-label fw-bold">Fim</label>
                    <input type="time" name="end_time" class="form-control" value="22:00" required>
                  </div>
                </div>
              </div>

              <div class="form-group mb-3">
                <label class="form-label fw-bold">Prioridade
                  <small class="text-muted">(maior = aparece primeiro)</small>
                </label>
                <input type="number" name="priority" class="form-control" value="0">
              </div>
            </div>

            <!-- Coluna Direita: Conteúdo com Radios -->
            <div class="col-md-6">
              <label class="form-label fw-bold">O que deseja exibir?</label>
              <div style="max-height:380px;overflow-y:auto;border:1px solid #dee2e6;border-radius:10px;padding:10px;">
                @if($layouts->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-1">🎨 Layouts</p>
                  @foreach($layouts as $l)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="content_radio" value="{{ $l->id }}|App\Models\TvDoorLayout"
                      id="c-lay-{{ $l->id }}" onchange="setContent(this.value)" required>
                    <label class="form-check-label text-sm" for="c-lay-{{ $l->id }}">{{ $l->name }}</label>
                  </div>
                  @endforeach
                @endif

                @if($media->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">📁 Mídias</p>
                  @foreach($media as $m)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="content_radio" value="{{ $m->id }}|App\Models\TvDoorMedia"
                      id="c-med-{{ $m->id }}" onchange="setContent(this.value)">
                    <label class="form-check-label text-sm" for="c-med-{{ $m->id }}">{{ $m->name }}</label>
                  </div>
                  @endforeach
                @endif

                @if($campaigns->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">🚀 Campanhas MaxDivulga</p>
                  @foreach($campaigns as $c)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="content_radio" value="{{ $c->id }}|App\Models\MaxDivulgaCampaign"
                      id="c-cam-{{ $c->id }}" onchange="setContent(this.value)">
                    <label class="form-check-label text-sm" for="c-cam-{{ $c->id }}">{{ $c->name }}</label>
                  </div>
                  @endforeach
                @endif

                @if($layouts->isEmpty() && $media->isEmpty() && $campaigns->isEmpty())
                  <p class="text-sm text-center text-secondary py-3">Nenhum conteúdo disponível. Crie layouts ou faça upload de mídias antes.</p>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" class="btn bg-gradient-success">Agendar Conteúdo</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========== Modal Preview ========== -->
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-eye me-2 text-info"></i> Pré-visualização</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body text-center p-4">
        <div id="preview-content" class="bg-dark p-4 border-radius-lg text-white">
          <p id="preview-name" class="h5"></p>
          <p id="preview-type" class="text-muted text-sm"></p>
          <p id="preview-time" class="text-info text-sm"></p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function setContent(value) {
    const [id, type] = value.split('|');
    document.getElementById('new_schedulable_id').value   = id;
    document.getElementById('new_schedulable_type').value = type;
}

function previewSchedule(name, type, start, end) {
    document.getElementById('preview-name').innerText = name;
    document.getElementById('preview-type').innerText = 'Tipo: ' + type;
    document.getElementById('preview-time').innerText = 'Horário: ' + start + ' – ' + end;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}

function openEdit(id, playerId, contentVal, days, start, end, priority, resolution) {
    // Para simplificar, redireciona para uma rota de edição ou abre o modal com dados preenchidos
    alert('Edição de ID #' + id + ' — implemente a rota de edição para completar.');
}
</script>
@endsection
