@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4 mx-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">MaxDivulga - Temas</h5>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success mt-2">{{ session('success') }}</div>
                    @endif
                    <form action="{{ route('admin.maxdivulga.store_theme') }}" method="POST"
                        class="mb-4 bg-light p-3 rounded">
                        @csrf
                        <h6>Cadastrar Novo Tema (Blade Padrão)</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <label>Nome do Tema</label>
                                <input type="text" name="name" class="form-control" required
                                    placeholder="Ex: Clássico Ofertas">
                            </div>
                            <div class="col-md-6">
                                <label>Indentificador Físico (slug)</label>
                                <input type="text" name="identifier" class="form-control" required
                                    placeholder="Ex: classico_ofertas">
                                <small class="text-muted">A view correspondente deve existir em:
                                    resources/views/maxdivulga/themes/identificador.blade.php</small>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-3">Registrar Tema</button>
                    </form>

                    <h6>Temas Cadastrados</h6>
                    <table class="table align-items-center mb-0">
                        <thead>
                            <tr>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">ID</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nome</th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Identifier
                                </th>
                                <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Ativo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($themes as $theme)
                                <tr>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $theme->id }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $theme->name }}</p>
                                    </td>
                                    <td>
                                        <p class="text-xs font-weight-bold mb-0">{{ $theme->identifier }}</p>
                                    </td>
                                    <td>
                                        <span
                                            class="badge badge-sm bg-gradient-{{ $theme->is_active ? 'success' : 'secondary' }}">
                                            {{ $theme->is_active ? 'Ativo' : 'Inativo' }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-sm">Nenhum tema configurado.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection