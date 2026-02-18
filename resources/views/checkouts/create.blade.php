@extends('layouts.user_type.auth')

@section('content')
    <h1>Registrar Máquina</h1>
    <form action="{{ route('checkouts.store') }}" method="POST">
        @csrf
        <div class="form-group">
            <label for="licenca_id">Licença:</label>
            <select name="licenca_id" id="licenca_id" class="form-control" required>
                <option value="">Selecione uma licença</option>
                @foreach($licencas as $licenca)
                    <option value="{{ $licenca->id }}">{{ $licenca->codigo }} - {{ $licenca->descricao }}</option>
                @endforeach
            </select>
        </div>

        <div class="form-group">
            <label for="codigo">Código:</label>
            <input type="text" name="codigo" id="codigo" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="descricao">Descrição:</label>
            <input type="text" name="descricao" id="descricao" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="ip">IP:</label>
            <input type="text" name="ip" id="ip" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="sistema_operacional">Sistema Operacional:</label>
            <input type="text" name="sistema_operacional" id="sistema_operacional" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="hardware">Hardware:</label>
            <input type="text" name="hardware" id="hardware" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Registrar Máquina</button>
    </form>
@endsection
