@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">

                <div class="card-header pb-0 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0">Gerenciamento Global de Faturas (Admin)</h6>
                    @include('components.loja-selector')
                </div>

                <div class="card-body px-0 pt-0 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success text-white m-3 text-sm">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger text-white m-3 text-sm">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="table-responsive p-0 mt-3">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"># ID /
                                        Data</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        Loja / Responsável</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Vencimento</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valor</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagamentos as $pag)
                                    <tr>
                                        <td>
                                            <div class="d-flex px-3 py-1">
                                                <div class="d-flex flex-column justify-content-center">
                                                    <h6 class="mb-0 text-sm">#F-{{ str_pad($pag->id, 5, '0', STR_PAD_LEFT) }}
                                                    </h6>
                                                    <small
                                                        class="text-xs text-muted">{{ $pag->created_at->format('d/m/Y') }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $pag->licenca->loja->nome ?? 'N/A' }}</h6>
                                                <small
                                                    class="text-xs text-secondary">{{ $pag->licenca->loja->email ?? '' }}</small>
                                            </div>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span
                                                class="text-secondary text-xs font-weight-bold">{{ \Carbon\Carbon::parse($pag->data_proximo_pagamento)->format('d/m/Y') }}</span>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span class="text-secondary text-xs font-weight-bold">R$
                                                {{ number_format($pag->valor, 2, ',', '.') }}</span>
                                        </td>

                                        <td class="align-middle text-center text-sm">
                                            @if($pag->status === 'pago')
                                                <span class="badge badge-sm bg-gradient-success">Pago</span>
                                            @elseif($pag->status === 'atrasado')
                                                <span class="badge badge-sm bg-gradient-danger">Atrasado</span>
                                            @elseif($pag->status === 'cancelado')
                                                <span class="badge badge-sm bg-gradient-dark">Cancelada</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-warning">Pendente</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if($pagamentos->isEmpty())
                                    <tr>
                                        <td colspan="5" class="text-center py-4">Nenhuma fatura encontrada.</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>

                        <div class="d-flex justify-content-center mt-4">
                            {{ $pagamentos->links('pagination::bootstrap-4') }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection