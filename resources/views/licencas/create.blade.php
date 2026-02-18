
@extends('layouts.user_type.auth')

@section('content')
<div class="container">
    <h2>Nova Licença</h2>
    <form action="{{ route('licencas.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <input type="text" class="form-control" id="descricao" name="descricao" required>
        </div>
        <div class="form-group">
            <label for="validade">Validade:</label>
            <input type="date" class="form-control" id="validade" name="validade" required>
        </div>
        <div class="form-group">
            <label for="validade">Status:</label>
            <select class="form-control" name="status" id="status" required>
                    <option value="1">Ativa</option>
                    <option value="0">Inativa</option>
                </select>
        </div>
        <div class="form-group">
            <label for="loja_id">Loja:</label>
            <select class="form-control" id="loja_id" name="loja_id" required>
                @foreach ($lojas as $loja)
                    <option value="{{ $loja->id }}">{{ $loja->nome }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-success">Salvar</button>
    </form>
</div>
@endsection
