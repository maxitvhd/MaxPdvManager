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

                        <!-- Telegram Card -->
                        <div class="col-md-4">
                            <div class="card border border-info shadow-none">
                                <div class="card-body text-center">
                                    <i class="fab fa-telegram text-info mb-3" style="font-size: 3rem;"></i>
                                    <h6 class="mb-1">Telegram Channels/Groups</h6>

                                    <p class="text-xs text-muted mb-3">Envie ofertas para seus clientes no Telegram.</p>

                                    @php
                                        $telegramAccounts = $socialAccounts->where('provider', 'telegram');
                                    @endphp

                                    @if($telegramAccounts->count() > 0)
                                        <div class="text-start mb-3">
                                            <p class="text-xs font-weight-bold mb-1">Canais Conectados:</p>
                                            @foreach($telegramAccounts as $acc)
                                                <div class="d-flex justify-content-between align-items-center mb-1 p-1 bg-light border-radius-sm">
                                                    <span class="text-xs">{{ $acc->meta_data['name'] ?? $acc->provider_id }}</span>
                                                    <form action="{{ route('lojista.maxdivulga.canais.disconnect', 'telegram') }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="provider_id" value="{{ $acc->provider_id }}">
                                                        <button type="submit" class="btn btn-link text-danger p-0 m-0" title="Remover"><i class="fa fa-trash"></i></button>
                                                    </form>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <button class="btn bg-gradient-info btn-sm w-100" data-bs-toggle="modal" data-bs-target="#connectTelegramModal">
                                        {{ $telegramAccounts->count() > 0 ? 'Adicionar Outro' : 'Conectar Telegram' }}
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Modal Conectar Telegram -->
                        <div class="modal fade" id="connectTelegramModal" tabindex="-1" role="dialog" aria-labelledby="connectTelegramModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="connectTelegramModalLabel">Conectar Grupo/Canal Telegram</h5>
                                        <button type="button" class="btn-close text-dark" data-bs-dismiss="modal" aria-label="Close">
                                            <span aria-hidden="true">&times;</span>
                                        </button>
                                    </div>
                                    <form action="{{ route('lojista.maxdivulga.canais.telegram') }}" method="POST">
                                        @csrf
                                        <div class="modal-body">
                                            <div class="alert alert-info text-white text-xs">
                                                <strong>Como conectar:</strong><br>
                                                1. Adicione o nosso bot no seu grupo ou canal.<br>
                                                2. Obtenha o <strong>Chat ID</strong> do seu grupo (pode usar bots como @userinfobot ou @GetIDsBot).<br>
                                                3. Insira o ID e o nome abaixo.
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="chat_name">Nome para Identificação (ex: Grupo de Ofertas)</label>
                                                <input type="text" name="chat_name" id="chat_name" class="form-control" placeholder="Meu Grupo" required>
                                            </div>
                                            <div class="form-group mb-3">
                                                <label for="chat_id">Chat ID do Telegram</label>
                                                <input type="text" name="chat_id" id="chat_id" class="form-control" placeholder="Ex: -100123456789" required>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-link text-dark ml-auto" data-bs-dismiss="modal">Fechar</button>
                                            <button type="submit" class="btn bg-gradient-info">Salvar Canal</button>
                                        </div>
                                    </form>
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