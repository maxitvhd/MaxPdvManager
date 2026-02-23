@extends('layouts.user_type.auth')

@section('content')
    <div class="onboarding-bg">
        <!-- Efeitos de Luz de Fundo (Mockup Style) -->
        <div class="light-orb"></div>

        <div class="container min-vh-100 d-flex align-items-center justify-content-center py-5">
            <div class="row justify-content-center w-100">
                <div class="col-lg-10">
                    <div class="premium-glass-card animate-fade-in">

                        <div class="text-center mb-5">
                            <div class="icon-orb mx-auto mb-4">
                                <i class="fas fa-rocket fa-2x text-white"></i>
                            </div>
                            <h1 class="display-5 text-white font-weight-bolder mb-2 mt-3">
                                Bem-vindo ao <span class="text-gradient">MaxCheckout</span>
                            </h1>
                            <p class="text-white-50 lead mx-auto" style="max-width: 600px;">
                                Olá, <strong>{{ Auth::user()->name }}</strong>! Vamos preparar o seu arsenal para o sucesso.
                                Siga os passos abaixo.
                            </p>
                        </div>

                        <div class="row g-4 justify-content-center mt-2">
                            <!-- Step 1: Criar Loja -->
                            <div class="col-md-5">
                                <div class="step-card h-100 p-4 transition-all">
                                    <div class="step-check"><i class="fas fa-check"></i></div>
                                    <div class="mb-3 mt-2">
                                        <i class="fas fa-store fa-2x gradient-icon"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bold mb-2">1. Criar Loja</h5>
                                    <p class="text-white-50 text-sm mb-4">Configure sua unidade de varejo para começar a
                                        vender hoje mesmo.</p>
                                    <a href="{{ route('lojas.create') }}"
                                        class="btn btn-outline-white w-100 rounded-pill">COMEÇAR <i
                                            class="fas fa-arrow-right ms-2 mt-1"></i></a>
                                </div>
                            </div>

                            <!-- Step 2: Comprar Planos -->
                            <div class="col-md-5">
                                <div class="step-card h-100 p-4 transition-all in-progress">
                                    <div class="mb-3 mt-2">
                                        <i class="fas fa-shopping-cart fa-2x gradient-icon-alt"></i>
                                    </div>
                                    <h5 class="text-white font-weight-bold mb-2">2. Comprar Planos</h5>
                                    <p class="text-white-50 text-sm mb-4">Escolha a melhor licença para blindar o seu
                                        estoque e equipe.</p>
                                    <a href="{{ route('licencas.index') }}"
                                        class="btn btn-outline-white w-100 rounded-pill">CONFIGURAR</a>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-5 pt-4 border-top border-white border-opacity-10">
                            <p class="text-white-50 text-sm">
                                Precisa de ajuda com o seu arsenal técnico?
                                <a href="#" class="text-white font-weight-bold ms-1">Fale com o Suporte</a>
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .onboarding-bg {
            background-color: #0d1222;
            /* Fundo Ultra Dark */
            min-height: 100vh;
            width: 100%;
            position: relative;
            overflow: hidden;
        }

        /* O Grande Círculo de Luz (Mockup Style) */
        .light-orb {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.45) 0%, rgba(168, 85, 247, 0.2) 40%, transparent 75%);
            filter: blur(80px);
            z-index: 0;
            animation: orb-pulse 6s infinite alternate;
        }

        @keyframes orb-pulse {
            from {
                opacity: 0.6;
                transform: translate(-50%, -50%) scale(0.9);
            }

            to {
                opacity: 1;
                transform: translate(-50%, -50%) scale(1.1);
            }
        }

        .premium-glass-card {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(30px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 35px;
            padding: 60px;
            position: relative;
            z-index: 2;
            box-shadow: 0 40px 100px -20px rgba(0, 0, 0, 0.8);
        }

        .icon-orb {
            width: 90px;
            height: 90px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .text-gradient {
            background: linear-gradient(135deg, #818cf8 0%, #c084fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .step-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.06);
            border-radius: 20px;
            position: relative;
        }

        .step-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-8px);
            border-color: rgba(129, 140, 248, 0.3);
        }

        .step-check {
            position: absolute;
            top: 15px;
            right: 15px;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.8rem;
        }

        .gradient-icon {
            background: linear-gradient(135deg, #4f46e5 0%, #9333ea 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-icon-alt {
            background: linear-gradient(135deg, #0ea5e9 0%, #4f46e5 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .btn-outline-white {
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 1px;
            padding: 12px;
            transition: all 0.3s;
        }

        .btn-outline-white:hover {
            background: white !important;
            color: #0f172a !important;
            transform: scale(1.02);
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
@endsection