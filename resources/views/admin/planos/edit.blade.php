@extends('layouts.user_type.auth')

@section('content')

    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card mb-4">

                <div class="card-header pb-0 border-bottom">
                    <h6 class="mb-0">Editar Plano: {{ $plano->nome }}</h6>
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

                    <form action="{{ route('planos.update', $plano->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-control-label">Nome do Plano</label>
                                    <input class="form-control" type="text" name="nome"
                                        value="{{ old('nome', $plano->nome) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Valor (R$)</label>
                                    <input class="form-control" type="number" step="0.01" name="valor"
                                        value="{{ old('valor', $plano->valor) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Validade (Meses)</label>
                                    <input class="form-control" type="number" min="1" name="meses_validade"
                                        value="{{ old('meses_validade', $plano->meses_validade) }}" required>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="form-control-label">Limite de Dispositivos PDV</label>
                                    <input class="form-control" type="number" min="1" name="limite_dispositivos"
                                        value="{{ old('limite_dispositivos', $plano->limite_dispositivos) }}" required>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('planos.index') }}" class="btn btn-light btn-md mb-0 me-2">Cancelar</a>
                            <button type="submit" class="btn bg-gradient-primary btn-md mb-0">Atualizar Plano</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>

@endsection