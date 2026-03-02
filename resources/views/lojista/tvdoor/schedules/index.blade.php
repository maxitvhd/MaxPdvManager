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
                @php $type = class_basename($schedule->schedulable_type ?? ''); @endphp
                <tr>
                  <td>
                    <div class="d-flex px-3 py-1 align-items-center gap-2">
                      <i class="fas fa-desktop text-primary"></i>
                      <span class="text-sm font-weight-bold">{{ $schedule->player->name ?? 'N/A' }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="d-flex px-2 py-1 align-items-center gap-2">
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
                    <span class="badge badge-sm bg-gradient-dark">{{ $schedule->resolution ?? '1920x1080' }}</span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="badge badge-sm bg-gradient-warning">{{ $schedule->priority ?? 0 }}</span>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex gap-1 px-2">
                      {{-- Editar --}}
                      <button class="btn btn-link text-warning p-1 mb-0 btn-edit-schedule" title="Editar"
                        data-id="{{ $schedule->id }}"
                        data-player="{{ $schedule->player_id }}"
                        data-schedulable-id="{{ $schedule->schedulable_id }}"
                        data-schedulable-type="{{ $schedule->schedulable_type }}"
                        data-days="{{ json_encode($schedule->days ?? []) }}"
                        data-start="{{ $schedule->start_time }}"
                        data-end="{{ $schedule->end_time }}"
                        data-priority="{{ $schedule->priority ?? 0 }}"
                        data-res="{{ $schedule->resolution ?? '1920x1080' }}">
                        <i class="fas fa-edit"></i>
                      </button>
                      {{-- Preview --}}
                      <button class="btn btn-link text-info p-1 mb-0 btn-preview-schedule" title="Pré-visualizar"
                        data-name="{{ $schedule->schedulable->name ?? 'N/A' }}"
                        data-type="{{ $type }}"
                        data-start="{{ $schedule->start_time }}"
                        data-end="{{ $schedule->end_time }}"
                        data-res="{{ $schedule->resolution ?? '1920x1080' }}">
                        <i class="fas fa-eye"></i>
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

{{-- ========== Modal Novo Agendamento ========== --}}
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
                  <option value="1280x720">HD (1280×720)</option>
                  <option value="3840x2160">4K Ultra HD</option>
                  <option value="1080x1920">Vertical 9:16</option>
                  <option value="1080x1080">Quadrado</option>
                </select>
              </div>
              <label class="form-label fw-bold">Dias da Semana</label>
              <div class="d-flex flex-wrap gap-2 mb-3">
                @foreach(['mon'=>'Seg','tue'=>'Ter','wed'=>'Qua','thu'=>'Qui','fri'=>'Sex','sat'=>'Sáb','sun'=>'Dom'] as $val => $label)
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="days[]" value="{{ $val }}" id="new-day-{{ $val }}" checked>
                    <label class="form-check-label text-xs" for="new-day-{{ $val }}">{{ $label }}</label>
                  </div>
                @endforeach
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
                <label class="form-label fw-bold">Prioridade</label>
                <input type="number" name="priority" class="form-control" value="0">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">O que deseja exibir?</label>
              <div style="max-height:380px;overflow-y:auto;border:1px solid #dee2e6;border-radius:10px;padding:10px;">
                @if($layouts->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1">🎨 Layouts</p>
                  @foreach($layouts as $l)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="content_radio" value="{{ $l->id }}|App\Models\TvDoorLayout" id="c-lay-{{ $l->id }}" onchange="setContent(this.value)">
                    <label class="form-check-label text-sm" for="c-lay-{{ $l->id }}">{{ $l->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($media->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">📁 Mídias</p>
                  @foreach($media as $m)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="content_radio" value="{{ $m->id }}|App\Models\TvDoorMedia" id="c-med-{{ $m->id }}" onchange="setContent(this.value)">
                    <label class="form-check-label text-sm" for="c-med-{{ $m->id }}">{{ $m->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($campaigns->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">🚀 Campanhas MaxDivulga</p>
                  @foreach($campaigns as $c)
                  <div class="form-check mb-2">
                    <input class="form-check-input" type="radio" name="content_radio" value="{{ $c->id }}|App\Models\MaxDivulgaCampaign" id="c-cam-{{ $c->id }}" onchange="setContent(this.value)">
                    <label class="form-check-label text-sm" for="c-cam-{{ $c->id }}">{{ $c->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($layouts->isEmpty() && $media->isEmpty() && $campaigns->isEmpty())
                  <p class="text-sm text-center text-secondary py-3">Nenhum conteúdo disponível.</p>
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" class="btn bg-gradient-success">Agendar</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========== Modal Editar Agendamento ========== --}}
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2 text-warning"></i>Editar Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editScheduleForm" method="POST">
        @csrf @method('PUT')
        <input type="hidden" name="schedulable_id"   id="edit_schedulable_id">
        <input type="hidden" name="schedulable_type" id="edit_schedulable_type">
        <div class="modal-body">
          <div class="row">
            <div class="col-md-6">
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Player</label>
                <select name="player_id" id="edit_player_id" class="form-control" required>
                  @foreach($players as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Resolução</label>
                <select name="resolution" id="edit_resolution" class="form-control" required>
                  <option value="1920x1080">Full HD (1920×1080)</option>
                  <option value="1280x720">HD (1280×720)</option>
                  <option value="3840x2160">4K (3840×2160)</option>
                  <option value="1080x1920">Vertical 9:16</option>
                  <option value="1080x1080">Quadrado</option>
                </select>
              </div>
              <label class="form-label fw-bold">Dias da Semana</label>
              <div class="d-flex flex-wrap gap-2 mb-3" id="edit-days-wrap">
                @foreach(['mon'=>'Seg','tue'=>'Ter','wed'=>'Qua','thu'=>'Qui','fri'=>'Sex','sat'=>'Sáb','sun'=>'Dom'] as $val => $label)
                  <div class="form-check">
                    <input class="form-check-input edit-day-check" type="checkbox" name="days[]" value="{{ $val }}" id="edit-day-{{ $val }}">
                    <label class="form-check-label text-xs" for="edit-day-{{ $val }}">{{ $label }}</label>
                  </div>
                @endforeach
              </div>
              <div class="row">
                <div class="col-6">
                  <div class="form-group mb-3">
                    <label class="form-label fw-bold">Início</label>
                    <input type="time" name="start_time" id="edit_start_time" class="form-control" required>
                  </div>
                </div>
                <div class="col-6">
                  <div class="form-group mb-3">
                    <label class="form-label fw-bold">Fim</label>
                    <input type="time" name="end_time" id="edit_end_time" class="form-control" required>
                  </div>
                </div>
              </div>
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Prioridade</label>
                <input type="number" name="priority" id="edit_priority" class="form-control" value="0">
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Conteúdo</label>
              <div style="max-height:380px;overflow-y:auto;border:1px solid #dee2e6;border-radius:10px;padding:10px;" id="edit-content-list">
                @if($layouts->count())
                  <p class="text-xxs text-secondary fw-bold mb-1 mt-1">🎨 LAYOUTS</p>
                  @foreach($layouts as $l)
                  <div class="form-check mb-2">
                    <input class="form-check-input edit-content-radio" type="radio" name="content_radio_edit"
                      value="{{ $l->id }}|App\Models\TvDoorLayout"
                      id="ec-lay-{{ $l->id }}" onchange="setEditContent(this.value)">
                    <label class="form-check-label text-sm" for="ec-lay-{{ $l->id }}">{{ $l->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($media->count())
                  <p class="text-xxs text-secondary fw-bold mb-1 mt-2">📁 MÍDIAS</p>
                  @foreach($media as $m)
                  <div class="form-check mb-2">
                    <input class="form-check-input edit-content-radio" type="radio" name="content_radio_edit"
                      value="{{ $m->id }}|App\Models\TvDoorMedia"
                      id="ec-med-{{ $m->id }}" onchange="setEditContent(this.value)">
                    <label class="form-check-label text-sm" for="ec-med-{{ $m->id }}">{{ $m->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($campaigns->count())
                  <p class="text-xxs text-secondary fw-bold mb-1 mt-2">🚀 CAMPANHAS</p>
                  @foreach($campaigns as $c)
                  <div class="form-check mb-2">
                    <input class="form-check-input edit-content-radio" type="radio" name="content_radio_edit"
                      value="{{ $c->id }}|App\Models\MaxDivulgaCampaign"
                      id="ec-cam-{{ $c->id }}" onchange="setEditContent(this.value)">
                    <label class="form-check-label text-sm" for="ec-cam-{{ $c->id }}">{{ $c->name }}</label>
                  </div>
                  @endforeach
                @endif
              </div>
            </div>
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

{{-- ========== Modal Preview ========== --}}
<div class="modal fade" id="previewModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-eye me-2 text-info"></i>Detalhes do Agendamento</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body p-4">
        <ul class="list-group list-group-flush">
          <li class="list-group-item"><strong>Conteúdo:</strong> <span id="preview-name"></span></li>
          <li class="list-group-item"><strong>Tipo:</strong> <span id="preview-type"></span></li>
          <li class="list-group-item"><strong>Horário:</strong> <span id="preview-time"></span></li>
          <li class="list-group-item"><strong>Resolução:</strong> <span id="preview-res"></span></li>
        </ul>
      </div>
    </div>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // ---- Listeners para Editar ----
    document.querySelectorAll('.btn-edit-schedule').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            openEditSchedule(
                d.id, d.player, d.schedulableId, d.schedulableType,
                JSON.parse(d.days), d.start, d.end, d.priority, d.res
            );
        });
    });

    // ---- Listeners para Preview ----
    document.querySelectorAll('.btn-preview-schedule').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            previewSchedule(d.name, d.type, d.start, d.end, d.res);
        });
    });

    // ---- Auto-abrir se vier via GET ----
    @isset($schedule)
    openEditSchedule(
        {!! $schedule->id !!},
        {!! $schedule->player_id !!},
        {!! $schedule->schedulable_id !!},
        {!! json_encode($schedule->schedulable_type) !!},
        {!! json_encode($schedule->days ?? []) !!},
        {!! json_encode($schedule->start_time) !!},
        {!! json_encode($schedule->end_time) !!},
        {!! $schedule->priority ?? 0 !!},
        {!! json_encode($schedule->resolution ?? '1920x1080') !!}
    );
    @endisset
});

// ---- Novo agendamento: setar conteúdo via radio ----
function setContent(value) {
    const [id, type] = value.split('|');
    document.getElementById('new_schedulable_id').value   = id;
    document.getElementById('new_schedulable_type').value = type;
}

// ---- Editar: setar conteúdo via radio ----
function setEditContent(value) {
    const [id, type] = value.split('|');
    document.getElementById('edit_schedulable_id').value   = id;
    document.getElementById('edit_schedulable_type').value = type;
}

// ---- Abrir modal de edição com dados preenchidos ----
function openEditSchedule(id, playerId, schedulableId, schedulableType, days, start, end, priority, resolution) {
    // Action do form
    document.getElementById('editScheduleForm').action = `/lojista/tvdoor/schedules/${id}`;

    // Player
    document.getElementById('edit_player_id').value = playerId;

    // Resolução
    const resEl = document.getElementById('edit_resolution');
    Array.from(resEl.options).forEach(o => o.selected = (o.value === resolution));

    // Dias
    document.querySelectorAll('.edit-day-check').forEach(cb => {
        cb.checked = days.includes(cb.value);
    });

    // Horários
    document.getElementById('edit_start_time').value = start;
    document.getElementById('edit_end_time').value   = end;

    // Prioridade
    document.getElementById('edit_priority').value = priority;

    // Conteúdo (selecionar radio correspondente)
    document.getElementById('edit_schedulable_id').value   = schedulableId;
    document.getElementById('edit_schedulable_type').value = schedulableType;
    document.querySelectorAll('.edit-content-radio').forEach(r => {
        const [rid, rtype] = r.value.split('|');
        r.checked = (rid == schedulableId && rtype == schedulableType);
    });

    new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
}

// ---- Preview ----
function previewSchedule(name, type, start, end, res) {
    document.getElementById('preview-name').innerText = name;
    document.getElementById('preview-type').innerText = type;
    document.getElementById('preview-time').innerText = start + ' – ' + end;
    document.getElementById('preview-res').innerText  = res;
    new bootstrap.Modal(document.getElementById('previewModal')).show();
}
</script>
@endsection
