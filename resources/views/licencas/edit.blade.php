@extends('layouts.user_type.auth')

@section('content')
    <div class="container">
        <h2>Editar Licença</h2>
        <form action="{{ route('licencas.update', $licenca->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="codigo">Código:</label>
                <input type="text" class="form-control" id="codigo" name="codigo" value="{{ $licenca->codigo }}" readonly>
            </div>

            <div class="form-group">
                <label for="codigo">Chave Key:</label>
                <input type="password" class="form-control" id="codigo" name="codigo" value="{{ $licenca->key }}" readonly>
            </div>

            <div class="form-group">
                <label for="descricao">Descrição:</label>
                <input type="text" class="form-control" id="descricao" name="descricao" value="{{ $licenca->descricao }}"
                    required>
            </div>
            @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                <div class="form-group">
                    <label for="validade">Validade:</label>
                    <input type="date" class="form-control" id="validade" name="validade"
                        value="{{ \Carbon\Carbon::parse($licenca->validade)->format('Y-m-d') }}" required>
                </div>
                <div class="form-group">
                    <label for="status" class="form-label">Status da Licença</label>
                    <select class="form-control @error('status') is-invalid @enderror" name="status" id="status" required>
                        <option value="ativo" {{ old('status', $licenca->status) == 'ativo' ? 'selected' : '' }}>Ativo</option>
                        <option value="inativo" {{ old('status', $licenca->status) == 'inativo' ? 'selected' : '' }}>Inativo
                        </option>
                    </select>
                    @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <div class="form-group">
                <label for="loja_id">Vinculada a Loja:</label>
                <select class="form-control" id="loja_id" name="loja_id" required>
                    @foreach ($lojas as $loja)
                        <option value="{{ $loja->id }}" {{ $licenca->loja_id == $loja->id ? 'selected' : '' }}>{{ $loja->nome }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-success">Atualizar</button>
        </form>
    </div>
@endsection