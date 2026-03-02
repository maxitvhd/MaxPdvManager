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
              <h6>Programações da TvDoor</h6>
              <p class="text-sm mb-0">Defina o que será exibido em cada player, em quais dias e horários (suporte a playlist).</p>
            </div>
            <div class="col-md-4 text-end">
              <button type="button" class="btn bg-gradient-success btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                <i class="fas fa-plus me-1"></i> Nova Programação
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
                  <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Conteúdo (Playlist)</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Horários</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Resolução</th>
                  <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prior.</th>
                  <th class="text-secondary opacity-7">Ações</th>
                </tr>
              </thead>
              <tbody>
                @forelse($schedules as $sched)
                <tr>
                  <td>
                    <div class="d-flex px-3 py-1 align-items-center gap-2">
                      <i class="fas fa-desktop text-primary"></i>
                      <span class="text-sm font-weight-bold">{{ $sched->player->name ?? 'N/A' }}</span>
                    </div>
                  </td>
                  <td>
                    <div class="px-2 py-1">
                      @php $items = $sched->content_items ?? []; @endphp
                      @if(count($items))
                        @foreach($items as $ci)
                          @php
                            $ciType = class_basename($ci['type'] ?? '');
                            $ciModel = match($ci['type'] ?? '') {
                              'App\Models\TvDoorLayout' => $layouts->find($ci['id'] ?? 0),
                              'App\Models\TvDoorMedia' => $media->find($ci['id'] ?? 0),
                              'App\Models\MaxDivulgaCampaign' => $campaigns->find($ci['id'] ?? 0),
                              default => null
                            };
                          @endphp
                          <span class="badge badge-sm bg-gradient-{{ $ciType === 'TvDoorLayout' ? 'info' : ($ciType === 'TvDoorMedia' ? 'success' : 'primary') }} me-1 mb-1">
                            {{ $ciModel->name ?? '#'.$ci['id'] }}
                          </span>
                        @endforeach
                      @else
                        <span class="text-secondary text-xs">Sem conteúdo</span>
                      @endif
                    </div>
                  </td>
                  <td class="align-middle text-center">
                    @php $slots = $sched->time_slots ?? []; @endphp
                    @if(count($slots))
                      @foreach($slots as $slot)
                        @php
                          $dayLabels = ['mon'=>'Seg','tue'=>'Ter','wed'=>'Qua','thu'=>'Qui','fri'=>'Sex','sat'=>'Sáb','sun'=>'Dom'];
                        @endphp
                        <p class="text-xxs mb-0">
                          <span class="badge bg-secondary me-1 text-xxs">{{ $dayLabels[$slot['day'] ?? ''] ?? ($slot['day'] ?? '?') }}</span>
                          {{ $slot['start'] ?? '' }}–{{ $slot['end'] ?? '' }}
                        </p>
                      @endforeach
                    @else
                      <span class="text-secondary text-xs">—</span>
                    @endif
                  </td>
                  <td class="align-middle text-center">
                    <span class="badge badge-sm bg-gradient-dark">{{ $sched->resolution ?? '1920x1080' }}</span>
                  </td>
                  <td class="align-middle text-center">
                    <span class="badge badge-sm bg-gradient-warning">{{ $sched->priority ?? 0 }}</span>
                  </td>
                  <td class="align-middle">
                    <div class="d-flex gap-1 px-2">
                      {{-- Editar --}}
                      <button class="btn btn-link text-warning p-1 mb-0 btn-edit-schedule" title="Editar"
                        data-id="{{ $sched->id }}"
                        data-player="{{ $sched->player_id }}"
                        data-content-items="{{ json_encode($sched->content_items ?? []) }}"
                        data-time-slots="{{ json_encode($sched->time_slots ?? []) }}"
                        data-priority="{{ $sched->priority ?? 0 }}"
                        data-res="{{ $sched->resolution ?? '1920x1080' }}">
                        <i class="fas fa-edit"></i>
                      </button>
                      {{-- Excluir --}}
                      <form action="{{ route('lojista.tvdoor.schedules.destroy', $sched->id) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-1 mb-0" onclick="return confirm('Excluir esta programação?')">
                          <i class="fas fa-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-4 text-sm text-secondary">Nenhuma programação criada.</td></tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- ========== Modal Nova Programação ========== --}}
<div class="modal fade" id="addScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-plus me-2 text-success"></i>Nova Programação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form action="{{ route('lojista.tvdoor.schedules.store') }}" method="POST" id="addScheduleForm">
        @csrf
        <div class="modal-body">
          <div class="row">
            {{-- Coluna 1: Configurações --}}
            <div class="col-md-4">
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Player</label>
                <select name="player_id" class="form-control" required>
                  @foreach($players as $p)
                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Resolução</label>
                <select name="resolution" class="form-control" required>
                  <option value="1920x1080">Full HD (1920×1080)</option>
                  <option value="1280x720">HD (1280×720)</option>
                  <option value="3840x2160">4K Ultra HD</option>
                  <option value="1080x1920">Vertical 9:16</option>
                  <option value="1080x1080">Quadrado</option>
                </select>
              </div>
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Prioridade</label>
                <input type="number" name="priority" class="form-control" value="0" min="0" max="100">
              </div>

              {{-- Horários dinâmicos --}}
              <label class="form-label fw-bold">Horários de Exibição</label>
              <div id="add-slots-container" class="mb-2"></div>
              <button type="button" class="btn btn-sm bg-gradient-info w-100" onclick="addSlot('add-slots-container', 'add_time_slots')">
                <i class="fas fa-plus me-1"></i> Adicionar Horário
              </button>
              <input type="hidden" name="time_slots" id="add_time_slots" value="[]">
            </div>

            {{-- Coluna 2: Conteúdo (Playlist) --}}
            <div class="col-md-8">
              <label class="form-label fw-bold">Conteúdo — Playlist (selecione um ou mais, na ordem de exibição)</label>
              <input type="hidden" name="content_items" id="add_content_items" value="[]">
              <div id="add-playlist-selected" class="mb-2 d-flex flex-wrap gap-2 p-2 border rounded" style="min-height:40px;background:#f8f9fa;">
                <span class="text-secondary text-xs" id="add-playlist-empty-msg">Nenhum item selecionado. Clique nos itens abaixo para adicionar.</span>
              </div>
              <div style="max-height:320px;overflow-y:auto;border:1px solid #dee2e6;border-radius:10px;padding:10px;">
                @if($layouts->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1">🎨 Layouts</p>
                  @foreach($layouts as $l)
                  <div class="form-check mb-1">
                    <input class="form-check-input add-content-check" type="checkbox" value="{{ $l->id }}|App\Models\TvDoorLayout" id="al-{{ $l->id }}" onchange="updatePlaylist('add')">
                    <label class="form-check-label text-sm" for="al-{{ $l->id }}">{{ $l->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($media->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">📁 Mídias</p>
                  @foreach($media as $m)
                  <div class="form-check mb-1">
                    <input class="form-check-input add-content-check" type="checkbox" value="{{ $m->id }}|App\Models\TvDoorMedia" id="am-{{ $m->id }}" onchange="updatePlaylist('add')">
                    <label class="form-check-label text-sm" for="am-{{ $m->id }}">{{ $m->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($campaigns->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">🚀 Campanhas MaxDivulga</p>
                  @foreach($campaigns as $c)
                  <div class="form-check mb-1">
                    <input class="form-check-input add-content-check" type="checkbox" value="{{ $c->id }}|App\Models\MaxDivulgaCampaign" id="ac-{{ $c->id }}" onchange="updatePlaylist('add')">
                    <label class="form-check-label text-sm" for="ac-{{ $c->id }}">{{ $c->name }}</label>
                  </div>
                  @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn bg-gradient-success" onclick="return validateScheduleForm('add')">Salvar Programação</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- ========== Modal Editar Programação ========== --}}
<div class="modal fade" id="editScheduleModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content border-radius-xl">
      <div class="modal-header">
        <h5 class="modal-title"><i class="fas fa-edit me-2 text-warning"></i>Editar Programação</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <form id="editScheduleForm" method="POST">
        @csrf @method('PUT')
        <div class="modal-body">
          <div class="row">
            {{-- Coluna 1: Configurações --}}
            <div class="col-md-4">
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
              <div class="form-group mb-3">
                <label class="form-label fw-bold">Prioridade</label>
                <input type="number" name="priority" id="edit_priority" class="form-control" value="0" min="0" max="100">
              </div>

              {{-- Horários dinâmicos --}}
              <label class="form-label fw-bold">Horários de Exibição</label>
              <div id="edit-slots-container" class="mb-2"></div>
              <button type="button" class="btn btn-sm bg-gradient-info w-100" onclick="addSlot('edit-slots-container', 'edit_time_slots')">
                <i class="fas fa-plus me-1"></i> Adicionar Horário
              </button>
              <input type="hidden" name="time_slots" id="edit_time_slots" value="[]">
            </div>

            {{-- Coluna 2: Conteúdo (Playlist) --}}
            <div class="col-md-8">
              <label class="form-label fw-bold">Conteúdo — Playlist (selecione um ou mais, na ordem de exibição)</label>
              <input type="hidden" name="content_items" id="edit_content_items" value="[]">
              <div id="edit-playlist-selected" class="mb-2 d-flex flex-wrap gap-2 p-2 border rounded" style="min-height:40px;background:#f8f9fa;">
                <span class="text-secondary text-xs" id="edit-playlist-empty-msg">Nenhum item selecionado.</span>
              </div>
              <div style="max-height:320px;overflow-y:auto;border:1px solid #dee2e6;border-radius:10px;padding:10px;">
                @if($layouts->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1">🎨 Layouts</p>
                  @foreach($layouts as $l)
                  <div class="form-check mb-1">
                    <input class="form-check-input edit-content-check" type="checkbox" value="{{ $l->id }}|App\Models\TvDoorLayout" id="el-{{ $l->id }}" onchange="updatePlaylist('edit')">
                    <label class="form-check-label text-sm" for="el-{{ $l->id }}">{{ $l->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($media->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">📁 Mídias</p>
                  @foreach($media as $m)
                  <div class="form-check mb-1">
                    <input class="form-check-input edit-content-check" type="checkbox" value="{{ $m->id }}|App\Models\TvDoorMedia" id="em-{{ $m->id }}" onchange="updatePlaylist('edit')">
                    <label class="form-check-label text-sm" for="em-{{ $m->id }}">{{ $m->name }}</label>
                  </div>
                  @endforeach
                @endif
                @if($campaigns->count())
                  <p class="text-xxs text-secondary text-uppercase fw-bold mb-1 mt-2">🚀 Campanhas MaxDivulga</p>
                  @foreach($campaigns as $c)
                  <div class="form-check mb-1">
                    <input class="form-check-input edit-content-check" type="checkbox" value="{{ $c->id }}|App\Models\MaxDivulgaCampaign" id="ec-{{ $c->id }}" onchange="updatePlaylist('edit')">
                    <label class="form-check-label text-sm" for="ec-{{ $c->id }}">{{ $c->name }}</label>
                  </div>
                  @endforeach
                @endif
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn bg-gradient-warning" onclick="return validateScheduleForm('edit')">Salvar Alterações</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
// ============================================================
// SLOTS (Horários por dia)
// ============================================================
const DAYS = {mon:'Seg',tue:'Ter',wed:'Qua',thu:'Qui',fri:'Sex',sat:'Sáb',sun:'Dom'};

function addSlot(containerId, hiddenId, data = {}) {
    const container = document.getElementById(containerId);
    const slotId = Date.now();
    const div = document.createElement('div');
    div.className = 'card p-2 mb-2 border';
    div.dataset.slotId = slotId;
    div.innerHTML = `
        <div class="row g-1 align-items-center">
            <div class="col-4">
                <select class="form-control form-control-sm slot-day" onchange="serializeSlots('${containerId}','${hiddenId}')">
                    ${Object.entries(DAYS).map(([v,l]) => `<option value="${v}" ${data.day===v?'selected':''}>${l}</option>`).join('')}
                </select>
            </div>
            <div class="col-3">
                <input type="time" class="form-control form-control-sm slot-start" value="${data.start||'08:00'}" onchange="serializeSlots('${containerId}','${hiddenId}')">
            </div>
            <div class="col-1 text-center text-xs">às</div>
            <div class="col-3">
                <input type="time" class="form-control form-control-sm slot-end" value="${data.end||'22:00'}" onchange="serializeSlots('${containerId}','${hiddenId}')">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-link text-danger p-0" onclick="this.closest('[data-slot-id]').remove(); serializeSlots('${containerId}','${hiddenId}')">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>`;
    container.appendChild(div);
    serializeSlots(containerId, hiddenId);
}

function serializeSlots(containerId, hiddenId) {
    const slots = [];
    document.querySelectorAll(`#${containerId} [data-slot-id]`).forEach(div => {
        slots.push({
            day: div.querySelector('.slot-day').value,
            start: div.querySelector('.slot-start').value,
            end: div.querySelector('.slot-end').value
        });
    });
    document.getElementById(hiddenId).value = JSON.stringify(slots);
}

// ============================================================
// PLAYLIST (Conteúdo)
// ============================================================
function updatePlaylist(mode) {
    const prefix = mode === 'add' ? 'add' : 'edit';
    const checks = document.querySelectorAll(`.${prefix}-content-check:checked`);
    const items = Array.from(checks).map(c => {
        const [id, type] = c.value.split('|');
        const label = c.parentElement.querySelector('label').textContent.trim();
        return {id: parseInt(id), type, label};
    });

    // Atualiza o hidden input
    document.getElementById(`${prefix}_content_items`).value = JSON.stringify(
        items.map(i => ({id: i.id, type: i.type}))
    );

    // Atualiza a visualização de selecionados
    const display = document.getElementById(`${prefix}-playlist-selected`);
    const emptyMsg = document.getElementById(`${prefix}-playlist-empty-msg`);
    display.innerHTML = '';
    if (items.length === 0) {
        const span = document.createElement('span');
        span.className = 'text-secondary text-xs';
        span.id = `${prefix}-playlist-empty-msg`;
        span.textContent = 'Nenhum item selecionado.';
        display.appendChild(span);
    } else {
        items.forEach((item, idx) => {
            const badge = document.createElement('span');
            badge.className = 'badge bg-gradient-primary';
            badge.textContent = `${idx+1}. ${item.label}`;
            display.appendChild(badge);
        });
    }
}

// ============================================================
// ABRIR MODAL DE EDIÇÃO
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    // Listener para botões de editar
    document.querySelectorAll('.btn-edit-schedule').forEach(btn => {
        btn.addEventListener('click', function() {
            const d = this.dataset;
            openEditSchedule(
                d.id,
                d.player,
                JSON.parse(d.contentItems || '[]'),
                JSON.parse(d.timeSlots || '[]'),
                parseInt(d.priority) || 0,
                d.res
            );
        });
    });
});

function openEditSchedule(id, playerId, contentItems, timeSlots, priority, resolution) {
    // Limpar
    document.getElementById('edit-slots-container').innerHTML = '';
    document.querySelectorAll('.edit-content-check').forEach(c => c.checked = false);

    // Action do form
    document.getElementById('editScheduleForm').action = `/lojista/tvdoor/schedules/${id}`;

    // Player
    document.getElementById('edit_player_id').value = playerId;

    // Resolução
    const resEl = document.getElementById('edit_resolution');
    Array.from(resEl.options).forEach(o => o.selected = (o.value === resolution));

    // Prioridade
    document.getElementById('edit_priority').value = priority;

    // Horários
    (timeSlots || []).forEach(slot => addSlot('edit-slots-container', 'edit_time_slots', slot));
    if (!timeSlots || timeSlots.length === 0) {
        addSlot('edit-slots-container', 'edit_time_slots');
    }

    // Conteúdo (checar os checkboxes correspondentes)
    (contentItems || []).forEach(ci => {
        const cb = document.querySelector(`.edit-content-check[value="${ci.id}|${ci.type}"]`);
        if (cb) cb.checked = true;
    });
    updatePlaylist('edit');

    new bootstrap.Modal(document.getElementById('editScheduleModal')).show();
}

// ============================================================
// VALIDAÇÃO
// ============================================================
function validateScheduleForm(mode) {
    const prefix = mode === 'add' ? 'add' : 'edit';
    const items = JSON.parse(document.getElementById(`${prefix}_content_items`).value || '[]');
    const slots = JSON.parse(document.getElementById(`${prefix}_time_slots`).value || '[]');

    if (items.length === 0) {
        alert('Selecione pelo menos um item de conteúdo para a playlist.');
        return false;
    }
    if (slots.length === 0) {
        alert('Adicione pelo menos um horário de exibição.');
        return false;
    }
    return true;
}

// Inicializar slot vazio no modal de adicionar quando abrir
document.getElementById('addScheduleModal').addEventListener('show.bs.modal', function() {
    const c = document.getElementById('add-slots-container');
    if (c.children.length === 0) {
        addSlot('add-slots-container', 'add_time_slots');
    }
    // Limpar checkboxes
    document.querySelectorAll('.add-content-check').forEach(c => c.checked = false);
    updatePlaylist('add');
});
</script>
@endsection
