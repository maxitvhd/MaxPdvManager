@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12 col-md-8 m-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>Visualizar Campanha: {{ $campaign->name }}</h6>
                    <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
                </div>
                <div class="card-body mt-3">
                    @if($campaign->format === 'text')
                        <h5 class="text-sm">Texto Gerado pela IA:</h5>
                        <div class="p-4 bg-light border rounded">
                            {!! nl2br(e($campaign->copy)) !!}
                        </div>
                    @elseif(in_array($campaign->format, ['image', 'pdf']))
                        <div class="text-center">
                            @if($campaign->format === 'image')
                                <img src="{{ asset($campaign->file_path) }}" class="img-fluid rounded border shadow-sm"
                                    alt="Campanha Imagem" style="max-height: 800px;">
                            @else
                                <iframe src="{{ asset($campaign->file_path) }}" width="100%" height="800px" style="border: none;"
                                    class="shadow-sm rounded"></iframe>
                            @endif
                            <hr class="mt-5 mb-4">
                            <h5 class="text-sm text-start">Copy (Texto) Gerado pela IA para acompanhar a postagem:</h5>
                            <div class="p-4 bg-light border rounded text-start">
                                {!! nl2br(e($campaign->copy)) !!}
                            </div>
                        </div>
                    @else
                        <p class="text-muted">Formato não suportado ou aquivo ainda em geração.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection