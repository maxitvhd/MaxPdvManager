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
                                        Copy WhatsApp/Instagram</span>
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
                                        @else 🎨 @endif
                                    </span>
                                </div>
                            @endif

                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="font-weight-bolder mb-0" style="font-size:0.9rem; line-height:1.3;">{{ $camp->name }}
                                    </h6>
                                    @if($camp->status == 'active')
                                        <span class="badge bg-gradient-success ms-2" style="flex-shrink:0;">Ativa</span>
                                    @else
                                        <span class="badge bg-gradient-secondary ms-2"
                                            style="flex-shrink:0;">{{ ucfirst($camp->status) }}</span>
                                    @endif
                                </div>

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
@endsection