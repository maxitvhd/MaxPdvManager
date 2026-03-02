@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Biblioteca de Mídias</h6>
                            <p class="text-sm mb-0">Carregue fotos e vídeos para serem usados na sua programação.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <button type="button" class="btn bg-gradient-success btn-sm mb-0" data-bs-toggle="modal" data-bs-target="#uploadMediaModal">
                                <i class="fas fa-upload me-2"></i> Fazer Upload
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row mt-4">
                        @forelse($media as $item)
                        <div class="col-xl-3 col-md-4 col-sm-6 mb-4">
                            <div class="card h-100 border-0 shadow-sm overflow-hidden">
                                <div class="position-relative">
                                    @if($item->type == 'image')
                                        <img src="{{ asset('storage/' . $item->file_path) }}" class="card-img-top" alt="{{ $item->name }}" style="height: 150px; object-fit: cover;">
                                    @else
                                        <div class="bg-dark d-flex align-items-center justify-content-center" style="height: 150px;">
                                            <i class="fas fa-video text-white fa-3x opacity-5"></i>
                                        </div>
                                    @endif
                                    <span class="badge bg-gradient-{{ $item->type == 'image' ? 'info' : 'warning' }} position-absolute top-0 end-0 m-2">
                                        {{ $item->type == 'image' ? 'Imagem' : 'Vídeo' }}
                                    </span>
                                </div>
                                <div class="card-body p-3 text-center">
                                    <h6 class="mb-0 text-sm truncate-2">{{ $item->name }}</h6>
                                    <p class="text-xs text-secondary mb-3">{{ $item->duration }} segundos</p>
                                    <button class="btn btn-outline-danger btn-xs mb-0">Excluir</button>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-images fa-4x text-secondary opacity-2 mb-3"></i>
                            <p class="text-sm text-secondary">Sua biblioteca está vazia. Comece enviando alguns arquivos.</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Upload Mídia -->
<div class="modal fade" id="uploadMediaModal" tabindex="-1" role="dialog" aria-labelledby="uploadMediaModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-radius-xl">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadMediaModalLabel">Enviar Nova Mídia</h5>
                <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('lojista.tvdoor.media.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Nome da Mídia</label>
                        <input type="text" name="name" class="form-control" placeholder="Ex: Banner Promoção Pizza" required>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Arquivo (Imagem ou Vídeo)</label>
                        <input type="file" name="file" class="form-control" accept="image/*,video/*" required>
                        <small class="text-xxs text-muted">Formatos aceitos: JPG, PNG, MP4. Máx 50MB.</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Duração de Exibição (segundos)</label>
                        <input type="number" name="duration" class="form-control" value="10" min="1" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn bg-gradient-success">Iniciar Upload</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
