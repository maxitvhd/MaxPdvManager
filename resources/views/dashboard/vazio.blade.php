@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center mt-5">
                <div class="card glass-card p-5 border-0 shadow-lg animate-fade-in"
                    style="background: rgba(255, 255, 255, 0.02); backdrop-filter: blur(15px); border-radius: 24px;">
                    <div class="mb-4">
                        <i class="fas fa-store-slash fa-4x text-gradient"
                            style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                    </div>
                    <h2 class="text-white font-weight-bolder">Bem-vindo ao MaxCheckout!</h2>
                    <p class="text-white-50 lead mb-4">Parece que você ainda não tem uma loja ativa ou permissão vinculada.
                    </p>

                    <div class="row g-4 mb-5 text-start">
                        <div class="col-md-6">
                            <div class="p-3 rounded-4" style="background: rgba(99, 102, 241, 0.1);">
                                <h6 class="text-white mb-2"><i class="fas fa-plus-circle me-2 text-primary"></i> Criar Loja
                                </h6>
                                <p class="text-xs text-white-50 mb-0">Adicione sua primeira unidade para começar a vender e
                                    gerenciar seu estoque.</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 rounded-4" style="background: rgba(168, 85, 247, 0.1);">
                                <h6 class="text-white mb-2"><i class="fas fa-key me-2 text-purple"></i> Ativar Licença</h6>
                                <p class="text-xs text-white-50 mb-0">Se você já tem uma loja, verifique se sua licença
                                    anual está ativa.</p>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('lojas.create') }}" class="btn btn-premium px-4 py-3">🚀 CRIAR MINHA PRIMEIRA
                            LOJA</a>
                        <a href="{{ route('licencas.index') }}" class="btn btn-outline-light rounded-pill px-4">Ver
                            Licenças</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .btn-premium {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-weight: 700;
            transition: all 0.3s;
        }

        .btn-premium:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(99, 102, 241, 0.4);
        }

        .text-purple {
            color: #a855f7;
        }
    </style>
@endsection