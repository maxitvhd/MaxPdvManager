@extends('layouts.user_type.auth')

@section('content')
    <div class="row">
        <div class="col-12 col-lg-8 mx-auto">
            <div class="card mb-4">
                <div class="card-header pb-0 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">Editar Licença</h6>
                    <span class="badge {{ $licenca->status == 'ativo' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">
                        {{ $licenca->status == 'ativo' ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>

                <div class="card-body">
                    <form action="{{ route('licencas.update', $licenca->codigo) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <!-- Detalhes da Assinatura e Integração (Read Only) -->
                        <div class="bg-gray-100 p-3 border-radius-md mb-4">
                            <h6 class="text-sm text-dark font-weight-bolder mb-3">Informações de Assinatura e API</h6>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs text-secondary mb-1">Código de Ligação / Webhook</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control text-sm font-weight-bold py-1" id="codigo"
                                            value="{{ $licenca->codigo }}" readonly>
                                        <button class="btn btn-outline-primary mb-0 px-3 py-1" type="button"
                                            onclick="copyToClipboard('{{ $licenca->codigo }}')" data-bs-toggle="tooltip"
                                            title="Copiar"><i class="fas fa-copy"></i></button>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="text-xs text-secondary mb-1">Chave Key / Criptografia</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control text-sm font-weight-bold py-1" id="key"
                                            value="{{ $licenca->key }}" readonly>
                                        <button class="btn btn-outline-primary mb-0 px-3 py-1" type="button"
                                            onclick="copyToClipboard('{{ $licenca->key }}')" data-bs-toggle="tooltip"
                                            title="Copiar"><i class="fas fa-copy"></i></button>
                                        <button class="btn btn-outline-secondary mb-0 px-3 py-1" type="button"
                                            onclick="togglePasswordVisibility('key')" data-bs-toggle="tooltip"
                                            title="Mostrar/Ocultar"><i class="fas fa-eye" id="eye-key"></i></button>
                                    </div>
                                </div>
                            </div>

                            <hr class="horizontal dark mt-2 mb-3">

                            <div class="row text-sm">
                                <div class="col-md-4">
                                    <span class="text-secondary">Plano Vigente:</span><br>
                                    <span
                                        class="font-weight-bold text-dark">{{ $licenca->plano->nome ?? 'Nenhum Plano Assinado' }}</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-secondary">Lim. Aparelhos PDV:</span><br>
                                    <span class="font-weight-bold text-dark">{{ $licenca->limite_dispositivos ?? 1 }}
                                        Terminais</span>
                                </div>
                                <div class="col-md-4">
                                    <span class="text-secondary">Módulos Extras:</span><br>
                                    @if($licenca->modulos_adicionais && count($licenca->modulos_adicionais) > 0)
                                        @foreach($licenca->modulos_adicionais as $modulo)
                                            <span class="badge bg-gradient-info">{{ $modulo }}</span>
                                        @endforeach
                                    @else
                                        <span class="font-weight-bold text-dark">Nenhum</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="descricao" class="form-control-label">Descrição (Nome
                                        fantasia/Referência)</label>
                                    <input type="text" class="form-control" id="descricao" name="descricao"
                                        value="{{ $licenca->descricao }}" required>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('super-admin'))
                            <div class="form-group">
                                <label for="validade">Validade:</label>
                                <input type="date" class="form-control" id="validade" name="validade"
                                    value="{{ \Carbon\Carbon::parse($licenca->validade)->format('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="status" class="form-label">Status da Licença</label>
                                <select class="form-control @error('status') is-invalid @enderror" name="status" id="status"
                                    required>
                                    <option value="ativo" {{ old('status', $licenca->status) == 'ativo' ? 'selected' : '' }}>Ativo
                                    </option>
                                    <option value="inativo" {{ old('status', $licenca->status) == 'inativo' ? 'selected' : '' }}>
                                        Inativo
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
                                    <option value="{{ $loja->id }}" {{ $licenca->loja_id == $loja->id ? 'selected' : '' }}>
                                        {{ $loja->nome }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <a href="{{ route('licencas.index') }}" class="btn btn-light btn-md mb-0 me-2">Cancelar</a>
                            <button type="submit" class="btn bg-gradient-primary btn-md mb-0">Atualizar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Script Utilitários da Tela -->
    <script>
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                alert('Informação copiada com sucesso: ' + text);
            });
        }
        
        function togglePasswordVisibility(inputId) {
            var input = document.getElementById(inputId);
            var icon = document.getElementById('eye-' + inputId);
            
            if (input.type === "password") {
                input.type = "text";
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = "password";
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
        
        // Tooltips Initialization
        document.addEventListener("DOMContentLoaded", function () {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            })
        });
    </script>
@endsection