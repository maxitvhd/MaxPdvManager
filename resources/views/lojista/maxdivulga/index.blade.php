@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">

        {{-- Hero Banner --}}
        <div class="row mb-4">
            <div class="col-12">
                <div class="card border-0 shadow-lg"
                    style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%); border-radius: 20px; overflow:hidden;">
                    <div class="card-body py-4 px-4">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <div class="d-flex align-items-center mb-2">
                                    <span style="font-size:2.2rem; margin-right:12px;">🚀</span>
                                    <div>
                                        <h3 class="text-white mb-0 font-weight-bolder">MaxDivulga</h3>
                                        <p class="text-white-50 mb-0 text-sm">Motor de Campanhas Inteligentes com IA</p>
                                    </div>
                                </div>
                                <p class="text-white-50 text-sm mb-3">Crie artes profissionais, copies persuasivas e
                                    materiais de divulgação em segundos. A IA faz o trabalho pesado por você.</p>
                                <div class="d-flex flex-wrap gap-2">
                                    <span class="badge text-white"
                                        style="background:rgba(255,255,255,0.15); font-size:0.7rem; padding: 6px 12px; border-radius:20px;">✅
                                        Geração de Imagens PNG</span>
                                    <span class="badge text-white"
                                        style="background:rgba(255,255,255,0.15); font-size:0.7rem; padding: 6px 12px; border-radius:20px;">✅
                                        Catálogo PDF</span>
                                    <span class="badge text-white"
                                        style="background:rgba(255,255,255,0.15); font-size:0.7rem; padding: 6px 12px; border-radius:20px;">✅
                                        Copy WhatsApp/Instagram/Telegram</span>
                                    <span class="badge text-white"
                                        style="background:rgba(255,255,255,0.15); font-size:0.7rem; padding: 6px 12px; border-radius:20px;">✅
                                        Gatilhos de Vendas</span>
                                </div>
                            </div>
                            <div class="col-md-4 text-end mt-3 mt-md-0">
                                <a href="{{ route('lojista.maxdivulga.create') }}"
                                    class="btn btn-lg px-4 py-2 font-weight-bold"
                                    style="background: linear-gradient(135deg, #f72585, #b5179e); border:none; border-radius:50px; color:#fff; box-shadow: 0 8px 25px rgba(247,37,133,0.4); font-size:1rem;">
                                    ✨ Criar Nova Campanha com IA
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Stats --}}
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body text-center py-3">
                        <div style="font-size:1.8rem;">🎯</div>
                        <h4 class="font-weight-bolder mb-0">{{ $campaigns->count() }}</h4>
                        <p class="text-muted text-xs mb-0">Total de Campanhas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body text-center py-3">
                        <div style="font-size:1.8rem;">✅</div>
                        <h4 class="font-weight-bolder mb-0">{{ $campaigns->where('status', 'active')->count() }}</h4>
                        <p class="text-muted text-xs mb-0">Ativas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body text-center py-3">
                        <div style="font-size:1.8rem;">🖼️</div>
                        <h4 class="font-weight-bolder mb-0">{{ $campaigns->where('format', 'image')->count() }}</h4>
                        <p class="text-muted text-xs mb-0">Imagens Geradas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                    <div class="card-body text-center py-3">
                        <div style="font-size:1.8rem;">📄</div>
                        <h4 class="font-weight-bolder mb-0">{{ $campaigns->where('format', 'pdf')->count() }}</h4>
                        <p class="text-muted text-xs mb-0">PDFs Gerados</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Alert success --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show text-white mb-4" role="alert"
                style="border-radius:12px;">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        {{-- Campanhas Grid --}}
        <div class="row mb-2 align-items-center">
            <div class="col">
                <h5 class="font-weight-bolder mb-0">Minhas Campanhas</h5>
                <p class="text-muted text-sm mb-0">Clique em "Ver" para visualizar a arte e o texto gerado pela IA</p>
            </div>
        </div>

        @if($campaigns->isEmpty())
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm text-center py-5"
                        style="border-radius:20px; border: 2px dashed #dee2e6;">
                        <div style="font-size:4rem;">🤖</div>
                        <h4 class="mt-3">Nenhuma campanha criada ainda!</h4>
                        <p class="text-muted">Clique no botão acima para criar sua primeira campanha com Inteligência
                            Artificial.</p>
                        <a href="{{ route('lojista.maxdivulga.create') }}" class="btn bg-gradient-primary mx-auto"
                            style="width:fit-content;">✨ Criar Agora</a>
                    </div>
                </div>
            </div>
        @else
            <div class="row">
                @foreach($campaigns as $camp)
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card border-0 shadow-sm h-100"
                            style="border-radius:16px; overflow:hidden; transition: transform 0.2s, box-shadow 0.2s;"
                            onmouseenter="this.style.transform='translateY(-4px)';this.style.boxShadow='0 12px 35px rgba(0,0,0,0.15)'"
                            onmouseleave="this.style.transform='';this.style.boxShadow=''">

                            {{-- Preview da imagem se existir --}}
                            @if($camp->file_path && Str::endsWith($camp->file_path, '.png'))
                                <div style="height:180px; overflow:hidden; background:#f0f0f0;">
                                    <img src="{{ asset($camp->file_path) }}" alt="{{ $camp->name }}"
                                        style="width:100%; height:100%; object-fit:cover; object-position:top;"
                                        onerror="this.parentElement.innerHTML='<div style=\'height:180px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#667eea,#764ba2);\'><span style=\'font-size:3rem;\'>🎨</span></div>'">
                                </div>
                            @else
                                <div
                                    style="height:120px;display:flex;align-items:center;justify-content:center;background:linear-gradient(135deg,#1a1a2e,#16213e);">
                                    <span style="font-size:3rem;">
                                        @if($camp->format === 'pdf') 📄
                                        @elseif($camp->format === 'text') 📝
                                        @elseif($camp->format === 'audio') 🔊
                                        @elseif($camp->format === 'full') 🚀
                                        @else 🎨 @endif
                                    </span>
                                </div>
                            @endif

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="font-weight-bolder mb-0" style="font-size:0.9rem; line-height:1.3;">{{ $camp->name }}
                                    </h6>
                                    @if($camp->status == 'active')
                                        <span class="badge bg-gradient-success ms-2" style="flex-shrink:0;">Concluída</span>
                                    @else
                                        <span class="badge bg-gradient-warning ms-2"
                                            style="flex-shrink:0;">{{ ucfirst($camp->status) }}</span>
                                    @endif
                                </div>

                                @if($camp->audio_file_path)
                                    <div class="mb-3 w-100">
                                        <audio controls class="w-100" style="height: 35px; border-radius: 8px;">
                                            <source src="{{ asset($camp->audio_file_path) }}" type="audio/mpeg">
                                        </audio>
                                    </div>
                                @endif

                                @if($camp->is_scheduled)
                                    <div class="form-check form-switch ps-0 mb-2 d-flex align-items-center">
                                        <input class="form-check-input ms-0 mt-0" type="checkbox" id="toggle-{{ $camp->id }}" {{ $camp->is_active ? 'checked' : '' }} onchange="toggleCampaign({{ $camp->id }}, this)">
                                        <label class="form-check-label text-xs fw-bold ms-2 mb-0" for="toggle-{{ $camp->id }}">Piloto
                                            Automático</label>
                                    </div>
                                    <div class="mb-3 p-2 bg-gray-100 border-radius-md border">
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="far fa-calendar-alt text-secondary text-xs me-2"></i>
                                            <span class="text-xs fw-bold text-dark">
                                                {{ is_array($camp->scheduled_days) ? implode(', ', array_map('ucfirst', $camp->scheduled_days)) : 'Nenhum dia' }}
                                            </span>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="far fa-clock text-secondary text-xs me-2"></i>
                                            <span class="text-xs text-dark">
                                                @php
                                                    $countTimes = 0;
                                                    if (is_array($camp->scheduled_times)) {
                                                        foreach ($camp->scheduled_times as $dia => $horas) {
                                                            if (is_array($horas))
                                                                $countTimes += count($horas);
                                                        }
                                                    }
                                                @endphp
                                                {{ $countTimes > 0 ? $countTimes . ' horário(s) na semana' : 'Nenhum horário' }}
                                            </span>
                                        </div>
                                        <button type="button" class="btn btn-xs btn-outline-primary mb-0 mt-2 w-100"
                                            onclick="abrirModalEdicao({{ $camp->id }})">
                                            <i class="fas fa-edit me-1"></i> Editar Cronograma
                                        </button>
                                    </div>

                                    <!-- O modal foi movido para o final da página (fora do elemento Card) -->
                                @endif

                                <div class="d-flex flex-wrap gap-1 mb-3">
                                    <span class="badge"
                                        style="background:#e8f4fd;color:#0077b6;font-size:0.65rem;">{{ strtoupper($camp->format ?? 'ND') }}</span>
                                    <span class="badge"
                                        style="background:#f0fdf4;color:#16a34a;font-size:0.65rem;">{{ ucfirst($camp->type ?? '-') }}</span>
                                    @foreach($camp->channels ?? [] as $ch)
                                        <span class="badge"
                                            style="background:#fdf4ff;color:#9333ea;font-size:0.65rem;">{{ ucfirst($ch) }}</span>
                                    @endforeach
                                </div>

                                <p class="text-xs text-muted mb-3">
                                    <i class="fas fa-clock me-1"></i> {{ $camp->created_at->diffForHumans() }}
                                </p>

                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('lojista.maxdivulga.show', $camp->id) }}" class="btn btn-sm flex-fill"
                                        style="background:#0f3460;color:#fff;border-radius:8px;">
                                        <i class="fas fa-eye me-1"></i> Ver
                                    </a>
                                    @if($camp->file_path)
                                        <a href="{{ route('lojista.maxdivulga.download', $camp->id) }}" class="btn btn-sm"
                                            style="background:#10b981;color:#fff;border-radius:8px; width:38px;" title="Baixar">
                                            <i class="fas fa-download"></i>
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm"
                                        style="background:#ef4444;color:#fff;border-radius:8px; width:38px;" title="Excluir"
                                        onclick="if(confirm('Apagar esta campanha?')) document.getElementById('del-{{ $camp->id }}').submit()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <form id="del-{{ $camp->id }}" action="{{ route('lojista.maxdivulga.destroy', $camp->id) }}"
                                        method="POST" style="display:none;">
                                        @csrf @method('DELETE')
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Modais de Edição no Final do DOM (Evita conflitos de overflow:hidden e transform dos Cards) -->
    @if(isset($campaigns) && !$campaigns->isEmpty())
        @foreach($campaigns as $camp)
            @if($camp->is_scheduled)
                <div class="modal fade" id="editScheduleModal{{ $camp->id }}" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered" role="document">
                        <div class="modal-content border-radius-xl">
                            <form action="{{ route('lojista.maxdivulga.updateSchedule', $camp->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-header">
                                    <h5 class="modal-title" id="ModalLabel">Configurações da Automação (Robô)</h5>
                                                    <button type="button" class="btn-close text-dark" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body p-4 text-start">

                                                    <ul class="nav nav-tabs" id="myTab{{ $camp->id }}" role="tablist">
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link active" id="crono-tab-{{ $camp->id }}" data-bs-toggle="tab" data-bs-target="#crono-{{ $camp->id }}" type="button" role="tab" aria-controls="crono" aria-selected="true">Cronograma</button>
                                                        </li>
                                                        <li class="nav-item" role="presentation">
                                                            <button class="nav-link" id="audio-tab-{{ $camp->id }}" data-bs-toggle="tab" data-bs-target="#audio-{{ $camp->id }}" type="button" role="tab" aria-controls="audio" aria-selected="false">Voz e Mídia</button>
                                                        </li>
                                                    </ul>

                                                    <div class="tab-content" id="myTabContent{{ $camp->id }}">
                                                        <!-- ABA 1: CRONOGRAMA -->
                                                        <div class="tab-pane fade show active mt-3" id="crono-{{ $camp->id }}" role="tabpanel" aria-labelledby="crono-tab-{{ $camp->id }}">
                                                            <div class="alert alert-info border-radius-lg text-white text-sm mb-3">
                                                                A inteligência artificial fará novas leituras do seu painel nesses horários, disparando automaticamente o conteúdo gerado.
                                                            </div>

                                                            <div class="row align-items-end mb-3">
                                                                <div class="col-md-5">
                                                                    <label class="form-label font-weight-bold text-xs mb-1">Dia da Semana</label>
                                                                    <select class="form-control form-control-sm" id="dia_add_{{ $camp->id }}">
                                                                        <option value="segunda">Segunda-Feira</option>
                                                                        <option value="terca">Terça-Feira</option>
                                                                        <option value="quarta">Quarta-Feira</option>
                                                                        <option value="quinta">Quinta-Feira</option>
                                                                        <option value="sexta">Sexta-Feira</option>
                                                                        <option value="sabado">Sábado</option>
                                                                        <option value="domingo">Domingo</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-5">
                                                                    <label class="form-label font-weight-bold text-xs mb-1">Horário (Ex: 09:00)</label>
                                                                    <input type="time" class="form-control form-control-sm" id="time_add_{{ $camp->id }}">
                                                                </div>
                                                                <div class="col-md-2">
                                                                    <button type="button" class="btn btn-sm btn-primary mb-0 w-100 p-0" style="height: 32px;"
                                                                        onclick="addDiaHorario({{ $camp->id }})"><i class="fas fa-plus"></i></button>
                                                                </div>
                                                            </div>

                                                            <h6 class="text-sm border-bottom pb-2 mt-4">Cronograma Atual</h6>

                                                            <!-- JSON PARA BACKEND -->
                                                            <input type="hidden" name="scheduled_times_json" id="times_json_{{ $camp->id }}"
                                                                value="{{ is_array($camp->scheduled_times) && count($camp->scheduled_times) > 0 ? json_encode($camp->scheduled_times) : '{}' }}">

                                                            <div id="times_list_{{ $camp->id }}" class="mt-2"
                                                                style="max-height:220px; overflow-y:auto; border-radius: 8px;">
                                                                <!-- JS irá popular as cartelas -->
                                                            </div>
                                                        </div>

                                                        <!-- ABA 2: VOZ E MÍDIA -->
                                                        <div class="tab-pane fade mt-3" id="audio-{{ $camp->id }}" role="tabpanel" aria-labelledby="audio-tab-{{ $camp->id }}">
                                                            <div class="row">
                                                                <div class="col-md-12 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Formato Final do Piloto Automático</label>
                                                                    <select name="format" class="form-control">
                                                                        <option value="full" {{ $camp->format == 'full' ? 'selected' : '' }}>📸 Arte Gráfica HD + 🔊 Áudio Locução (+ Recomendado)</option>
                                                                        <option value="image" {{ $camp->format == 'image' ? 'selected' : '' }}>📸 Apenas Arte Gráfica (Imagem e Texto)</option>
                                                                        <option value="audio" {{ $camp->format == 'audio' ? 'selected' : '' }}>🔊 Apenas Áudio de Rádio/Locução Interna</option>
                                                                        <option value="text" {{ $camp->format == 'text' ? 'selected' : '' }}>💬 Apenas o Texto Promocional Limpo</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Voz do Locutor</label>
                                                                    <select name="voice" class="form-control">
                                                                        <option value="pt-BR-FabioNeural" {{ $camp->voice == 'pt-BR-FabioNeural' ? 'selected' : '' }}>Fábio (Padrão/Masculino)</option>
                                                                        <option value="pt-BR-AntonioNeural" {{ $camp->voice == 'pt-BR-AntonioNeural' ? 'selected' : '' }}>Antônio (Masculino Médio)</option>
                                                                        <option value="pt-BR-DonatoNeural" {{ $camp->voice == 'pt-BR-DonatoNeural' ? 'selected' : '' }}>Donato (Rápido/Agudo)</option>
                                                                        <option value="pt-BR-HumbertoNeural" {{ $camp->voice == 'pt-BR-HumbertoNeural' ? 'selected' : '' }}>Humberto (Grave/Robusto)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Velocidade da Locução</label>
                                                                    <select name="audio_speed" class="form-control">
                                                                        <option value="0.9" {{ $camp->audio_speed == 0.9 ? 'selected' : '' }}>Lenta (0.9x)</option>
                                                                        <option value="1.0" {{ $camp->audio_speed == 1.0 || empty($camp->audio_speed) ? 'selected' : '' }}>Normal (1.0x)</option>
                                                                        <option value="1.25" {{ $camp->audio_speed == 1.25 ? 'selected' : '' }}>Rápida (1.25x)</option>
                                                                        <option value="1.5" {{ $camp->audio_speed == 1.5 ? 'selected' : '' }}>Muito Rápida (1.5x - Feirão)</option>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Emoção (Noise Scale)</label>
                                                                    <input type="range" class="form-range" min="0" max="1" step="0.001" name="noise_scale" value="{{ $camp->noise_scale ?? 0.667 }}">
                                                                    <small class="text-xxs text-secondary d-flex justify-content-between"><span>Sóbria</span><span>Expressiva</span></small>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Dicção (Noise W)</label>
                                                                    <input type="range" class="form-range" min="0" max="1" step="0.001" name="noise_w" value="{{ $camp->noise_w ?? 0.8 }}">
                                                                    <small class="text-xxs text-secondary d-flex justify-content-between"><span>Rápida</span><span>Clara/Articulada</span></small>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Música de Fundo</label>
                                                                    <select name="bg_audio" class="form-control">
                                                                        <option value="none">Nenhuma (Voz Limpa)</option>
                                                                        @if(isset($bgAudios))
                                                                            @foreach ($bgAudios as $bgOption)
                                                                                <option value="{{ $bgOption }}" {{ $camp->bg_audio == $bgOption ? 'selected' : '' }}>📻 {{ $bgOption }}</option>
                                                                            @endforeach
                                                                        @endif
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-6 mb-3">
                                                                    <label class="form-label font-weight-bold text-xs">Volume da Música Fundo</label>
                                                                    <input type="number" step="0.01" min="0.01" max="1.00" name="bg_volume" class="form-control" value="{{ $camp->bg_volume ?? 0.20 }}">
                                                                    <small class="text-xxs text-secondary">Entre 0.01 a 1.00. Padrão: 0.20</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-link text-dark shadow-none"
                                                        data-bs-dismiss="modal">Fechar</button>
                                                    <button type="submit" class="btn bg-gradient-success">💾 Salvar Alterações</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <!-- Renderiza calado no bg -->
                                <script>document.addEventListener('DOMContentLoaded', function () { setTimeout(function () { renderTimes({{ $camp->id }}) }, 500); });</script>
            @endif
        @endforeach
    @endif

        <script>
            // Função limpa e nativa de acionamento do Modal Bootstrap (sem interferência de atributos css do hover)
            function abrirModalEdicao(id) {
                try {
                    var el = document.getElementById('editScheduleModal' + id);
                    var myModal = new bootstrap.Modal(el, {
                        backdrop: 'static',
                        keyboard: false
                    });
                    renderTimes(id);
                    myModal.show();
                } catch (error) {
                    console.error("Erro ao invocar modal programaticamente", error);
                    alert("Erro ao abrir janela de edição. Tente atualizar a página.");
                }
            }

            function toggleCampaign(id, el) {
                const isActive = el.checked;
                fetch(`/lojista/maxdivulga/${id}/toggle-active`, {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ is_active: isActive })
                }).then(res => res.json()).then(data => {
                    if (!data.success) { alert('Erro ao alterar status'); el.checked = !isActive; }
                }).catch(err => { alert('Erro de conexão'); el.checked = !isActive; });
            }

            // LÓGICA DO ARRAY DICIONÁRIO ( { sabado: ["09:00", "15:00"], domingo: ["12:00"] } )

            function addDiaHorario(campId) {
                const timeInput = document.getElementById('time_add_' + campId);
                const diaSelect = document.getElementById('dia_add_' + campId);

                const timeVal = timeInput.value;
                const diaVal = diaSelect.value;
                if (!timeVal || !diaVal) return;

                const jsonInput = document.getElementById('times_json_' + campId);
                let timesObj = {};
                try {
                    timesObj = JSON.parse(jsonInput.value || '{}');
                    if (Array.isArray(timesObj)) timesObj = {}; // limpa legado se for array simples
                } catch (e) { timesObj = {}; }

                // Se dia não existe, cria array dele
                if (!timesObj[diaVal]) {
                    timesObj[diaVal] = [];
                }

                // Adiciona se nao bater
                if (!timesObj[diaVal].includes(timeVal)) {
                    timesObj[diaVal].push(timeVal);
                    timesObj[diaVal].sort(); // ordena horas
                    jsonInput.value = JSON.stringify(timesObj);
                    renderTimes(campId);
                }
                timeInput.value = '';
            }

            function removeDiaHorario(campId, dia, timeToRemove) {
                const jsonInput = document.getElementById('times_json_' + campId);
                let timesObj = JSON.parse(jsonInput.value || '{}');

                if (timesObj[dia]) {
                    timesObj[dia] = timesObj[dia].filter(t => t !== timeToRemove);
                    if (timesObj[dia].length === 0) {
                        delete timesObj[dia]; // apaga chave do dia se vazio
                    }
                    jsonInput.value = JSON.stringify(timesObj);
                    renderTimes(campId);
                }
            }

            function renderTimes(campId) {
                const jsonInput = document.getElementById('times_json_' + campId);
                const listEl = document.getElementById('times_list_' + campId);
                let timesObj = {};

                try {
                    timesObj = JSON.parse(jsonInput.value || '{}');
                    // Evita crash de DB legado com Array Simples ["09:00"] que quebra o Obj.keys
                    if (Array.isArray(timesObj)) timesObj = {};
                } catch (e) { timesObj = {}; }

                listEl.innerHTML = '';

                const diasArr = Object.keys(timesObj);
                if (diasArr.length === 0) {
                    listEl.innerHTML = '<div class="text-center p-3 border border-dashed rounded text-muted text-sm border-radius-sm">Sua campanha do piloto automático está pausada. Dê um start informando uma data acima.</div>';
                    return;
                }

                diasArr.forEach(dia => {
                    const mapDias = {
                        'segunda': 'Segunda-Feira', 'terca': 'Terça-Feira', 'quarta': 'Quarta-Feira',
                        'quinta': 'Quinta-Feira', 'sexta': 'Sexta-Feira', 'sabado': 'Sábado', 'domingo': 'Domingo'
                    };
                    const nomeDia = mapDias[dia] || dia;

                    let htmlDia = `
                            <div class="card bg-gray-100 shadow-none mb-2 border">
                                <div class="card-body p-2 px-3">
                                    <h6 class="text-xs font-weight-bolder mb-1 text-primary">${nomeDia}</h6>
                                    <div class="d-flex flex-wrap gap-2">
                        `;

                    timesObj[dia].forEach(t => {
                        htmlDia += `
                                <span class="badge bg-white text-dark border d-flex align-items-center p-1 px-2" style="font-size:0.75rem;">
                                    <i class="far fa-clock text-secondary me-1"></i> ${t}
                                    <i class="fas fa-times ms-2 text-danger cursor-pointer" onclick="removeDiaHorario(${campId}, '${dia}', '${t}')"></i>
                                </span>
                            `;
                    });

                    htmlDia += `</div></div></div>`;
                    listEl.innerHTML += htmlDia;
                });
            }
        </script>
@endsection
```