@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card mb-4">

                <div class="card-header pb-0 border-bottom">
                    <h6 class="mb-0">Editar Adicional / Extra</h6>
                </div>

                <div class="card-body">
                    @if($errors->any())
                        <div class="alert alert-danger text-white">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('adicionais.update', $adicional->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label class="form-control-label">Nome do Adicional</label>
                                    <input class="form-control" type="text" name="nome"
                                        value="{{ old('nome', $adicional->nome) }}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Tipo</label>
                                    <select name="tipo" class="form-control">
                                        <option value="dispositivo" {{ $adicional->tipo == 'dispositivo' ? 'selected' : '' }}>
                                            Expansão de Dispositivo PDV</option>
                                        <option value="modulo" {{ $adicional->tipo == 'modulo' ? 'selected' : '' }}>Módulo de
                                            Sistema</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Descrição Mínima</label>
                                    <textarea class="form-control" name="descricao"
                                        rows="2">{{ old('descricao', $adicional->descricao) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-control-label">Valor Adicional (R$)</label>
                                    <input class="form-control" type="number" step="0.01" name="valor"
                                        value="{{ old('valor', $adicional->valor) }}" required>
                                </div>
                            </div>

                            <div class="col-md-6 d-flex align-items-center pt-4">
                                <div class="form-check form-switch ps-0">
                                    <input class="form-check-input ms-auto" type="checkbox" id="statusCheck" name="status"
                                        {{ $adicional->status ? 'checked' : '' }}>
                                    <label class="form-check-label text-body ms-3 text-truncate w-80 mb-0"
                                        for="statusCheck">Ativo para Vendas</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('adicionais.index') }}" class="btn btn-light btn-md mb-0 me-2">Cancelar</a>
                            <button type="submit" class="btn bg-gradient-primary btn-md mb-0">Atualizar Adicional</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection