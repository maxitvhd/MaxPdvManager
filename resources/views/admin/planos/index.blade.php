@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">

                <div class="card-header pb-0 d-flex justify-content-between align-items-center">
                    <h6>Controle de Planos</h6>
                    <a href="{{ route('planos.create') }}" class="btn bg-gradient-primary btn-sm mb-0">
                        <i class="fas fa-plus me-2"></i> Criar Plano
                    </a>
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success text-white m-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome do
                                        Plano</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Limites</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Validade</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valor</th>
                                    <th class="text-secondary opacity-7 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($planos as $plano)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm"><i
                                                            class="fas fa-box text-primary me-2"></i>{{ $plano->nome }}</h6>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle text-center text-sm">
                                            <span class="badge badge-sm bg-gradient-info">{{ $plano->limite_dispositivos }}
                                                Dispositivos</span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">{{ $plano->meses_validade }}
                                                Mês(es)</span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">R$
                                                {{ number_format($plano->valor, 2, ',', '.') }}</span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <a href="{{ route('planos.edit', $plano->id) }}"
                                                class="text-secondary font-weight-bold text-xs me-3">
                                                <i class="fas fa-pencil-alt text-lg"></i>
                                            </a>
                                            <form action="{{ route('planos.destroy', $plano->id) }}" method="POST"
                                                class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-link text-danger font-weight-bold text-xs p-0 m-0"
                                                    onclick="return confirm('Deseja excluir este plano?')">
                                                    <i class="fas fa-trash text-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach

                                @if($planos->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Nenhum plano cadastrado.</td>
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