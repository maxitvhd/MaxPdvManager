@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12 col-md-8 m-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 d-flex justify-content-between">
                    <h6>Editar Campanha: {{ $campaign->name }}</h6>
                    <a href="{{ route('lojista.maxdivulga.index') }}" class="btn btn-sm btn-secondary">Voltar</a>
                </div>
                <div class="card-body mt-3">
                    <form action="{{ route('lojista.maxdivulga.update', $campaign->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info text-white text-sm">
                            Para preservar as peças e os textos gerados pela IA, a edição de campanhas existentes permite
                            apenas a alteração do Nome e Status. Para modificar canais, produtos ou temas, crie uma nova
                            campanha e a IA irá gerar novos anúncios do zero.
                        </div>

                        <div class="form-group mb-3">
                            <label>Nome da Campanha</label>
                            <input type="text" name="name" class="form-control" value="{{ $campaign->name }}" required>
                        </div>
                        <div class="form-group mb-3">
                            <label>Status</label>
                            <select name="status" class="form-control">
                                <option value="active" {{ $campaign->status == 'active' ? 'selected' : '' }}>Ativa</option>
                                <option value="paused" {{ $campaign->status == 'paused' ? 'selected' : '' }}>Pausada</option>
                                <option value="finished" {{ $campaign->status == 'finished' ? 'selected' : '' }}>Finalizada
                                </option>
                            </select>
                        </div>

                        <div class="text-end mt-4">
                            <button type="submit" class="btn bg-gradient-success">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection