@extends('layouts.user_type.guest')

@section('content')
  <style>
    .login-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.1), transparent),
        radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.1), transparent),
        #0f172a;
      padding: 40px 20px;
    }

    .glass-login-card {
      background: rgba(255, 255, 255, 0.02);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px;
      padding: 45px;
      width: 100%;
      max-width: 450px;
      box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
    }

    .form-control {
      background: rgba(255, 255, 255, 0.04) !important;
      border: 1px solid rgba(255, 255, 255, 0.1) !important;
      color: white !important;
      border-radius: 12px !important;
      padding: 12px 15px !important;
    }

    .form-control:focus {
      background: rgba(255, 255, 255, 0.06) !important;
      border-color: #6366f1 !important;
      box-shadow: 0 0 0 2px rgba(99, 102, 241, 0.2) !important;
    }

    .btn-premium {
      background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%);
      border: none;
      border-radius: 12px;
      padding: 15px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s;
      color: white;
    }

    .btn-premium:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(99, 102, 241, 0.4);
    }

    .text-gradient {
      background: linear-gradient(to right, #6366f1, #a855f7);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
  </style>

  <div class="login-container">
    <div class="glass-login-card animate-fade-in">
      <div class="text-center mb-5">
        <h2 class="font-weight-bolder text-white">MAX<span class="text-gradient">PDV</span></h2>
        <p class="text-white-50">Sentinela do seu Varejo</p>
      </div>

      <div class="mb-5">
        <h4 class="font-weight-bold text-white mb-2">Bem-vindo de volta</h4>
        <p class="text-white-50 text-sm">O arsenal do seu sucesso está pronto.</p>
      </div>

      <form role="form" method="POST" action="/session">
        @csrf
        <div class="mb-3">
          <label class="text-white-50 text-xs mb-1">E-MAIL</label>
          <input type="email" class="form-control" name="email" id="email" placeholder="Digite seu e-mail"
            value="{{ old('email') }}" required>
          @error('email')
            <p class="text-danger text-xs mt-2">{{ $message }}</p>
          @enderror
        </div>
        <div class="mb-4">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="text-white-50 text-xs">SENHA</label>
            <a href="/login/forgot-password" class="text-xs text-info">Esqueceu?</a>
          </div>
          <input type="password" class="form-control" name="password" id="password" placeholder="Digite sua senha"
            required>
          @error('password')
            <p class="text-danger text-xs mt-2">{{ $message }}</p>
          @enderror
        </div>

        <div class="form-check form-switch mb-4">
          <input class="form-check-input" type="checkbox" id="rememberMe" checked>
          <label class="form-check-label text-white-50 text-xs" for="rememberMe">Manter conectado</label>
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-premium w-100 mb-3">🚀 ACESSAR PAINEL</button>
        </div>
      </form>

      <div class="mt-4 pt-4 border-top border-white border-opacity-10 text-center">
        <p class="text-sm text-white-50">
          Não tem uma conta?
          <a href="register" class="text-info font-weight-bold">Cadastrar arsenal</a>
        </p>
      </div>
    </div>
  </div>
@endsection