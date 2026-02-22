@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        {{-- Header --}}
        <div class="row align-items-center mb-4">
            <div class="col-auto">
                <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-sm btn-white mb-0 shadow-sm border">
                    <i class="fas fa-arrow-left me-1"></i> Voltar
                </a>
            </div>
            <div class="col">
                <h4 class="mb-0 font-weight-bolder text-dark">{{ $campaign->name }}</h4>
                <p class="text-sm mb-0 text-secondary">
                    <i class="far fa-calendar-alt me-1"></i> Gerada em {{ $campaign->created_at->format('d/m/Y \à\s H:i') }}
                </p>
            </div>
            <div class="col-auto text-end">
                @if($campaign->file_path)
                    <a href="{{ route('lojista.maxdivulga.download', $campaign->id) }}"
                        class="btn btn-primary btn-sm mb-0 shadow-sm pulse-btn">
                        <i class="fas fa-download me-1"></i> Baixar Mídia
                    </a>
                @endif
            </div>
        </div>

        {{-- Resumo da Campanha --}}
        <div class="row mb-4">
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center h-100">
                            <div class="col-8">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-muted">Status</p>
                                <div class="mt-2">
                                    <span
                                        class="badge bg-gradient-{{ $campaign->status === 'active' ? 'success' : 'secondary' }} px-3 py-2">
                                        {{ ucfirst($campaign->status) }}
                                    </span>
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md">
                                    <i class="fas fa-info-circle text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center h-100">
                            <div class="col-8">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-muted">Formato</p>
                                <h5 class="font-weight-bolder mb-0 text-dark mt-1">
                                    {{ strtoupper($campaign->format ?? 'N/D') }}
                                </h5>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md">
                                    <i class="fas fa-photo-video text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center h-100">
                            <div class="col-8">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-muted">Tipo</p>
                                <h5 class="font-weight-bolder mb-0 text-dark mt-1">
                                    {{ ucfirst($campaign->type ?? '-') }}
                                </h5>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-success shadow text-center border-radius-md">
                                    <i class="fas fa-tag text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-sm-6">
                <div class="card shadow-sm border-0 h-100">
                    <div class="card-body p-3">
                        <div class="row align-items-center h-100">
                            <div class="col-8">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-muted">Canais</p>
                                <div class="mt-1">
                                    @forelse($campaign->channels ?? [] as $ch)
                                        <span class="badge bg-light text-dark border mb-1">{{ ucfirst($ch) }}</span>
                                    @empty
                                        <span class="text-muted text-sm">Nenhum</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="col-4 text-end">
                                <div class="icon icon-shape bg-gradient-warning shadow text-center border-radius-md">
                                    <i class="fas fa-bullhorn text-lg opacity-10" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        @if($campaign->is_scheduled && is_null($campaign->parent_id))
            <!-- Info do Cronograma de Disparo -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-2 border-primary" style="border-radius:16px;">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between flex-wrap gap-3">
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape bg-gradient-primary shadow text-center border-radius-md me-3">
                                    <i class="fas fa-calendar-check text-lg opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Dias de Disparo</h6>
                                    <p class="text-sm font-weight-bold mb-0 text-primary">
                                        {{ is_array($campaign->scheduled_days) && count($campaign->scheduled_days) > 0 ? implode(', ', array_map('ucfirst', $campaign->scheduled_days)) : 'Nenhum dia' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="d-flex align-items-center">
                                <div class="icon icon-shape bg-gradient-info shadow text-center border-radius-md me-3">
                                    <i class="fas fa-clock text-lg opacity-10"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0">Horários de Disparo</h6>
                                    <p class="text-sm font-weight-bold mb-0 text-info">
                                        @php
                                            $allTimes = [];
                                            if(is_array($campaign->scheduled_times)) {
                                                foreach($campaign->scheduled_times as $dia => $horas) {
                                                    if(is_array($horas)) {
                                                        foreach($horas as $h) {
                                                            $allTimes[] = ucfirst($dia) . ' às ' . $h;
                                                        }
                                                    }
                                                }
                                            }
                                        @endphp
                                        {{ count($allTimes) > 0 ? implode(', ', $allTimes) : 'Nenhum horário selecionado' }}
                                    </p>
                                </div>
                            </div>

                            <div>
                                <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-outline-primary mb-0">
                                    <i class="fas fa-edit me-1"></i> Alterar Agendamento no Painel
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-12">
                    <div class="card shadow-sm border-0" style="border-radius:16px;">
                        <div class="card-header pb-0 bg-transparent border-bottom">
                            <h6><i class="fas fa-history text-primary me-2"></i> Histórico de Disparos (Campanhas Geradas)</h6>
                            <p class="text-sm text-muted">Abaixo estão listadas todas as campanhas e mídias geradas automaticamente por esta programação.</p>
                        </div>
                        <div class="card-body px-0 pt-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Data Ref.</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Status</th>
                                            <th class="text-secondary opacity-7"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($campaign->children()->orderBy('created_at', 'desc')->get() as $child)
                                            <tr>
                                                <td>
                                                    <div class="d-flex px-3 py-1">
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm"><i class="far fa-clock me-1"></i> {{ $child->created_at->format('d/m/Y \à\s H:i') }}</h6>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="badge badge-sm bg-gradient-{{ $child->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($child->status) }}</span>
                                                </td>
                                                <td class="align-middle text-end pe-4">
                                                    <a href="{{ route('lojista.maxdivulga.show', $child->id) }}" class="btn btn-sm btn-outline-primary mb-0 shadow-sm border">
                                                        <i class="fas fa-eye me-1"></i> Ver Conteúdo
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center py-5 text-muted">
                                                    <i class="fas fa-robot fa-3x mb-3 text-secondary opacity-5"></i><br>
                                                    Nenhum disparo gerado ainda.<br><small>A IA criará as peças aqui no seu próximo horário agendado!</small>
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
        @else
        <div class="row g-4 d-flex align-items-stretch">
            {{-- COLUNA ESQUERDA: Arte Visual e Áudio --}}
            <div class="col-lg-5 col-xl-4 d-flex flex-column">
                <div class="card border-0 shadow-sm flex-grow-1" style="border-radius:16px; overflow:hidden;">
                    <div class="card-header pb-0 bg-transparent border-0 text-center pt-4">
                        <h6 class="mb-0 text-dark font-weight-bolder"><i class="fas fa-paint-brush me-2 text-primary"></i>
                            Mídia Gerada</h6>
                    </div>
                    <div class="card-body px-4 pb-4 pt-3 d-flex flex-column align-items-center justify-content-center"
                        style="min-height: 400px;">
                        @if($campaign->format === 'text')
                            <div class="text-center py-5">
                                <div class="icon icon-shape bg-gradient-light shadow text-center border-radius-xl d-inline-flex align-items-center justify-content-center mb-3"
                                    style="width: 80px; height: 80px;">
                                    <i class="fas fa-file-alt text-dark" style="font-size: 2.5rem;"></i>
                                </div>
                                <h5 class="text-dark">Apenas Texto</h5>
                                <p class="text-muted text-sm">Esta campanha não possui mídia visual.</p>
                            </div>
                        @elseif(in_array($campaign->format, ['image', 'full']) && $campaign->file_path)
                            <div class="position-relative w-100 text-center mb-4">
                                <img src="{{ asset($campaign->file_path) }}" class="img-fluid rounded-3 shadow-sm border"
                                    alt="Arte da Campanha"
                                    style="max-height:550px; object-fit:contain; transition: transform 0.3s ease;"
                                    onerror="this.parentElement.innerHTML='<div class=\'text-center py-5\'><i class=\'fas fa-exclamation-triangle text-warning fa-3x mb-3\'></i><p class=\'text-muted\'>Arte indisponível ou em processamento.</p></div>'">
                            </div>

                            @if($campaign->audio_file_path)
                                <div class="w-100 bg-light rounded-3 p-3 mt-auto shadow-sm border border-light">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-sm font-weight-bolder text-dark"><i
                                                class="fas fa-volume-up me-2 text-primary"></i> Áudio Locução</span>
                                        <a href="{{ asset($campaign->audio_file_path) }}" download
                                            class="btn btn-xs btn-outline-primary mb-0">
                                            <i class="fas fa-download"></i> Baixar
                                        </a>
                                    </div>
                                    <audio controls class="w-100 mt-2 shadow-sm" style="height: 40px; border-radius: 20px;">
                                        <source src="{{ asset($campaign->audio_file_path) }}" type="audio/mpeg">
                                        Seu navegador não suporta reprodução de áudio.
                                    </audio>
                                </div>
                            @endif
                        @elseif($campaign->format === 'pdf' && $campaign->file_path)
                            <div class="w-100 h-100 rounded-3 overflow-hidden shadow-sm border">
                                <iframe src="{{ asset($campaign->file_path) }}" width="100%" height="600px"
                                    style="border:none;"></iframe>
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="spinner-border text-primary mb-3" role="status"></div>
                                <h5 class="text-dark">Processando...</h5>
                                <p class="text-muted text-sm">A mídia ainda está sendo gerada.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- COLUNA DIREITA: Copies --}}
            <div class="col-lg-7 col-xl-8 d-flex flex-column">
                <div class="row g-4 h-100">

                    {{-- Copy Social --}}
                    <div class="col-12">
                        <div class="card border-0 shadow-sm h-100" style="border-radius:16px;">
                            <div
                                class="card-header pb-2 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 text-dark font-weight-bolder">
                                    <i class="fab fa-whatsapp text-success fs-5 me-2 align-middle"></i>
                                    Copy para Redes Sociais
                                </h6>
                                <button class="btn btn-sm btn-outline-secondary mb-0 copy-btn shadow-sm"
                                    onclick="copyToClipboard('copyText', this)">
                                    <i class="fas fa-copy me-1"></i> Copiar
                                </button>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $copyParaSocial = $campaign->copy_acompanhamento ?: 'A IA não gerou texto de acompanhamento.';
                                @endphp
                                <div id="copyText" class="p-4 bg-gray-100 border-radius-lg text-dark m-3"
                                    style="white-space: pre-wrap; font-size:0.95rem; line-height:1.6; border:1px solid #e9ecef; max-height: 250px; overflow-y: auto;">
                                    {{ $copyParaSocial }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Row Interna para dividirmos Headline e Locucao dinamicamente --}}
                    <div class="col-12">
                        <div class="row g-4 h-100 align-items-stretch">

                            {{-- Copy Headline --}}
                            <div class="col-md-6 mb-md-0 mb-4 d-flex">
                                @if(!empty($campaign->copy))
                                    <div class="card border-0 shadow-sm w-100" style="border-radius:16px;">
                                        <div
                                            class="card-header pb-2 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark font-weight-bolder">
                                                <i class="fas fa-heading text-info fs-5 me-2 align-middle"></i>
                                                Headline da Arte
                                            </h6>
                                            <button class="btn btn-sm btn-outline-secondary mb-0 copy-btn shadow-sm"
                                                onclick="copyToClipboard('copyHeadline', this)">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="copyHeadline" class="p-3 bg-gray-100 border-radius-lg text-dark h-100"
                                                style="white-space:pre-wrap; font-size:0.95rem; border:1px solid #e9ecef; max-height: 250px; overflow-y: auto;">
                                                {{ $campaign->copy }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card border-0 shadow-none border w-100 opacity-7"
                                        style="border-radius:16px; min-height: 120px;">
                                        <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                                            <i class="fas fa-heading text-muted opacity-5 mb-2 fs-3"></i>
                                            <span class="text-sm text-muted">A campanha não possui texto para arte visual</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Copy Locucao --}}
                            <div class="col-md-6 d-flex">
                                @if(!empty($campaign->copy_locucao))
                                    <div class="card border-0 shadow-sm w-100" style="border-radius:16px;">
                                        <div
                                            class="card-header pb-2 px-4 bg-transparent border-bottom d-flex justify-content-between align-items-center">
                                            <h6 class="mb-0 text-dark font-weight-bolder">
                                                <i class="fas fa-microphone-alt text-warning fs-5 me-2 align-middle"></i>
                                                Roteiro de Locução
                                            </h6>
                                            <button class="btn btn-sm btn-outline-secondary mb-0 copy-btn shadow-sm"
                                                onclick="copyToClipboard('copyLocucao', this)">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                        <div class="card-body p-3">
                                            <div id="copyLocucao" class="p-3 bg-gray-100 border-radius-lg text-dark h-100"
                                                style="white-space:pre-wrap; font-size:0.95rem; border:1px solid #e9ecef; max-height: 250px; overflow-y: auto;">
                                                {{ $campaign->copy_locucao }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="card border-0 shadow-none border w-100 opacity-7"
                                        style="border-radius:16px; min-height: 120px;">
                                        <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                                            <i class="fas fa-microphone-alt text-muted opacity-5 mb-2 fs-3"></i>
                                            <span class="text-sm text-muted">A campanha não possui locução habilitada</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        </div>
                    </div>

                    {{-- Compartilhamento Social --}}
                    <div class="col-12 mt-4">
                        <div class="card border-0 shadow-sm" style="border-radius:16px;">
                            <div class="card-header pb-2 px-4 bg-transparent border-bottom">
                                <h6 class="mb-0 text-dark font-weight-bolder">
                                    <i class="fas fa-share-alt text-primary fs-5 me-2 align-middle"></i>
                                    Enviar para Redes Sociais
                                </h6>
                            </div>
                            <div class="card-body p-4">
                                @if(count($socialAccounts) > 0)
                                    <form action="{{ route('lojista.maxdivulga.canais.publish', $campaign->id) }}" method="POST">
                                        @csrf
                                        <div class="row align-items-end g-3">
                                            <div class="col-md-4">
                                                <label class="form-label text-xs font-weight-bold">Selecione o Canal (Facebook)</label>
                                                <select name="target_info" class="form-select form-select-sm" required onchange="updateTargetType(this)">
                                                    <option value="" disabled selected>Escolha onde postar...</option>
                                                    
                                                    @php
                                                        $fbAccounts = $socialAccounts->where('provider', 'facebook');
                                                        $tgAccounts = $socialAccounts->where('provider', 'telegram');
                                                    @endphp

                                                    @if($fbAccounts->count() > 0)
                                                        @foreach($fbAccounts as $account)
                                                            <optgroup label="Facebook: {{ $account->meta_data['name'] ?? 'Conta' }}">
                                                                @if(!empty($account->meta_data['pages']))
                                                                    @foreach($account->meta_data['pages'] as $page)
                                                                        <option value="facebook|page|{{ $page['id'] }}">{{ $page['name'] }} (Página)</option>
                                                                    @endforeach
                                                                @endif
                                                                @if(!empty($account->meta_data['groups']))
                                                                    @foreach($account->meta_data['groups'] as $group)
                                                                        <option value="facebook|group|{{ $group['id'] }}">{{ $group['name'] }} (Grupo)</option>
                                                                    @endforeach
                                                                @endif
                                                            </optgroup>
                                                        @endforeach
                                                    @endif

                                                    @if($tgAccounts->count() > 0)
                                                        <optgroup label="Telegram">
                                                            @foreach($tgAccounts as $account)
                                                                <option value="telegram|{{ $account->meta_data['type'] ?? 'group' }}|{{ $account->provider_id }}">{{ $account->meta_data['name'] ?? 'Chat' }}</option>
                                                            @endforeach
                                                        </optgroup>
                                                    @endif
                                                </select>
                                                <input type="hidden" name="provider" id="social_provider">
                                                <input type="hidden" name="target_type" id="social_target_type">
                                                <input type="hidden" name="target_id" id="social_target_id">
                                            </div>
                                            <div class="col-md-3">
                                                <button type="submit" class="btn btn-primary btn-sm mb-0 w-100 shadow-sm">
                                                    <i class="fas fa-paper-plane me-1"></i> Publicar Agora
                                                </button>
                                            </div>
                                            <div class="col-md-5">
                                                <p class="text-xs text-muted mb-0">
                                                    <i class="fas fa-info-circle me-1"></i> 
                                                    A arte e a <strong>Copy Social</strong> serão enviadas.
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                @else
                                    <div class="text-center py-3">
                                        <p class="text-sm text-muted mb-3">Você ainda não conectou nenhuma rede social.</p>
                                        <a href="{{ route('lojista.maxdivulga.canais.index') }}" class="btn btn-outline-primary btn-sm mb-0">
                                            <i class="fas fa-plus me-1"></i> Conectar Redes Sociais
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
        @endif
    </div>

    <style>
        .copy-btn {
            padding: 0.35rem 0.8rem;
            font-size: 0.75rem;
            border-radius: 6px;
        }

        .pulse-btn {
            animation: pulse 2s infinite;
        }

        .bg-gray-100 {
            background-color: #f8f9fa !important;
        }

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.4);
            }

            70% {
                box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
            }
        }
    </style>

    <script>
        function copyToClipboard(elementId, btnEle) {
            var content = document.getElementById(elementId).innerText;
            navigator.clipboard.writeText(content).then(function () {
                var originalHtml = btnEle.innerHTML;
                btnEle.innerHTML = '<i class="fas fa-check text-success"></i>';
                btnEle.classList.add('border-success');
                setTimeout(() => {
                    btnEle.innerHTML = originalHtml;
                    btnEle.classList.remove('border-success');
                }, 2000);
            });
        }

        function updateTargetType(select) {
            const val = select.value.split('|');
            if (val.length === 3) {
                document.getElementById('social_provider').value = val[0];
                document.getElementById('social_target_type').value = val[1];
                document.getElementById('social_target_id').value = val[2];
            }
        }
    </script>
@endsection