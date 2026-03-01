<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>@yield('titulo', 'MaxBank') — Portal do Cliente</title>

  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <link rel="icon" type="image/png" href="/../assets/img/favicon.png">

  <style>
    :root {
      --bank-dark:    #0a0f1e;
      --bank-darker:  #060b16;
      --bank-card:    #111827;
      --bank-border:  rgba(255,255,255,0.08);
      --bank-text:    #e2e8f0;
      --bank-muted:   #94a3b8;
      --bank-primary: #3b82f6;
      --bank-success: #10b981;
      --bank-danger:  #ef4444;
      --bank-warning: #f59e0b;
      --bank-grad1:   #1e3a5f;
      --bank-grad2:   #0f172a;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
      font-family: 'Inter', sans-serif;
      background: var(--bank-darker);
      color: var(--bank-text);
      min-height: 100vh;
    }

    /* ===== NAVBAR ===== */
    .bank-nav {
      background: rgba(10,15,30,0.95);
      backdrop-filter: blur(20px);
      border-bottom: 1px solid var(--bank-border);
      padding: 0.75rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      position: sticky;
      top: 0;
      z-index: 100;
    }
    .bank-logo {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      color: var(--bank-text);
    }
    .bank-logo-icon {
      width: 36px; height: 36px;
      background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      border-radius: 10px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem; color: #fff; font-weight: 700;
    }
    .bank-logo-name { font-size: 1.1rem; font-weight: 700; letter-spacing: -0.3px; }
    .bank-logo-sub { font-size: 0.6rem; color: var(--bank-muted); display: block; }

    .bank-user-info {
      display: flex; align-items: center; gap: 0.75rem;
      font-size: 0.85rem; color: var(--bank-muted);
    }
    .bank-avatar {
      width: 32px; height: 32px;
      background: linear-gradient(135deg, #3b82f6, #8b5cf6);
      border-radius: 50%;
      display: flex; align-items: center; justify-content: center;
      font-weight: 700; font-size: 0.85rem; color: #fff;
    }

    /* ===== BOTTOM NAV (Mobile) ===== */
    .bank-bottom-nav {
      position: fixed;
      bottom: 0; left: 0; right: 0;
      background: rgba(10,15,30,0.98);
      backdrop-filter: blur(20px);
      border-top: 1px solid var(--bank-border);
      display: flex;
      z-index: 100;
      padding: 0.5rem 0;
    }
    .bank-bottom-nav a {
      flex: 1;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 3px;
      padding: 0.4rem;
      color: var(--bank-muted);
      text-decoration: none;
      font-size: 0.65rem;
      font-weight: 500;
      transition: color 0.2s;
    }
    .bank-bottom-nav a.active, .bank-bottom-nav a:hover { color: var(--bank-primary); }
    .bank-bottom-nav a i { font-size: 1.1rem; }

    /* ===== MAIN ===== */
    .bank-main {
      max-width: 900px;
      margin: 0 auto;
      padding: 1.5rem 1rem 6rem;
    }

    /* ===== CARDS ===== */
    .bank-card {
      background: var(--bank-card);
      border: 1px solid var(--bank-border);
      border-radius: 16px;
      padding: 1.5rem;
      margin-bottom: 1rem;
      backdrop-filter: blur(10px);
      transition: border-color 0.2s;
    }
    .bank-card:hover { border-color: rgba(59,130,246,0.3); }

    .bank-card-sm { padding: 1rem; border-radius: 12px; }

    .bank-card-gradient {
      background: linear-gradient(135deg, var(--bank-grad1), var(--bank-grad2));
      border: 1px solid rgba(59,130,246,0.3);
      position: relative;
      overflow: hidden;
    }
    .bank-card-gradient::before {
      content: '';
      position: absolute;
      top: -50%; right: -50%;
      width: 200%; height: 200%;
      background: radial-gradient(circle, rgba(59,130,246,0.12) 0%, transparent 60%);
      pointer-events: none;
    }

    /* ===== TYPOGRAPHY ===== */
    .bank-h1 { font-size: 2rem; font-weight: 700; letter-spacing: -0.5px; }
    .bank-h2 { font-size: 1.5rem; font-weight: 700; }
    .bank-h3 { font-size: 1.1rem; font-weight: 600; }
    .bank-label { font-size: 0.7rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.8px; color: var(--bank-muted); }
    .bank-value { font-size: 0.9rem; font-weight: 500; color: var(--bank-text); }

    /* ===== BADGES ===== */
    .bank-badge {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 3px 10px;
      border-radius: 20px;
      font-size: 0.7rem;
      font-weight: 600;
    }
    .bank-badge-success { background: rgba(16,185,129,0.15); color: #10b981; border:1px solid rgba(16,185,129,0.3); }
    .bank-badge-danger  { background: rgba(239,68,68,0.15); color: #ef4444; border:1px solid rgba(239,68,68,0.3); }
    .bank-badge-warning { background: rgba(245,158,11,0.15); color: #f59e0b; border:1px solid rgba(245,158,11,0.3); }
    .bank-badge-info    { background: rgba(59,130,246,0.15); color: #3b82f6; border:1px solid rgba(59,130,246,0.3); }
    .bank-badge-muted   { background: rgba(148,163,184,0.1); color: #94a3b8; border:1px solid rgba(148,163,184,0.2); }

    /* ===== BUTTONS ===== */
    .bank-btn {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      gap: 0.4rem;
      padding: 0.65rem 1.25rem;
      border-radius: 10px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      border: none;
      transition: all 0.2s;
      text-decoration: none;
    }
    .bank-btn-primary {
      background: linear-gradient(135deg, #3b82f6, #1d4ed8);
      color: #fff;
      box-shadow: 0 4px 15px rgba(59,130,246,0.35);
    }
    .bank-btn-primary:hover { transform: translateY(-1px); box-shadow: 0 6px 20px rgba(59,130,246,0.45); color:#fff; }
    .bank-btn-success {
      background: linear-gradient(135deg, #10b981, #059669);
      color: #fff;
      box-shadow: 0 4px 15px rgba(16,185,129,0.35);
    }
    .bank-btn-success:hover { transform: translateY(-1px); color:#fff; }
    .bank-btn-outline {
      background: transparent;
      color: var(--bank-muted);
      border: 1px solid var(--bank-border);
    }
    .bank-btn-outline:hover { border-color: var(--bank-primary); color: var(--bank-primary); }
    .bank-btn-lg { padding: 0.85rem 2rem; font-size: 1rem; }
    .bank-btn-sm { padding: 0.4rem 0.85rem; font-size: 0.78rem; }
    .bank-btn-full { width: 100%; }

    /* ===== INPUTS ===== */
    .bank-input-group { margin-bottom: 1rem; }
    .bank-input-label { display: block; font-size: 0.72rem; font-weight: 600; color: var(--bank-muted); text-transform: uppercase; letter-spacing: 0.7px; margin-bottom: 0.4rem; }
    .bank-input {
      width: 100%;
      background: rgba(255,255,255,0.05);
      border: 1px solid var(--bank-border);
      border-radius: 10px;
      padding: 0.7rem 1rem;
      font-size: 0.92rem;
      color: var(--bank-text);
      transition: border-color 0.2s, box-shadow 0.2s;
      font-family: 'Inter', sans-serif;
    }
    .bank-input:focus {
      outline: none;
      border-color: var(--bank-primary);
      box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
      background: rgba(59,130,246,0.05);
    }
    .bank-input::placeholder { color: var(--bank-muted); }

    /* ===== ALERT ===== */
    .bank-alert {
      padding: 0.85rem 1rem;
      border-radius: 10px;
      margin-bottom: 1rem;
      font-size: 0.85rem;
      font-weight: 500;
      display: flex;
      align-items: flex-start;
      gap: 0.5rem;
    }
    .bank-alert-success { background: rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.3); color:#10b981; }
    .bank-alert-danger  { background: rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.3); color:#ef4444; }
    .bank-alert-warning { background: rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.3); color:#f59e0b; }
    .bank-alert-info    { background: rgba(59,130,246,0.1); border:1px solid rgba(59,130,246,0.3); color:#3b82f6; }

    /* ===== PROGRESS ===== */
    .bank-progress {
      height: 8px;
      background: rgba(255,255,255,0.08);
      border-radius: 4px;
      overflow: hidden;
    }
    .bank-progress-bar {
      height: 100%;
      border-radius: 4px;
      background: linear-gradient(90deg, #3b82f6, #8b5cf6);
      transition: width 0.6s ease;
    }

    /* ===== TRANSACTION ITEM ===== */
    .bank-tx-item {
      display: flex;
      align-items: center;
      padding: 0.85rem 0;
      border-bottom: 1px solid var(--bank-border);
    }
    .bank-tx-item:last-child { border-bottom: none; }
    .bank-tx-icon {
      width: 40px; height: 40px;
      border-radius: 12px;
      display: flex; align-items: center; justify-content: center;
      font-size: 1rem;
      margin-right: 0.75rem;
      flex-shrink: 0;
    }
    .bank-tx-icon.debit  { background: rgba(239,68,68,0.15); color: #ef4444; }
    .bank-tx-icon.credit { background: rgba(16,185,129,0.15); color: #10b981; }
    .bank-tx-info { flex: 1; }
    .bank-tx-name { font-size: 0.88rem; font-weight: 600; color: var(--bank-text); }
    .bank-tx-date { font-size: 0.72rem; color: var(--bank-muted); }
    .bank-tx-amount { font-size: 0.95rem; font-weight: 700; }
    .bank-tx-amount.debit  { color: #ef4444; }
    .bank-tx-amount.credit { color: #10b981; }

    /* ===== TABS ===== */
    .bank-tabs { display: flex; gap: 0.25rem; background: rgba(255,255,255,0.04); border-radius: 12px; padding: 4px; margin-bottom: 1rem; }
    .bank-tab {
      flex: 1; text-align: center; padding: 0.5rem; border-radius: 9px;
      font-size: 0.82rem; font-weight: 600; color: var(--bank-muted);
      text-decoration: none; transition: all 0.2s;
    }
    .bank-tab.active { background: var(--bank-primary); color: #fff; }
    .bank-tab:hover:not(.active) { color: var(--bank-text); }

    /* ===== DIVIDER ===== */
    .bank-divider { height: 1px; background: var(--bank-border); margin: 1rem 0; }

    /* ===== ANIMATIONS ===== */
    @keyframes fadeUp { from { opacity:0; transform: translateY(15px); } to { opacity:1; transform: translateY(0); } }
    .fade-up { animation: fadeUp 0.4s ease forwards; }
    .fade-up-1 { animation-delay: 0.05s; opacity:0; }
    .fade-up-2 { animation-delay: 0.1s; opacity:0; }
    .fade-up-3 { animation-delay: 0.15s; opacity:0; }
    .fade-up-4 { animation-delay: 0.2s; opacity:0; }

    @media (max-width: 768px) {
      .bank-main { padding: 1rem 0.75rem 5rem; }
      .bank-h1 { font-size: 1.5rem; }
    }
  </style>

  @stack('bank-styles')
</head>
<body>

  {{-- Top Nav --}}
  <nav class="bank-nav">
    <a href="{{ route('banco.dashboard') }}" class="bank-logo">
      <div class="bank-logo-icon">M</div>
      <div>
        <span class="bank-logo-name">MaxBank</span>
        <span class="bank-logo-sub">Portal do Cliente</span>
      </div>
    </a>

    @if(isset($clienteLogado))
    <div class="bank-user-info">
      <span class="d-none d-sm-inline">{{ $clienteLogado->nome }}</span>
      <div class="bank-avatar">{{ strtoupper(substr($clienteLogado->nome, 0, 1)) }}</div>
      <a href="{{ route('banco.logout') }}" class="bank-btn bank-btn-outline bank-btn-sm" title="Sair">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
    @endif
  </nav>

  {{-- Alertas de sessão --}}
  @if(session('success') || session('error'))
  <div style="max-width:900px;margin:0 auto;padding:0.75rem 1rem 0;">
    @if(session('success'))
      <div class="bank-alert bank-alert-success">
        <i class="fas fa-check-circle"></i>{{ session('success') }}
      </div>
    @endif
    @if(session('error'))
      <div class="bank-alert bank-alert-danger">
        <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
      </div>
    @endif
  </div>
  @endif

  {{-- Conteúdo --}}
  <main class="bank-main">
    @yield('bank-content')
  </main>

  {{-- Bottom Nav (só quando logado) --}}
  @if(isset($clienteLogado))
  <nav class="bank-bottom-nav">
    <a href="{{ route('banco.dashboard') }}" class="{{ request()->routeIs('banco.dashboard') ? 'active' : '' }}">
      <i class="fas fa-home"></i>Início
    </a>
    <a href="{{ route('banco.faturas') }}" class="{{ request()->routeIs('banco.faturas') ? 'active' : '' }}">
      <i class="fas fa-file-invoice-dollar"></i>Faturas
    </a>
    <a href="{{ route('banco.perfil') }}" class="{{ request()->routeIs('banco.perfil') ? 'active' : '' }}">
      <i class="fas fa-user-circle"></i>Perfil
    </a>
    <a href="{{ route('banco.logout') }}">
      <i class="fas fa-sign-out-alt"></i>Sair
    </a>
  </nav>
  @endif

  @stack('bank-scripts')
</body>
</html>
