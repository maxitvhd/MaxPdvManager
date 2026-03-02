@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="row">
                        <div class="col-md-8">
                            <h6>Meus Layouts</h6>
                            <p class="text-sm mb-0">Layouts criados para exibição nas suas telas TvDoor.</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('lojista.tvdoor.layouts.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                                <i class="fas fa-plus-circle me-2"></i> Criar Novo Layout
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body p-3">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show text-white" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="row mt-2">
                        @forelse($layouts as $layout)
                        <div class="col-xl-4 col-md-6 mb-4">
                            <div class="card border-0 shadow-sm h-100" style="border-radius: 12px; overflow:hidden;">
                                <div class="d-flex align-items-center justify-content-center bg-gradient-dark" style="height: 140px;">
                                    <i class="fas fa-th-large text-white fa-4x opacity-3"></i>
                                </div>
                                <div class="card-body p-3">
                                    <h6 class="font-weight-bolder mb-1">{{ $layout->name }}</h6>
                                    <p class="text-xs text-secondary mb-3">
                                        {{ count($layout->content ?? []) }} elemento(s) &bull; Criado {{ $layout->created_at->diffForHumans() }}
                                    </p>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <a href="{{ route('lojista.tvdoor.layouts.edit', $layout->id) }}" class="btn btn-sm bg-gradient-warning mb-0">
                                            <i class="fas fa-edit me-1"></i> Editar
                                        </a>
                                        <a href="{{ route('lojista.tvdoor.schedules.index') }}" class="btn btn-sm bg-gradient-success mb-0">
                                            <i class="fas fa-calendar-alt me-1"></i> Agendar
                                        </a>
                                        <form action="{{ route('lojista.tvdoor.layouts.destroy', $layout->id) }}" method="POST" class="d-inline" id="delete-layout-{{ $layout->id }}">
                                            @csrf @method('DELETE')
                                            <button type="button" class="btn btn-sm btn-outline-danger mb-0" onclick="confirmDelete('delete-layout-{{ $layout->id }}')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <i class="fas fa-th-large fa-4x text-secondary opacity-2 mb-3"></i>
                            <p class="text-sm text-secondary mb-3">Nenhum layout criado ainda.</p>
                            <a href="{{ route('lojista.tvdoor.layouts.create') }}" class="btn bg-gradient-primary btn-sm">
                                <i class="fas fa-plus-circle me-2"></i> Criar meu primeiro layout
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
