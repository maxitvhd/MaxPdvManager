@extends('layouts.user_type.guest')

@section('content')
  <style>
    .register-container {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: radial-gradient(circle at top left, rgba(16, 185, 129, 0.1), transparent),
        radial-gradient(circle at bottom right, rgba(99, 102, 241, 0.1), transparent),
        #0f172a;
      padding: 40px 20px;
    }

    .glass-register-card {
      background: rgba(255, 255, 255, 0.02);
      backdrop-filter: blur(15px);
      border: 1px solid rgba(255, 255, 255, 0.08);
      border-radius: 28px;
      padding: 45px;
      width: 100%;
      max-width: 500px;
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
      border-color: #10b981 !important;
      box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2) !important;
    }

    .btn-success-premium {
      background: linear-gradient(135deg, #10b981 0%, #059669 100%);
      border: none;
      border-radius: 12px;
      padding: 15px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 1px;
      transition: all 0.3s;
      color: white;
    }

    .btn-success-premium:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
    }

    .text-gradient-success {
      background: linear-gradient(to right, #10b981, #34d399);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }

    .feature-badge {
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
      padding: 4px 10px;
      border-radius: 100px;
      font-size: 0.75rem;
      font-weight: 600;
      border: 1px solid rgba(16, 185, 129, 0.2);
    }
  </style>

  <div class="register-container">
    <div class="glass-register-card">
      <div class="text-center mb-5">
        <span class="badge bg-primary mb-2" style="font-size: 0.65rem; letter-spacing: 2px;">JOIN THE ECOSYSTEM</span>
        <h2 class="font-weight-bolder text-white">MAX<span class="text-gradient-success">ECO</span></h2>
        <p class="text-white-50">Blindagem e Inteligência para seu Negócio</p>
      </div>

      <div class="mb-5 text-center">
        <h4 class="font-weight-bold text-white mb-2">Construa seu Arsenal</h4>
        <p class="text-white-50 text-sm">O futuro do seu negócio começa neste passo.</p>
      </div>

      <form role="form" method="POST" action="/register">
        @csrf
        <div class="mb-3">
          <input type="text" class="form-control" placeholder="Seu Nome Completo" name="name" value="{{ old('name') }}"
            required>
          @error('name') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-3">
          <input type="email" class="form-control" placeholder="Seu melhor E-mail" name="email" value="{{ old('email') }}"
            required>
          @error('email') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
        </div>
        <div class="mb-4">
          <input type="password" class="form-control" placeholder="Crie uma Senha Forte" name="password" required>
          @error('password') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="mb-4">
          <p class="text-white-50 text-xs mb-3 text-uppercase font-weight-bold" style="letter-spacing: 1px;">Qual seu
            papel na operação?</p>
          <div class="row g-2">
            <div class="col-6">
              <div class="form-check p-0 mb-2">
                <input type="checkbox" class="btn-check" name="roles[]" value="dono" id="role_dono" checked
                  autocomplete="off">
                <label class="btn btn-outline-success btn-sm w-100 py-2 border-opacity-25" for="role_dono">Dono da
                  Loja</label>
              </div>
            </div>
            <div class="col-6">
              <div class="form-check p-0 mb-2">
                <input type="checkbox" class="btn-check" name="roles[]" value="funcionario" id="role_func"
                  autocomplete="off">
                <label class="btn btn-outline-success btn-sm w-100 py-2 border-opacity-25"
                  for="role_func">Funcionário</label>
              </div>
            </div>
          </div>
        </div>

        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" name="agreement" id="agreement" checked required>
          <label class="form-check-label text-white-50 text-xs" for="agreement">
            Eu aceito os <a href="javascript:;" class="text-info">Termos de Uso</a> e a <a href="javascript:;"
              class="text-info">Blindagem de Dados</a>.
          </label>
          @error('agreement') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-success-premium w-100 mb-3">🚀 CRIAR MEU ARSENAL AGORA</button>
        </div>
      </form>

      <div class="mt-4 pt-4 border-top border-white border-opacity-10 text-center">
        <p class="text-sm text-white-50">
          Já possui uma licença ativa?
          <a href="login" class="text-success font-weight-bold">Entrar no Painel</a>
        </p>
      </div>

      <div class="mt-4 d-flex justify-content-center gap-2">
        <span class="feature-badge"><i class="fas fa-shield-alt me-1"></i> SHA-256</span>
        <span class="feature-badge"><i class="fas fa-lock me-1"></i> SSL SECURE</span>
      </div>
    </div>
  </div>
@endsection