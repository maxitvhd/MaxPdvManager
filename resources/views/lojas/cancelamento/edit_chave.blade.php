@extends('layouts.user_type.auth')


@section('content')
<div class="container">
    <h1>Editar Chave de Cancelamento - {{ $loja->nome }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('lojas.cancelamento.chaves.update', [$loja->id, $chave->id]) }}">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="chave" class="form-label">Chave Atual</label>
            <input type="text" class="form-control" id="chave" value="{{ $chave->chave }}" disabled>
            <small class="form-text text-muted">A chave não pode ser alterada.</small>
        </div>

        <div class="mb-3">
            <label for="user_id" class="form-label">Funcionário</label>
            <select class="form-control" id="user_id" name="user_id" required>
                <option value="">Selecione um funcionário</option>
                @foreach ($funcionarios as $funcionario)
                    <option value="{{ $funcionario->id }}" {{ $funcionario->id == $chave->user_id ? 'selected' : '' }}>
                        {{ $funcionario->name }} (Código: {{ $funcionario->codigo }})
                    </option>
                @endforeach
            </select>
            <small class="form-text text-muted">Escolha o novo funcionário para esta chave.</small>
            @error('user_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Atualizar Chave</button>
        <a href="{{ route('lojas.cancelamento.chaves', $loja->id) }}" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
@endsection