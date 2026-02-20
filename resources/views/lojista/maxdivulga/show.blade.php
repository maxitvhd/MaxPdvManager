@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-12 col-xl-10">

                {{-- Header --}}
                <div class="d-flex align-items-center mb-4">
                    <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-sm btn-outline-secondary me-3">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <div>
                        <h4 class="mb-0 font-weight-bolder">{{ $campaign->name }}</h4>
                        <p class="text-muted text-sm mb-0">Campanha gerada em
                            {{ $campaign->created_at->format('d/m/Y \à\s H:i') }}</p>
                    </div>
                    <div class="ms-auto">
                        @if($campaign->file_path)
                            <a href="{{ route('lojista.maxdivulga.download', $campaign->id) }}" class="btn btn-success btn-sm">
                                <i class="fas fa-download me-1"></i> Baixar Arte
                            </a>
                        @endif
                    </div>
                </div>

                <div class="row g-4">

                    {{-- COLUNA ESQUERDA: Arte --}}
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm h-100" style="border-radius:16px; overflow:hidden;">
                            <div class="card-header py-2 px-4" style="background:#1a1a2e; border:none;">
                                <h6 class="text-white mb-0">🎨 Arte da Campanha</h6>
                            </div>
                            <div class="card-body p-3 d-flex align-items-center justify-content-center"
                                style="background:#f8f9fa; min-height:400px;">
                                @if($campaign->format === 'text')
                                    <div class="text-center px-4">
                                        <div style="font-size:3rem;">📝</div>
                                        <p class="text-muted text-sm mt-2">Campanha de texto — sem arte visual</p>
                                    </div>
                                @elseif($campaign->format === 'image' && $campaign->file_path)
                                    <img src="{{ asset($campaign->file_path) }}" class="img-fluid rounded shadow"
                                        alt="Arte da Campanha" style="max-height:600px; width:100%; object-fit:contain;"
                                        onerror="this.parentElement.innerHTML='<div class=\'text-center\'><div style=\'font-size:3rem;\'>⚠️</div><p class=\'text-muted text-sm\'>Arte não encontrada ou em processamento.</p></div>'">
                                @elseif($campaign->format === 'pdf' && $campaign->file_path)
                                    <iframe src="{{ asset($campaign->file_path) }}" width="100%" height="600px"
                                        style="border:none; border-radius:8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                                    </iframe>
                                @else
                                    <div class="text-center px-4">
                                        <div style="font-size:3rem;">⏳</div>
                                        <p class="text-muted text-sm mt-2">Arte ainda em processamento ou formato não suportado.
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- COLUNA DIREITA: Copy Social --}}
                    <div class="col-lg-6">
                        <div class="card border-0 shadow-sm" style="border-radius:16px; overflow:hidden;">
                            <div class="card-header py-2 px-4 d-flex justify-content-between align-items-center"
                                style="background:linear-gradient(135deg,#25d366,#128c7e); border:none;">
                                <h6 class="text-white mb-0">💬 Copy para WhatsApp / Instagram</h6>
                                <button class="btn btn-sm"
                                    style="background:rgba(255,255,255,0.2);color:#fff;border:none;border-radius:8px;"
                                    onclick="copyToClipboard()" title="Copiar texto">
                                    <i class="fas fa-copy me-1"></i> Copiar
                                </button>
                            </div>
                            <div class="card-body p-0">
                                @php
                                    $copyParaSocial = $campaign->copy_acompanhamento ?: $campaign->copy;
                                @endphp
                                @if($copyParaSocial)
                                    <div id="copyText" class="p-4"
                                        style="white-space: pre-wrap; font-family: 'Segoe UI', sans-serif; font-size:0.88rem; line-height:1.7; background:#f0fdf4; min-height:200px; color:#1a1a1a;">
                                        {{ $copyParaSocial }}</div>
                                @else
                                    <div class="p-4 text-center text-muted">
                                        <i class="fas fa-robot me-2"></i> A IA não gerou o texto de acompanhamento. Verifique as
                                        configurações da API.
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Copy INTERNA da arte (headline) — separado --}}
                        @if(!empty($campaign->copy) && $campaign->copy_acompanhamento)
                            <div class="card border-0 shadow-sm mt-3" style="border-radius:16px; overflow:hidden;">
                                <div class="card-header py-2 px-4" style="background:#6366f1; border:none;">
                                    <h6 class="text-white mb-0">🎯 Headline da Arte (já dentro da imagem)</h6>
                                </div>
                                <div class="card-body p-3" style="background:#f5f3ff;">
                                    <p class="text-sm mb-0" style="white-space:pre-wrap; color:#3730a3;">{{ $campaign->copy }}
                                    </p>
                                </div>
                            </div>
                        @endif

                        {{-- Informações da Campanha --}}
                        <div class="card border-0 shadow-sm mt-3" style="border-radius:16px;">
                            <div class="card-body py-3 px-4">
                                <h6 class="font-weight-bold mb-3">📋 Detalhes</h6>
                                <div class="row text-sm">
                                    <div class="col-6 mb-2">
                                        <span class="text-muted">Formato:</span><br>
                                        <strong>{{ strtoupper($campaign->format ?? 'N/D') }}</strong>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <span class="text-muted">Status:</span><br>
                                        <span
                                            class="badge bg-gradient-{{ $campaign->status === 'active' ? 'success' : 'secondary' }}">{{ ucfirst($campaign->status) }}</span>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <span class="text-muted">Tipo:</span><br>
                                        <strong>{{ ucfirst($campaign->type ?? '-') }}</strong>
                                    </div>
                                    <div class="col-6 mb-2">
                                        <span class="text-muted">Canais:</span><br>
                                        @foreach($campaign->channels ?? [] as $ch)
                                            <span class="badge bg-gradient-info me-1">{{ ucfirst($ch) }}</span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <script>
        function copyToClipboard() {
            const text = document.getElementById('copyText').innerText;
            navigator.clipboard.writeText(text).then(() => {
                const btn = event.target.closest('button');
                btn.innerHTML = '<i class="fas fa-check me-1"></i> Copiado!';
                btn.style.background = 'rgba(255,255,255,0.4)';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-copy me-1"></i> Copiar';
                    btn.style.background = 'rgba(255,255,255,0.2)';
                }, 2000);
            });
        }
    </script>
@endsection