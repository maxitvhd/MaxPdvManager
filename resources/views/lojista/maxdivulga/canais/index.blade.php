@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card mb-4">
                <div class="card-header pb-0">
                    <div class="d-flex flex-row justify-content-between">
                        <div>
                            <h5 class="mb-0">Canais de Envio Sociais</h5>
                            <p class="text-sm">Conecte suas redes para enviar as artes do MaxDivulga automaticamente.</p>
                        </div>
                    </div>
                </div>
                <div class="card-body px-4 pt-4 pb-2">
                    @if(session('success'))
                        <div class="alert alert-success">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <div class="row mt-3">
                        <!-- Facebook Card -->
                        <div class="col-md-4">
                            <div class="card border border-primary shadow-none">
                                <div class="card-body text-center">
                                    <i class="fab fa-facebook text-primary mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="mb-1">Facebook Pages & Groups</h6>

                                    @php
                                        $fbAccount = $socialAccounts->where('provider', 'facebook')->first();
                                    @endphp

                                    @if($fbAccount)
                                        <div class="badge badge-sm bg-gradient-success mb-3">Conectado</div>
                                        <p class="text-xs text-muted">Logado como:
                                            {{ $fbAccount->meta_data['name'] ?? 'Usuário FB' }}</p>

                                        <ul class="list-group list-group-flush mb-3">
                                            @if(!empty($fbAccount->meta_data['pages']))
                                                <li class="list-group-item text-xs p-1">Páginas:
                                                    {{ count($fbAccount->meta_data['pages']) }}</li>
                                            @endif
                                            @if(!empty($fbAccount->meta_data['groups']))
                                                <li class="list-group-item text-xs p-1">Grupos:
                                                    {{ count($fbAccount->meta_data['groups']) }}</li>
                                            @endif
                                        </ul>

                                        <form action="{{ route('lojista.maxdivulga.canais.disconnect', 'facebook') }}"
                                            method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-outline-danger btn-sm w-100">Desconectar</button>
                                        </form>
                                    @else
                                        <p class="text-xs text-muted mb-4">Poste suas ofertas em suas páginas e grupos
                                            automaticamente.</p>
                                        <a href="{{ route('lojista.maxdivulga.canais.auth', 'facebook') }}"
                                            class="btn bg-gradient-primary btn-sm w-100">Conectar Facebook</a>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Placeholder for Instagram -->
                        <div class="col-md-4">
                            <div class="card border border-info shadow-none opacity-6">
                                <div class="card-body text-center">
                                    <i class="fab fa-instagram text-info mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="mb-1">Instagram Business</h6>
                                    <p class="text-xs text-muted">Postagem de Feed e Stories.</p>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>Em breve</button>
                                </div>
                            </div>
                        </div>

                        <!-- Placeholder for Google -->
                        <div class="col-md-4">
                            <div class="card border border-danger shadow-none opacity-6">
                                <div class="card-body text-center">
                                    <i class="fab fa-google text-danger mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="mb-1">Google My Business</h6>
                                    <p class="text-xs text-muted">Postagem de Atualizações Local.</p>
                                    <button class="btn btn-secondary btn-sm w-100" disabled>Em breve</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection