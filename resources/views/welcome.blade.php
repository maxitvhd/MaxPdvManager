<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaxPDV - Inteligência Artificial para seu Comércio</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
            opacity: 0.2;
            z-index: -1;
            mask-image: linear-gradient(to left, rgba(0, 0, 0, 1), rgba(0, 0, 0, 0));
            -webkit-mask-image: linear-gradient(to left, rgba(0, 0, 0, 1), rgba(0, 0, 0, 0));
        }
    </style>
</head>

<body>
    <div class="bg-gradient-custom"></div>
    <div class="hero-bg-img"></div>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top py-3"
        style="background: rgba(15, 23, 42, 0.8); backdrop-filter: blur(10px);">
        <div class="container">
            <a class="navbar-brand font-weight-bolder" href="#">
                <span class="text-gradient" style="font-size: 1.5rem; font-weight: 800;">MAX</span>PDV
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item"><a class="nav-link px-3" href="#recursos">Recursos</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#planos">Planos</a></li>
                    <li class="nav-item"><a class="nav-link px-3" href="#seguranca">Segurança</a></li>
                    <li class="nav-item ms-lg-3">
                        <a href="{{ route('login') }}" class="btn btn-outline-light rounded-pill px-4">Login</a>
                    </li>
                    <li class="nav-item ms-lg-2">
                        <a href="{{ url('/register') }}" class="btn btn-premium px-4">Contratar Now</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section container">
        <div class="row align-items-center">
            <div class="col-lg-7 text-start animate-fade-in">
                <span
                    class="badge rounded-pill bg-primary bg-opacity-10 text-primary px-3 py-2 mb-3 border border-primary border-opacity-25">
                    ✨ Nova Era do Varejo com IA
                </span>
                <h1 class="display-3 font-weight-bolder mb-4" style="line-height: 1.1;">
                    O Futuro do seu <br>
                    <span class="text-gradient">Comércio Inteligente</span>
                </h1>
                <p class="lead text-white-50 mb-5 pe-lg-5">
                    A primeira plataforma que une PDV robusto, gestão de estoque e um Gerador de Temas com IA poderoso
                    para suas redes sociais. Economia, rapidez e segurança em um só lugar.
                </p>
                <div class="d-flex gap-3">
                    <a href="{{ url('/register') }}" class="btn btn-premium btn-lg px-5 py-3">Começar Agora</a>
                    <a href="#recursos" class="btn btn-outline-light btn-lg rounded-pill px-5 py-3">Explorar
                        Recursos</a>
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block animate-fade-in delay-1">
                <div class="glass-card p-4">
                    <img src="https://pdv.aiconect.com.br/assets/img/mockup_dashboard.png"
                        class="img-fluid rounded-4 shadow-lg" alt="Dashboard Preview"
                        onerror="this.src='https://via.placeholder.com/600x400/1e1b4b/ffffff?text=Interface+Inteligente'">
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="recursos" class="py-100 container mt-5">
        <div class="text-center mb-5 animate-fade-in">
            <h2 class="display-5 font-weight-bold">Recursos <span class="text-gradient">Revolucionários</span></h2>
            <p class="text-white-50">Tudo o que você precisa para dominar o mercado local.</p>
        </div>
        <div class="row g-4">
            <div class="col-md-4 animate-fade-in delay-1">
                <div class="glass-card text-center h-100">
                    <div class="feature-icon text-primary"><i class="fas fa-brain"></i></div>
                    <h3>Gerador de Temas IA</h3>
                    <p class="text-white-50">Crie artes profissionais para WhatsApp e Redes Sociais apenas descrevendo o
                        que você quer. Nossa IA faz o resto.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in delay-2">
                <div class="glass-card text-center h-100">
                    <div class="feature-icon text-success"><i class="fas fa-bolt"></i></div>
                    <h3>Checkout Ultra Rápido</h3>
                    <p class="text-white-50">Venda em segundos com nossa interface otimizada. Menos filas, mais lucro
                        para o seu negócio.</p>
                </div>
            </div>
            <div class="col-md-4 animate-fade-in delay-3">
                <div class="glass-card text-center h-100">
                    <div class="feature-icon text-info"><i class="fas fa-shield-halved"></i></div>
                    <h3>Segurança Máxima</h3>
                    <p class="text-white-50">Dados criptografados e backup em tempo real. Sua loja funcionando 24/7 sem
                        preocupações.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="planos" class="py-100 bg-black bg-opacity-25" style="margin-top: 100px; padding: 100px 0;">
        <div class="container">
            <div class="text-center mb-5 animate-fade-in">
                <h2 class="display-5 font-weight-bold">Escolha seu <span class="text-gradient">Plano</span></h2>
                <p class="text-white-50">Preços transparentes para todos os tamanhos de negócio.</p>
            </div>
            <div class="row g-4 justify-content-center">
                @forelse($planos as $plano)
                    <div class="col-lg-4 col-md-6 animate-fade-in delay-{{ $loop->index + 1 }}">
                        <div class="glass-card h-100 p-5 d-flex flex-column {{ $loop->index == 1 ? 'featured' : '' }}">
                            @if($loop->index == 1)
                                <div class="position-absolute top-0 start-50 translate-middle">
                                    <span class="badge rounded-pill bg-primary px-3 py-2">MAIS POPULAR</span>
                                </div>
                            @endif
                            <h3 class="mb-2">{{ $plano->nome }}</h3>
                            <div class="mb-4">
                                <span class="display-4 font-weight-bold">R$
                                    {{ number_format($plano->valor, 2, ',', '.') }}</span>
                                <span class="text-white-50">/mês</span>
                            </div>
                            <ul class="list-unstyled mb-5 flex-grow-1">
                                <li class="mb-3 text-white-50"><i class="fas fa-check text-primary me-2"></i> Cadastro de
                                    Produtos</li>
                                <li class="mb-3 text-white-50"><i class="fas fa-check text-primary me-2"></i>
                                    <strong>{{ $plano->limite_dispositivos }}</strong> Dispositivos
                                </li>
                                <li class="mb-3 text-white-50"><i class="fas fa-check text-primary me-2"></i>
                                    {{ $plano->meses_validade }} Meses de validade</li>
                                @if($plano->modulos_adicionais)
                                    @foreach($plano->modulos_adicionais as $modulo)
                                        <li class="mb-3 text-white-50"><i class="fas fa-check text-primary me-2"></i> {{ $modulo }}
                                        </li>
                                    @endforeach
                                @endif
                            </ul>
                            <a href="{{ url('/register?plan=' . $plano->id) }}"
                                class="btn {{ $loop->index == 1 ? 'btn-premium' : 'btn-outline-light rounded-pill' }} w-100 py-3">Contratar
                                Agora</a>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-white-50">Nenhum plano disponível no momento.</div>
                @endforelse
            </div>

            <!-- Additionals -->
            @if($adicionais->count() > 0)
                <div class="mt-5 text-center animate-fade-in delay-3">
                    <h4 class="mb-4 text-white-50">Recursos Adicionais (Upgrade)</h4>
                    <div class="d-flex flex-wrap justify-content-center gap-3">
                        @foreach($adicionais as $adicional)
                            <div class="glass-card py-2 px-4 rounded-pill border-opacity-25"
                                style="border: 1px dashed var(--primary)">
                                <span class="text-sm">
                                    <strong>{{ $adicional->nome }}</strong>:
                                    <span class="text-primary">R$ {{ number_format($adicional->valor, 2, ',', '.') }}</span>
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </section>

    <!-- Final CTA -->
    <section class="py-100 container mt-5 pb-5">
        <div
            class="glass-card p-5 text-center bg-gradient-to-r from-primary/10 to-secondary/10 border-primary animate-fade-in">
            <h2 class="display-4 font-weight-bold mb-4">Pronto para dar o <br><span class="text-gradient">Próximo
                    Passo?</span></h2>
            <p class="lead text-white-50 mb-5">Junte-se a milhares de lojistas que já estão lucrando mais com o MaxPDV.
            </p>
            <a href="{{ url('/register') }}" class="btn btn-premium btn-lg px-5 py-3 shadow-lg">Começar Agora Grátis</a>
            <div class="mt-4 text-sm text-white-50">
                <i class="fas fa-lock me-2"></i> Sem fidelidade • <i class="fas fa-headset ms-3 me-2"></i> Suporte
                Premium
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="py-5 border-top border-white border-opacity-10 mt-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0 text-white-50">&copy; {{ date('Y') }} MAXPDV. Todos os direitos reservados.</p>
                </div>
                <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="text-white-50 me-3"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="text-white-50"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>