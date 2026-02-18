@extends('layouts.user_type.auth')
@section('content')
<div class="container">
    <h1>Cancelamentos da Loja: {{ $loja->nome }}</h1>
    <table class="table">
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Código Venda</th>
                <th>Operador Caixa</th>
                <th>Quem Cancelou</th>
                <th>Produtos</th>
                <th>Valor Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($cancelamentos as $cancelamento)
                <tr>
                    <td>{{ $cancelamento->data_hora }}</td>
                    <td>{{ $cancelamento->codigo_venda }}</td>
                    <td>{{ $cancelamento->user->name }}</td>
                    <td>{{ $cancelamento->cancelamentoKey->user->name }}</td>
                    <td>{{ implode(', ', $cancelamento->produtos) }}</td>
                    <td>R$ {{ $cancelamento->valor_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection