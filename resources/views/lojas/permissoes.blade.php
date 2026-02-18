{{--pagina descontinuado , 31/12/2025 criado o funcionarios permissões --}}


@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h1>Gerenciar Permissões da Loja: {{ $loja->nome }}</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('lojas.storePermissao', $loja->id) }}">
        @csrf
        <div class="mb-3">
            <label for="codigo" class="form-label">Código do Usuário</label>
            <input type="text" class="form-control" id="codigo" name="codigo" required>
            @error('codigo')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <div class="mb-3">
            <label for="role" class="form-label">Função</label>
            <select class="form-control" id="role" name="role" required>
                <option value="dono">Dono</option>
                <option value="contador">Contador</option>
                <option value="funcionario">Funcionário</option>
            </select>
            @error('role')
                <span class="text-danger">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary">Adicionar Permissão</button>
    </form>

    <h3 class="mt-4">Usuários com Permissão</h3>
    <table class="table">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Código</th>
                <th>Função</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loja->permissoes as $permissao)
                <tr>
                    <td>{{ $permissao->user->name }}</td>
                    <td>{{ $permissao->user->codigo }}</td>
                    <td>{{ $permissao->role }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('lojas.index') }}" class="btn btn-secondary mt-3">Voltar</a>
</div>
@endsection