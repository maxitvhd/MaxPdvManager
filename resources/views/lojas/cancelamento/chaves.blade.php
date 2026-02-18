@extends('layouts.user_type.auth')
@section('content')

<div class="container">
    <h3>Chaves de Cancelamento - {{ $loja->nome }}</h3>
    <a href="{{ route('lojas.cancelamento.chaves.create', $loja->id) }}" class="btn btn-primary">Adicionar Chave</a>
    <table class="table">
        <thead>
            <tr>
                <th>Funcionário</th>
                <th>Chave</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($chaves as $chave)
                <tr>
                    <td>{{ $chave->user->name }}</td>
                    <td>{{ $chave->chave }}</td>
                    <td>
                        <a href="{{ route('lojas.cancelamento.chaves.edit', [$loja->id, $chave->id]) }}" class="btn btn-sm btn-primary">Editar</a>
                        <form action="{{ route('lojas.cancelamento.chaves.destroy', [$loja->id, $chave->id]) }}" method="POST" style="display:inline;">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection