@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <h2>Licenças</h2>
        <a href="{{ route('licencas.create') }}" class="btn btn-primary mb-3">Nova Licença</a>

        <div class="table-responsive">
            <table class="table table-bordered align-items-center mb-0">
                <thead>
                    <tr>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Código</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Descrição</th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Validade
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Status
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Loja
                        </th>
                        <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Ações
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($licencas as $licenca)
                        <tr>
                            <td class="align-middle text-sm"><span class="font-weight-bold">{{ $licenca->codigo }}</span></td>
                            <td class="align-middle text-sm">{{ $licenca->descricao }}</td>
                            <td class="align-middle text-center text-sm">
                                {{ \Carbon\Carbon::parse($licenca->validade)->format('d/m/Y') }}</td>
                            <td class="align-middle text-center text-sm">
                                <span
                                    class="badge badge-sm {{ $licenca->status == 'ativo' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                                    {{ $licenca->status == 'ativo' ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td class="align-middle text-center text-sm">{{ $licenca->loja->nome }}</td>
                            <td class="align-middle text-center" style="white-space: nowrap;">
                                <a href="{{ route('licencas.edit', $licenca->id) }}"
                                    class="btn btn-warning btn-sm mb-0">Editar</a>
                                <a href="{{ route('pagamentos.faturas', ['loja_codigo' => $licenca->loja->codigo ?? '']) }}"
                                    class="btn btn-success btn-sm mb-0">Faturas</a>
                                <a href="{{ route('assinaturas.index', $licenca->id) }}"
                                    class="btn btn-primary btn-sm mb-0">Mudar Plano/Extras</a>
                                <form action="{{ route('licencas.destroy', $licenca->id) }}" method="POST"
                                    style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm mb-0">Excluir</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection