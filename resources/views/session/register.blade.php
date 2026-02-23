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

    .feature-badge {
      background: rgba(16, 185, 129, 0.1);
      color: #10b981;
      padding: 4px 10px;
      border-radius: 100px;
      font-size: 0.75rem;
      font-weight: 600;
      border: 1px solid rgba(16, 185, 129, 0.2);
    }

    .modal-content {
      background: #1e293b;
      border: 1px solid rgba(255, 255, 255, 0.1);
      border-radius: 20px;
      color: white;
    }

    .role-option {
      background: rgba(255, 255, 255, 0.03);
      border: 1px solid rgba(255, 255, 255, 0.05);
      border-radius: 12px;
      padding: 12px;
      margin-bottom: 10px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .role-option:hover {
      background: rgba(16, 185, 129, 0.1);
      border-color: #10b981;
    }

    .role-option.active {
      background: #10b981;
      color: white;
    }
  </style>

  <div class="register-container">
    <div class="glass-register-card">
      <div class="text-center mb-5">
        <h2 class="font-weight-bolder text-white">MAX<span style="color: #10b981;">ECO</span></h2>
        <p class="text-white-50">Blindagem e Inteligência para seu Negócio</p>
      </div>

      <form role="form" method="POST" action="/register">
        @csrf
        <div class="mb-3">
          <input type="text" class="form-control" placeholder="Nome Completo" name="name" value="{{ old('name') }}"
            required>
        </div>
        <div class="mb-3">
          <input type="email" class="form-control" placeholder="E-mail" name="email" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
          <input type="password" class="form-control" placeholder="Senha" name="password" required>
        </div>

        <div class="mb-4">
          <label class="text-white-50 text-xs mb-2">IDENTIFICAREI SUA FUNÇÃO</label>
          <button type="button" class="btn btn-outline-light w-100 py-2 border-opacity-25"
            style="border-radius: 12px; border: 1px dashed rgba(255,255,255,0.3)" data-bs-toggle="modal"
            data-bs-target="#rolesModal" id="roleBtn">
            <i class="fas fa-user-tag me-2"></i> <span id="roleText">Escolher Função</span>
          </button>
          <input type="hidden" name="roles[]" id="selectedRole" value="">
          @error('roles') <p class="text-danger text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="form-check mb-4">
          <input class="form-check-input" type="checkbox" name="agreement" id="agreement" checked required>
          <label class="form-check-label text-white-50 text-xs" for="agreement">
            Eu aceito os <a href="javascript:;" class="text-info">Termos de Uso</a>.
          </label>
        </div>

        <div class="text-center">
          <button type="submit" class="btn btn-success-premium w-100 mb-3">🚀 CRIAR MEU ARSENAL</button>
        </div>
      </form>

      <div class="text-center mt-3">
        <p class="text-sm text-white-50">
          Já tem conta? <a href="login" class="text-success font-weight-bold">Entrar</a>
        </p>
      </div>
    </div>
  </div>

  <!-- Roles Modal -->
  <div class="modal fade" id="rolesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header border-0">
          <h5 class="modal-title">Selecione sua Função</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="role-grid">
            <div class="role-option" onclick="selectRole('dono', 'Dono')">
              <i class="fas fa-crown me-2 text-warning"></i> Dono
            </div>
            <div class="role-option" onclick="selectRole('usuario', 'Usuário')">
              <i class="fas fa-user me-2 text-info"></i> Usuário
            </div>
            <div class="role-option" onclick="selectRole('funcionario', 'Funcionário')">
              <i class="fas fa-user-tie me-2 text-success"></i> Funcionário
            </div>
            <div class="role-option" onclick="selectRole('contador', 'Contador')">
              <i class="fas fa-calculator me-2 text-primary"></i> Contador
            </div>
            <div class="role-option" onclick="selectRole('fornecedor', 'Fornecedor')">
              <i class="fas fa-truck me-2 text-danger"></i> Fornecedor
            </div>
            <div class="role-option" onclick="selectRole('prestador', 'Prestador')">
              <i class="fas fa-tools me-2 text-warning"></i> Prestador
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    function selectRole(val, label) {
      document.getElementById('selectedRole').value = val;
      document.getElementById('roleText').innerText = label;
      document.getElementById('roleBtn').classList.remove('btn-outline-light');
      document.getElementById('roleBtn').classList.add('btn-outline-success');

      // Close modal
      var myModalEl = document.getElementById('rolesModal');
      var modal = bootstrap.Modal.getInstance(myModalEl);
      modal.hide();
    }
  </script>
@endsection