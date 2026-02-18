@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h2>Licenças</h2>
    <a href="{{ route('licencas.create') }}" class="btn btn-primary">Nova Licença</a>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descrição</th>
                <th>Validade</th>
                <th>Status</th>
                <th>Loja</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($licencas as $licenca)
            <tr>
                <td>{{ $licenca->codigo }}</td>
                <td>{{ $licenca->descricao }}</td>
                <td>{{ $licenca->validade }}</td>
                <td>
    <span class="badge {{ $licenca->status == 'ativo' ? 'bg-success' : 'bg-danger' }}">
        {{ $licenca->status == 'ativo' ? 'Ativa' : 'Inativa' }}
    </span>
</td>                           <td>{{ $licenca->loja->nome }}</td>
                <td>
                    <a href="{{ route('licencas.edit', $licenca->id) }}" class="btn btn-warning">Editar</a>
                    <form action="{{ route('licencas.destroy', $licenca->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">Excluir</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
