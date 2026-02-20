@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card mb-4">

                <div class="card-header pb-0 d-flex justify-content-between align-items-center border-bottom">
                    <div>
                        <h6 class="mb-0">Minhas Faturas Mensais</h6>
                        <p class="text-sm mb-0">Acompanhe e realize o pagamento mensal de manutenção das suas Lojas e
                            Terminais de PDV.</p>
                    </div>
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
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7"># Fatura
                                    </th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Referente à Loja</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Vencimento</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Valor</th>
                                    <th
                                        class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Status</th>
                                    <th class="text-center text-secondary opacity-7 text-xs">Ação</th>
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
                                                    <small class="text-xs text-muted">Gerada em
                                                        {{ $pag->created_at->format('d/m/Y') }}</small>
                                                </div>
                                            </div>
                                        </td>

                                        <td class="align-middle text-center">
                                            <span
                                                class="text-secondary text-xs font-weight-bold">{{ $pag->licenca->loja->nome ?? 'N/A' }}</span>
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
                                                <span class="badge badge-sm bg-gradient-warning text-dark">Pendente</span>
                                            @endif
                                        </td>

                                        <td class="align-middle text-center">
                                            @if($pag->status !== 'pago' && $pag->status !== 'cancelado')
                                                <a href="{{ route('pagamentos.gerar', $pag->id) }}"
                                                    class="btn btn-sm bg-gradient-success mb-0">Pagar Agora</a>
                                            @else
                                                <button class="btn btn-sm btn-outline-secondary mb-0" disabled>Concluído</button>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                @if($pagamentos->isEmpty())
                                    <tr>
                                        <td colspan="6" class="text-center py-4">Você ainda não possui faturas.</td>
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