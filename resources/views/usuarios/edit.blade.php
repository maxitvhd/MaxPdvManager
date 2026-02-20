@extends('layouts.user_type.auth')

@section('content')

    <div>
        <div class="row">
            <div class="col-12">
                <div class="card mb-4 mx-4">
                    <div class="card-header pb-0">
                        <h5 class="mb-0">Editar Usuário: {{ $usuario->name }}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name" class="form-control-label">Nome Completo</label>
                                        <input class="form-control" type="text" placeholder="Nome Completo" id="name"
                                            name="name" value="{{ old('name', $usuario->name) }}" required>
                                        @error('name')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="email" class="form-control-label">E-mail</label>
                                        <input class="form-control" type="email" placeholder="example@email.com" id="email"
                                            name="email" value="{{ old('email', $usuario->email) }}" required>
                                        @error('email')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="phone" class="form-control-label">Telefone</label>
                                        <input class="form-control" type="text" placeholder="Telefone" id="phone"
                                            name="phone" value="{{ old('phone', $usuario->phone) }}">
                                        @error('phone')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="funcao" class="form-control-label">Função / Cargo</label>
                                        <input class="form-control" type="text" placeholder="Cargo" id="funcao"
                                            name="funcao" value="{{ old('funcao', $usuario->funcao) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="password" class="form-control-label">Nova Senha (Deixe em branco para
                                            manter a atual)</label>
                                        <input class="form-control" type="password" placeholder="Nova Senha" id="password"
                                            name="password">
                                        @error('password')
                                            <p class="text-danger text-xs mt-2">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end mt-4">
                                <a href="{{ route('usuarios.index') }}" class="btn btn-light m-0 me-2">Voltar</a>
                                <button type="submit" class="btn bg-gradient-dark m-0">Salvar Alterações</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection