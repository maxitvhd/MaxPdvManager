@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Agendamentos da TvDoor</h6>
                            <p class="text-sm mb-0">Defina o que será exibido em cada player e em qual horário.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn bg-gradient-success btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                + Novo Agendamento
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
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Dias/Horários</th>
                                    <th class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Prioridade</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($schedules as $schedule)
                                <tr>
                                    <td>
                                        <div class="d-flex px-3 py-1">
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $schedule->player->name }}</h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div class="icon icon-shape icon-xs bg-gradient-{{ str_contains($schedule->schedulable_type, 'Layout') ? 'info' : (str_contains($schedule->schedulable_type, 'Media') ? 'success' : 'primary') }} shadow text-center border-radius-sm me-3">
                                                <i class="ni ni-app text-white opacity-10"></i>
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $schedule->schedulable->name }}</h6>
                                                <p class="text-xxs text-secondary mb-0">Tipo: {{ str_replace('App\\Models\\', '', $schedule->schedulable_type) }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <p class="text-xs font-weight-bold mb-0">{{ implode(', ', array_map('ucfirst', $schedule->days)) }}</p>
                                        <p class="text-xxs text-secondary mb-0">{{ $schedule->start_time }} - {{ $schedule->end_time }}</p>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge badge-sm bg-gradient-secondary">{{ $schedule->priority }}</span>
                                    </td>
                                    <td class="align-middle">
                                        <form action="{{ route('lojista.tvdoor.schedules.destroy', $schedule->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-link text-danger text-gradient px-3 mb-0"><i class="far fa-trash-alt"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-4">Sem agendamentos.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Novo Agendamento -->
<div class="modal fade" id="addScheduleModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-radius-xl">
            <div class="modal-header">
                <h5 class="modal-title">Novo Agendamento</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('lojista.tvdoor.schedules.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Selecione o Player</label>
                        <select name="player_id" class="form-control" required>
                            @foreach($players as $p) <option value="{{ $p->id }}">{{ $p->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">O que deseja exibir?</label>
                        <select name="content_select" class="form-control" id="content_select" required>
                            <optgroup label="Layouts Personalizados">
                                @foreach($layouts as $l) <option value="{{ $l->id }}|App\Models\TvDoorLayout">🎨 {{ $l->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Mídias da Biblioteca">
                                @foreach($media as $m) <option value="{{ $m->id }}|App\Models\TvDoorMedia">📁 {{ $m->name }}</option> @endforeach
                            </optgroup>
                            <optgroup label="Campanhas MaxDivulga">
                                @foreach($campaigns as $c) <option value="{{ $c->id }}|App\Models\MaxDivulgaCampaign">🚀 {{ $c->name }}</option> @endforeach
                            </optgroup>
                        </select>
                        <input type="hidden" name="schedulable_id" id="schedulable_id">
                        <input type="hidden" name="schedulable_type" id="schedulable_type">
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Dias da Semana</label>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach(['mon' => 'Seg', 'tue' => 'Ter', 'wed' => 'Qua', 'thu' => 'Qui', 'fri' => 'Sex', 'sat' => 'Sáb', 'sun' => 'Dom'] as $val => $label)
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="days[]" value="{{ $val }}" id="day-{{ $val }}" checked>
                                    <label class="form-check-label text-xs" for="day-{{ $val }}">{{ $label }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Início</label>
                                <input type="time" name="start_time" class="form-control" value="08:00" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-group mb-3">
                                <label class="form-label">Fim</label>
                                <input type="time" name="end_time" class="form-control" value="22:00" required>
                            </div>
                        </div>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Prioridade (Opcional)</label>
                        <input type="number" name="priority" class="form-control" value="0">
                        <small class="text-xxs">Valores maiores aparecem primeiro.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn bg-gradient-success">Agendar Conteúdo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('content_select').addEventListener('change', function() {
    const [id, type] = this.value.split('|');
    document.getElementById('schedulable_id').value = id;
    document.getElementById('schedulable_type').value = type;
});
// Trigger inicial
window.addEventListener('DOMContentLoaded', () => {
    document.getElementById('content_select').dispatchEvent(new Event('change'));
});
</script>
@endsection
