@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-4 min-vh-80 d-flex align-items-center justify-content-center">
        <div class="position-absolute d-none d-lg-block"
            style="top: 10%; left: 5%; width: 300px; height: 300px; background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%); filter: blur(50px); animation: pulse 8s infinite alternate;">
        </div>
        <div class="position-absolute d-none d-lg-block"
            style="bottom: 15%; right: 10%; width: 400px; height: 400px; background: radial-gradient(circle, rgba(168, 85, 247, 0.1) 0%, transparent 70%); filter: blur(60px); animation: pulse 12s infinite alternate-reverse;">
        </div>

        <div class="row justify-content-center w-100 z-index-1">
            <div class="col-lg-10 text-center">
                <div class="card glass-card p-4 p-md-5 border-0 shadow-2xl animate-fade-in-up"
                    style="background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 40px;">

                    <div class="mb-4">
                        <div class="icon-pulse mx-auto">
                            <i class="fas fa-rocket fa-3x text-gradient"
                                style="background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent;"></i>
                        </div>
                    </div>

                    <h1 class="display-4 text-white font-weight-bolder mb-2">Bem-vindo ao <span
                            class="text-gradient-purple">MaxCheckout</span></h1>
                    <p class="text-white-50 lead mb-5 mx-auto" style="max-width: 700px;">
                        Olá, <strong>{{ Auth::user()->name }}</strong>! O seu arsenal de vendas está quase pronto. Siga os
                        passos abaixo para blindar o seu varejo.
                    </p>

                    <div class="row g-4 mb-5">
                        <!-- Step 1: Loja -->
                        <div class="col-md-4">
                            <div class="onboarding-step h-100 p-4 rounded-4 text-start transition-all">
                                <div class="step-number mb-3">01</div>
                                <h5 class="text-white mb-3"><i class="fas fa-store me-2" style="color: #6366f1;"></i> Criar
                                    Loja</h5>
                                <p class="text-sm text-white-50 mb-4">Adicione sua unidade física para gerenciar estoque e
                                    terminais.</p>
                                <a href="{{ route('lojas.create') }}"
                                    class="btn btn-sm btn-outline-primary w-100 rounded-pill">Começar <i
                                        class="fas fa-arrow-right ms-1"></i></a>
                            </div>
                        </div>

                        <!-- Step 2: Licença -->
                        <div class="col-md-4">
                            <div class="onboarding-step h-100 p-4 rounded-4 text-start transition-all">
                                <div class="step-number mb-3">02</div>
                                <h5 class="text-white mb-3"><i class="fas fa-key me-2" style="color: #a855f7;"></i> Ativar
                                    Licença</h5>
                                <p class="text-sm text-white-50 mb-4">Escolha um plano e gere sua chave mestre de ativação.
                                </p>
                                <a href="{{ route('licencas.create') }}"
                                    class="btn btn-sm btn-outline-purple w-100 rounded-pill">Configurar</a>
                            </div>
                        </div>

                        <!-- Step 3: PDV -->
                        <div class="col-md-4">
                            <div class="onboarding-step h-100 p-4 rounded-4 text-start transition-all">
                                <div class="step-number mb-3">03</div>
                                <h5 class="text-white mb-3"><i class="fas fa-desktop me-2" style="color: #22d3ee;"></i>
                                    Conectar PDV</h5>
                                <p class="text-sm text-white-50 mb-4">Baixe o MaxOS e conecte seus terminais de venda.</p>
                                <a href="{{ url('/app') }}" target="_blank"
                                    class="btn btn-sm btn-outline-info w-100 rounded-pill">Acessar Guia</a>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-top border-white border-opacity-10 mt-auto">
                        <p class="text-white-50 text-sm mb-0">Precisa de ajuda com o seu arsenal técnico? <a href="#"
                                class="text-white font-weight-bold">Fale com o Suporte</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .min-vh-80 {
            min-height: 80vh;
        }

        .text-gradient-purple {
            background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .onboarding-step {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            position: relative;
            overflow: hidden;
        }

        .onboarding-step:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        }

        .step-number {
            font-size: 2rem;
            font-weight: 900;
            opacity: 0.1;
            position: absolute;
            top: 10px;
            right: 20px;
            color: white;
        }

        .icon-pulse {
            width: 100px;
            height: 100px;
            background: rgba(99, 102, 241, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
            animation: pulse-ring 2s infinite;
        }

        @keyframes pulse-ring {
            0% {
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4);
            }

            70% {
                box-shadow: 0 0 0 20px rgba(99, 102, 241, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(99, 102, 241, 0);
            }
        }

        @keyframes pulse {
            from {
                transform: scale(1) translate(0, 0);
                opacity: 0.5;
            }

            to {
                transform: scale(1.1) translate(20px, 20px);
                opacity: 0.8;
            }
        }

        .animate-fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-outline-purple {
            color: #a855f7;
            border-color: #a855f7;
        }

        .btn-outline-purple:hover {
            background-color: #a855f7;
            color: white;
        }

        .shadow-2xl {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6);
        }
    </style>
@endsection