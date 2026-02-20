@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">

                <div class="card-header pb-0 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0">Gerenciamento de Extras / Adicionais</h6>
                    <a href="{{ route('adicionais.create') }}" class="btn bg-gradient-primary btn-sm mb-0">Novo
                        Adicional</a>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success text-white m-3 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif

                    <div class="table-responsive p-0 mt-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome /
                                        Descrição</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Tipo</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valor</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-secondary opacity-7"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($adicionais as $ext)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">{{ $ext->nome }}</h6>
                                                    <small
                                                        class="text-xs text-muted">{{ \Illuminate\Support\Str::limit($ext->descricao, 40) }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle text-center text-sm">
                                            @if($ext->tipo === 'dispositivo')
                                                <span class="badge badge-sm bg-gradient-info">Dispositivo PDV</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">Módulo</span>
                                            @endif
                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">R$
                                                {{ number_format($ext->valor, 2, ',', '.') }}</span>
                                        </td>

                                        <td class="align-middle text-center text-sm">
                                            @if($ext->status)
                                                <span class="badge badge-sm bg-gradient-success">Ativo</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-secondary">Inativo</span>
                                            @endif
                                        </td>

                                        <td class="align-middle text-end pe-4">
                                            <a href="{{ route('adicionais.edit', $ext->id) }}"
                                                class="text-secondary font-weight-bold text-xs me-3">
                                                Editar
                                            </a>

                                            <form action="{{ route('adicionais.destroy', $ext->id) }}" method="POST"
                                                class="d-inline" onsubmit="return confirm('Tem certeza?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-link text-danger font-weight-bold text-xs m-0 p-0">Remover</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($adicionais->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Nenhum adicional cadastrado ainda.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection