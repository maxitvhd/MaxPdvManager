@extends('layouts.user_type.auth')

@section('content')
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h3 class="mb-0 text-center">{{ $produto->nome }}</h3>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Descrição:</strong> {{ $produto->descricao }}</p>
                                <p><strong>Preço de Venda:</strong> R$ {{ number_format($produto->preco, 2, ',', '.') }}</p>
                                <p><strong>Preço de Compra:</strong> R$ {{ number_format($produto->preco_compra, 2, ',', '.') }}</p>
                                <p><strong>Código de Barras:</strong> {{ $produto->codigo_barra }}</p>
                                <p><strong>Estoque:</strong> {{ $produto->estoque }}</p>
                                <p><strong>Peso:</strong> {{ $produto->peso }} kg</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Tamanho:</strong> {{ $produto->tamanho }}</p>
                                <p><strong>Validade:</strong> {{ $produto->validade ? date('d/m/Y', strtotime($produto->validade)) : 'N/A' }}</p>
                                <p><strong>Código NFe:</strong> {{ $produto->codigo_nfe }}</p>
                                <p><strong>Responsável:</strong> {{ $produto->user->name ?? 'Não informado' }}</p>
                                @if ($produto->imagem)
                                    <div class="text-center mt-3">
                                        <img src="{{ asset('storage/usuario/' . Auth::user()->codigo . '/produtos/' . $produto->imagem) }}" 
                                             alt="{{ $produto->nome }}" 
                                             class="img-thumbnail" 
                                             style="max-width: 200px;">
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-center">
                        <a href="{{ route('produtos.index') }}" class="btn btn-secondary">Voltar</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection