<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ecossistema Max - O Futuro do Varejo Inteligente</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css" />
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/landing_page.css') }}">
    <style>
        .hero-bg-img {
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background: url('{{ asset('assets/img/landing/hero-bg.png') }}') no-repeat center center;
            background-size: cover;
            opacity: 0.15;
            z-index: -1;
            mask-image: linear-gradient(to left, rgba(0,0,0,1), rgba(0,0,0,0));
            -webkit-mask-image: linear-gradient(to left, rgba(0,0,0,1), rgba(0,0,0,0));
        }
        .swiper { width: 100%; padding-top: 50px; padding-bottom: 50px; }
        .swiper-slide { background-position: center; background-size: cover; width: 300px !important; height: auto !important; }
        .solution-card { height: 100%; display: flex; flex-direction: column; justify-content: space-between; padding: 30px; border-radius: 24px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); transition: all 0.3s; }
        .swiper-slide-active .solution-card { background: rgba(255,255,255,0.08); border-color: var(--primary); transform: scale(1.05); box-shadow: 0 10px 30px rgba(0,0,0,0.3); }
        .ars-icon { font-size: 1.5rem; color: var(--primary); margin-right: 15px; width: 30px; display: inline-block; text-align: center; }
        .contact-form .form-control { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.1); color: white; padding: 12px; }
        .contact-form .form-control:focus { background: rgba(255,255,255,0.08); border-color: var(--primary); box-shadow: none; color: white; }
        .hover-white:hover { color: white !important; }
        .feature-item h6 { font-size: 1.1rem; }
    </style>
</head>
<body>
    <div class="bg-gradient-custom"></div>
    <div class="hero-bg-img"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3" style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(15px); border-bottom: 1px solid rgba(255,255,255,0.05);">
        <div class="container">
            <a class="navbar-brand font-weight-bolder" href="#">
                <span class="text-gradient" style="font-size: 1.5rem; font-weight: 800;">MAX</span>PDV
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="#beneficios">Benefícios</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#solucoes">Soluções</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#planos">Planos</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#arsenal">Arsenal Max</a></li>
                    <li class="nav-item ms-lg-3 d-none d-lg-block">
                        <a href="{{ route('login') }}" class="text-white text-decoration-none me-3 opacity-75 hover-opacity-100">Entrar</a>
                    </li>
                    <li class="nav-item mt-3 mt-lg-0">
                        <a href="{{ url('/register') }}" class="btn btn-premium px-4">🚀 Contratar Agora</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section container">
        <div class="row align-items-center">
            <div class="col-lg-8 text-lg-start animate-fade-in">
                <span class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3 border border-primary border-opacity-25" style="letter-spacing: 1px;">
                    SISTEMA OPERACIONAL DEDICADO & IA
                </span>
                <h1 class="display-3 font-weight-bolder mb-4" style="line-height: 1.1;">
                    O Futuro do Varejo Chegou.<br>
                    <span class="text-gradient">Rápido. Blindado. Inteligente.</span>
                </h1>
                <p class="lead text-white-50 mb-5 pe-lg-5" style="max-width: 750px;">
                    Transforme qualquer computador em um terminal de vendas de alta performance com o Ecossistema Max. Segurança militar, funcionamento offline e marketing guiado por Inteligência Artificial.
                </p>
                <div class="d-flex flex-wrap justify-content-center justify-content-lg-start gap-3">
                    <a href="{{ url('/register') }}" class="btn btn-premium btn-lg px-5 py-3 shadow-lg">🚀 Contratar Agora</a>
                    <a href="#planos" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3">Ver Planos</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Benefits Section -->
    <section id="beneficios" class="py-5 container" style="margin-top: 50px;">
        <div class="row g-4">
            <div class="col-md-4 animate-fade-in delay-1">
                <div class="glass-card text-center h-100 p-4">
                    <div class="feature-icon" style="color: #6366f1;"><i class="fas fa-shield-halved"></i></div>
                    <h4 class="font-weight-bold">🛡️ Segurança Extrema</h4>
                    <p class="text-white-50 text-sm">Auditoria visual com biometria e Hash SHA-256. Fique de olho no seu caixa de onde estiver com acompanhamento fotográfico em tempo real.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in delay-2">
                <div class="glass-card text-center h-100 p-4">
                    <div class="feature-icon" style="color: #10b981;"><i class="fas fa-bolt"></i></div>
                    <h4 class="font-weight-bold">⚡ Rapidez e Economia</h4>
                    <p class="text-white-50 text-sm">Nosso sistema operacional MaxOS roda perfeitamente em máquinas antigas com apenas 1GB de RAM e 10GB de HD. Vida nova ao seu hardware.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in delay-3">
                <div class="glass-card text-center h-100 p-4">
                    <div class="feature-icon" style="color: #f59e0b;"><i class="fas fa-wifi-slash"></i></div>
                    <h4 class="font-weight-bold">📶 Venda Sempre</h4>
                    <p class="text-white-50 text-sm">A internet caiu? O MaxPDV continua vendendo (Offline-First) e sincroniza tudo automaticamente quando a rede voltar. Sem filas paradas.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Solutions Carousel -->
    <section id="solucoes" class="py-5 overflow-hidden">
        <div class="container text-center mb-5 animate-fade-in">
            <h2 class="display-5 font-weight-bold">O Que Compõe o <span class="text-gradient">Ecossistema Max</span></h2>
            <p class="text-white-50">Uma planta baixa de soluções integradas para o seu sucesso.</p>
        </div>
        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                <!-- Slide 1 -->
                <div class="swiper-slide">
                    <div class="solution-card">
                        <div class="mb-4">
                            <div class="text-primary mb-3"><i class="fas fa-cash-register fa-2x"></i></div>
                            <h5 class="font-weight-bold">MaxPDV & Checkout</h5>
                            <p class="text-white-50 text-xs">Frente de caixa com modos especializados para Mercado, Farmácia, Açougue e Restaurantes.</p>
                        </div>
                        <div class="text-sm text-primary">Operação Offline-First <i class="fas fa-arrow-right ms-2"></i></div>
                    </div>
                </div>
                <!-- Slide 2 -->
                <div class="swiper-slide">
                    <div class="solution-card">
                        <div class="mb-4">
                            <div class="text-success mb-3"><i class="fas fa-mobile-screen-button fa-2x"></i></div>
                            <h5 class="font-weight-bold">MaxVisionApp</h5>
                            <p class="text-white-50 text-xs">Cadastro de produtos pelo celular com acesso ao banco global de 5 milhões de itens e bipagem via câmera.</p>
                        </div>
                        <div class="text-sm text-success">Agilidade no Cadastro <i class="fas fa-arrow-right ms-2"></i></div>
                    </div>
                </div>
                <!-- Slide 3 -->
                <div class="swiper-slide">
                    <div class="solution-card">
                        <div class="mb-4">
                            <div class="text-info mb-3"><i class="fas fa-music fa-2x"></i></div>
                            <h5 class="font-weight-bold">MaxMusic</h5>
                            <p class="text-white-50 text-xs">Sua rádio indoor personalizada para engajar clientes e programar comerciais ou mensagens bíblicas automáticas.</p>
                        </div>
                        <div class="text-sm text-info">Rádio Indoor Profissional <i class="fas fa-arrow-right ms-2"></i></div>
                    </div>
                </div>
                <!-- Slide 4 -->
                <div class="swiper-slide">
                    <div class="solution-card" style="border-color: #f97316; box-shadow: 0 0 20px rgba(249, 115, 22, 0.1);">
                        <div class="mb-4">
                            <div class="mb-3 d-flex align-items-center"><i class="fas fa-rocket fa-2x text-warning me-2"></i> <span class="badge-new">NOVO</span></div>
                            <h5 class="font-weight-bold">MaxPublica</h5>
                            <p class="text-white-50 text-xs">Sua agência de marketing automatizada. IA que gera artes e áudios e publica nas redes sociais.</p>
                        </div>
                        <div class="text-sm text-warning">Marketing Guiado por IA <i class="fas fa-arrow-right ms-2"></i></div>
                    </div>
                </div>
                <!-- Slide 5 -->
                <div class="swiper-slide">
                    <div class="solution-card">
                        <div class="mb-4">
                            <div class="text-purple mb-3" style="color: #a855f7;"><i class="fas fa-microchip fa-2x"></i></div>
                            <h5 class="font-weight-bold">MaxOS</h5>
                            <p class="text-white-50 text-xs">O motor que move tudo. Sistema operacional blindado, imune a vírus e ultra-leve em modo quiosque.</p>
                        </div>
                        <div class="text-sm" style="color: #a855f7;">Blindagem Read-Only <i class="fas fa-arrow-right ms-2"></i></div>
                    </div>
                </div>
            </div>
            <div class="swiper-pagination mt-4"></div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="planos" class="py-5 bg-black bg-opacity-25" style="padding: 100px 0 !important;">
        <div class="container text-center">
            <div class="mb-5 animate-fade-in">
                <h2 class="display-5 font-weight-bold">Vitrine de <span class="text-gradient">Planos Inteligentes</span></h2>
                <p class="text-white-50">Configurações para cada estágio do seu crescimento.</p>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse($planos as $plano)
                    <div class="col-lg-4 col-md-6 animate-fade-in delay-{{ $loop->index + 1 }}">
                        <div class="glass-card h-100 p-5 d-flex flex-column {{ $loop->index == 1 ? 'featured' : '' }}">
                            @if($loop->index == 1)
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge rounded-pill bg-primary px-3 py-2">MELHOR ESCOLHA</span>
                                </div>
                            @endif
                            <h3 class="font-weight-bold mb-3">{{ $plano->nome }}</h3>
                            <div class="mb-4">
                                <span class="display-4 font-weight-bold">R$ {{ number_format($plano->valor, 2, ',', '.') }}</span>
                                <span class="text-white-50">/mês</span>
                            </div>
                            
                            <ul class="list-unstyled mb-5 flex-grow-1 text-start">
                                <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-check text-primary mt-1 me-2"></i> <span>PDV Core Unlimited + MaxOS Blindado</span></li>
                                <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-check text-primary mt-1 me-2"></i> <span>Pagamentos PIX e Bitcoin Lightning</span></li>
                                <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-check text-primary mt-1 me-2"></i> <span><strong>{{ $plano->limite_dispositivos }}</strong> Terminal(is) Ativo(s)</span></li>
                                
                                @if(Str::contains(strtolower($plano->nome), ['gestão', 'segurança', 'admin', 'pleno', 'pro']))
                                    <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-plus text-success mt-1 me-2"></i> <span>Fiado 2.0 (Biometria Facial)</span></li>
                                    <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-plus text-success mt-1 me-2"></i> <span>Auditoria Visual SHA-256 Anti-Fraude</span></li>
                                @endif
                                
                                @if(Str::contains(strtolower($plano->nome), ['full', 'commerce', 'premium', 'total', 'ouro']))
                                    <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-star text-warning mt-1 me-2"></i> <span>MaxPublica (Agência de IA)</span></li>
                                    <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-star text-warning mt-1 me-2"></i> <span>MaxMusic (Rádio Indoor Playlists)</span></li>
                                @endif

                                @if($plano->modulos_adicionais)
                                    @foreach($plano->modulos_adicionais as $modulo)
                                        <li class="mb-3 text-white-50 text-sm d-flex align-items-start"><i class="fas fa-check text-primary mt-1 me-2"></i> <span>{{ $modulo }}</span></li>
                                    @endforeach
                                @endif
                            </ul>
                            
                            <a href="{{ url('/register?plan=' . $plano->id) }}" class="btn {{ $loop->index == 1 ? 'btn-premium' : 'btn-outline-light rounded-pill' }} w-100 py-3 font-weight-bold mt-auto">🚀 Contratar Agora</a>
                        </div>
                    </div>
                @empty
                    <div class="col-12 py-5 text-white-50">Nenhum plano disponível no momento.</div>
                @endforelse
            </div>

            <!-- Add-ons UI -->
            @if($adicionais->count() > 0)
                <div class="mt-5 animate-fade-in delay-3">
                    <p class="text-white-50 text-xs mb-3 text-uppercase font-weight-bold" style="letter-spacing: 2px;">Adicione separadamente ao seu plano:</p>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @foreach($adicionais as $adicional)
                            <div class="glass-card py-2 px-4 rounded-pill border-opacity-25" style="border: 1px dashed rgba(99, 102, 241, 0.4)">
                                <span class="text-xs">
                                    <span class="text-white-50">{{ $adicional->nome }}</span>: 
                                    <span class="text-primary font-weight-bold">+ R$ {{ number_format($adicional->valor, 2, ',', '.') }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Arsenal Max / Features Grid -->
    <section id="arsenal" class="py-5 container" style="margin-top: 50px;">
        <div class="text-center mb-5 animate-fade-in">
            <h2 class="display-5 font-weight-bold">A Lista Completa do <span class="text-gradient">Arsenal MaxCheckout</span></h2>
            <p class="text-white-50">Mostre o poder real do sistema para o seu varejo.</p>
        </div>
        
        <div class="row g-5">
            <!-- Col 1 -->
            <div class="col-lg-4 animate-fade-in delay-1">
                <div class="mb-5 feature-item">
                    <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-shield-virus ars-icon"></i> Segurança Militar</h5>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Auditoria Visual com Hash</h6>
                        <p class="text-white-50 text-sm">Cancelamento ou sangria exige foto do gerente e marca d'água SHA-256 contra Replay Attack.</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Guardrails de Preço</h6>
                        <p class="text-white-50 text-sm">O sistema bloqueia vendas se o desconto ferir a margem mínima configurada.</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Fiado 2.0 Biométrico</h6>
                        <p class="text-white-50 text-sm">Crédito cliente aprovado por face, com proteção de limite mesmo offline.</p>
                    </div>
                </div>
            </div>
            <!-- Col 2 -->
            <div class="col-lg-4 animate-fade-in delay-2">
                <div class="mb-5 feature-item">
                    <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-gauge-high ars-icon"></i> Performance MaxOS</h5>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Hardware Legacy-Ready</h6>
                        <p class="text-white-50 text-sm">Roda em máquinas com 1GB RAM. Transforme sucata em terminais de alta performance.</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Blindagem Read-Only</h6>
                        <p class="text-white-50 text-sm">O sistema não corrompe se a energia acabar. Imune a vírus pelo design da arquitetura Linux.</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Multi-Hardware Native</h6>
                        <p class="text-white-50 text-sm">Suporte nativo para balanças, impressoras e gavetas sem configurações complexas.</p>
                    </div>
                </div>
            </div>
            <!-- Col 3 -->
            <div class="col-lg-4 animate-fade-in delay-3">
                <div class="mb-5 feature-item">
                    <h5 class="font-weight-bold mb-4 d-flex align-items-center"><i class="fas fa-brain ars-icon"></i> Automação IA</h5>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Marketing MaxPublica</h6>
                        <p class="text-white-50 text-sm">Gera design, áudio locutado e publica no Instagram/WhatsApp automaticamente.</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Banco Global Vision</h6>
                        <p class="text-white-50 text-sm">Acesse catálogo de 5 milhões de itens e dispense a digitação manual de novos produtos.</p>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-white font-weight-bold mb-1">Cobrança Automatizada</h6>
                        <p class="text-white-50 text-sm">IA monitora fiado e envia mensagens para clientes, além de sugerir promoções.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Big CTA Card -->
        <div class="glass-card mt-5 p-5 text-center bg-primary bg-opacity-10 border-primary border-opacity-25 animate-fade-in">
            <h2 class="font-weight-bolder mb-3">Tudo o que sua loja precisa em <span class="text-gradient">Um Só Ecossistema.</span></h2>
            <p class="lead text-white-50 mb-5">MaxCheckout - Sem fidelidade, suporte humanizado e instalação em minutos.</p>
            <a href="{{ url('/register') }}" class="btn btn-premium btn-lg px-5 py-3">🚀 CRIAR MINHA CONTA AGORA</a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 border-top border-white border-opacity-10 mt-5 bg-black bg-opacity-40">
        <div class="container">
            <div class="row g-4 mb-5">
                <div class="col-lg-4">
                    <h3 class="font-weight-bolder mb-3 text-gradient">MaxCheckout</h3>
                    <p class="text-white-50 text-sm">Plataforma de alta resiliência e inteligência para o comércio moderno. Onde tecnologia de ponta encontra a economia real.</p>
                    <div class="d-flex align-items-center gap-3 mt-4">
                        <span class="text-white-50 text-xs text-uppercase"><i class="fas fa-lock me-1"></i> SSL 256B</span>
                        <span class="text-white-50 text-xs text-uppercase"><i class="fas fa-shield me-1"></i> PCI COMPLIANT</span>
                    </div>
                </div>
                <div class="col-lg-2">
                    <h6 class="font-weight-bold mb-4 text-uppercase text-xs" style="letter-spacing: 1px;">Links Úteis</h6>
                    <ul class="list-unstyled text-sm">
                        <li class="mb-2"><a href="#beneficios" class="text-white-50 text-decoration-none hover-white">Benefícios</a></li>
                        <li class="mb-2"><a href="#solucoes" class="text-white-50 text-decoration-none hover-white">Soluções</a></li>
                        <li class="mb-2"><a href="#planos" class="text-white-50 text-decoration-none hover-white">Planos</a></li>
                    </ul>
                </div>
                <div class="col-lg-2">
                    <h6 class="font-weight-bold mb-4 text-uppercase text-xs" style="letter-spacing: 1px;">Legal</h6>
                    <ul class="list-unstyled text-sm">
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">Termos de Uso</a></li>
                        <li class="mb-2"><a href="#" class="text-white-50 text-decoration-none hover-white">Privacidade</a></li>
                    </ul>
                </div>
                <div class="col-lg-4">
                    <h6 class="font-weight-bold mb-4 text-uppercase text-xs" style="letter-spacing: 1px;">Dúvidas Rápidas?</h6>
                    <form class="contact-form">
                        <div class="input-group">
                            <input type="text" class="form-control text-sm" placeholder="Seu WhatsApp">
                            <button class="btn btn-primary" type="button"><i class="fas fa-paper-plane"></i></button>
                        </div>
                        <p class="text-xs text-white-50 mt-2">Nossa equipe entrará em contato em breve.</p>
                    </form>
                </div>
            </div>
            <div class="row pt-5 border-top border-white border-opacity-5 align-items-center">
                <div class="col-md-6">
                    <p class="text-xs text-white-50 mb-0">&copy; {{ date('Y') }} <strong>MaxCheckout</strong> - Criado Pela <a href="https://www.maximo.tec.br" class="text-white font-weight-bold" target="_blank">Maximo Tec Soluções</a>. 🛡️ SHA256 Monitorado.</p>
                </div>
                <div class="col-md-6 text-md-end mt-3 mt-md-0">
                    <div class="d-flex justify-content-center justify-content-md-end gap-3 text-white-50">
                        <a href="#" class="hover-white"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="hover-white"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="hover-white"><i class="fab fa-linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Swiper
            const swiper = new Swiper(".mySwiper", {
                effect: "coverflow",
                grabCursor: true,
                centeredSlides: true,
                slidesPerView: "auto",
                loop: true,
                autoplay: { delay: 3500 },
                coverflowEffect: { rotate: 5, stretch: 0, depth: 100, modifier: 2, slideShadows: false },
                pagination: { el: ".swiper-pagination", clickable: true },
            });

            // Observer for animations
            const obs = new IntersectionObserver((es) => {
                es.forEach(e => {
                    if (e.isIntersecting) {
                        e.target.style.opacity = '1';
                        e.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            document.querySelectorAll('.animate-fade-in').forEach(el => {
                el.style.transition = 'all 0.8s ease-out';
                obs.observe(el);
            });
        });
    </script>
</body>
</html>