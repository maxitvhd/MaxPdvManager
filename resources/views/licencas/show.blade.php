
@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h2>Detalhes da Licença</h2>
    <ul class="list-group">
        <li class="list-group-item"><strong>Código:</strong> {{ $licenca->codigo }}</li>
        <li class="list-group-item"><strong>Chave Key:</strong> {{ $licenca->key }}</li>
        <li class="list-group-item"><strong>Descrição:</strong> {{ $licenca->descricao }}</li>
        <li class="list-group-item"><strong>Validade:</strong> {{ $licenca->validade }}</li>
        <li class="list-group-item"><strong>Status:</strong> {{ $licenca->status }}</li>
        <li class="list-group-item"><strong>Loja:</strong> {{ $licenca->loja->nome }}</li>
    </ul>
    <a href="{{ route('licencas.index') }}" class="btn btn-primary mt-3">Voltar</a>
</div>
@endsection
