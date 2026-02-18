@extends('layouts.user_type.auth')

@section('content')
    <h1>Máquinas Conectadas</h1>
    <a href="{{ route('checkouts.create') }}" class="btn btn-primary">Adicionar Máquina</a>
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Licença</th>
                <th>Descrição</th>
                <th>IP</th>
                <th>Sistema Operacional</th>
                <th>Hardware</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($checkouts as $checkout)
                <tr>
                    <td>{{ $checkout->id }}</td>
                    <td>{{ $checkout->licenca->codigo ?? 'N/A' }}</td>
                    <td>{{ $checkout->descricao }}</td>
                    <td>{{ $checkout->ip }}</td>
                    <td>{{ $checkout->sistema_operacional }}</td>
                    <td>{{ $checkout->hardware }}</td>
                    <td>
                        <a href="{{ route('checkouts.edit', $checkout->id) }}" class="btn btn-warning">Editar</a>
                        <form action="{{ route('checkouts.destroy', $checkout->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
