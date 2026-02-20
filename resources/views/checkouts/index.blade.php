@extends('layouts.user_type.auth')

@section('content')
    <h1>Máquinas Conectadas</h1>
    <a href="{{ route('checkouts.create') }}" class="btn btn-primary">Adicionar Máquina</a>
    <table class="table">
        <thead>
            <tr>
                <th>Licença</th>
                <th>Descrição / Nome</th>
                <th>Rede / IP</th>
                <th>OS</th>
                <th>Status</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($checkouts as $checkout)
                <tr>
                    <td>{{ $checkout->licenca->codigo ?? 'N/A' }}</td>
                    <td>{{ $checkout->descricao }}<br><small class="text-muted">{{ $checkout->hardware }}</small></td>
                    <td>{{ $checkout->ip }}</td>
                    <td>{{ $checkout->sistema_operacional }}</td>
                    <td>
                        <span class="badge {{ $checkout->status === 'ativo' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst($checkout->status) }}
                        </span>
                    </td>
                    <td>
                        <!-- Toggle de Status -->
                        @php
                            $podeLigar = $checkout->licenca && $checkout->licenca->isValid();
                        @endphp
                        <form action="{{ route('checkouts.toggleStatus', $checkout->id) }}" method="POST"
                            style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit"
                                class="btn btn-sm {{ $checkout->status === 'ativo' ? 'btn-outline-danger' : 'btn-outline-success' }} mb-0 me-1"
                                title="{{ $checkout->status === 'ativo' ? 'Desativar Conexão' : (!$podeLigar ? 'Licença Vencida ou Inválida' : 'Autorizar Conexão') }}"
                                {{ $checkout->status === 'inativo' && !$podeLigar ? 'disabled' : '' }}>
                                {{ $checkout->status === 'ativo' ? 'Desligar' : 'Ligar PDV' }}
                            </button>
                        </form>

                        <a href="{{ route('checkouts.edit', $checkout->id) }}"
                            class="btn btn-sm btn-warning mb-0 me-1">Editar</a>
                        <form action="{{ route('checkouts.destroy', $checkout->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger mb-0"
                                onclick="return confirm('Deseja realmente remover?')">Excluir</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection