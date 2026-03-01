@extends('bank.portal.layout')
@section('titulo', 'Login')

@push('bank-styles')
<style>
  body { display: flex; align-items: center; justify-content: center; min-height: 100vh; }
  .bank-nav { display: none; }
  .bank-main { padding: 1rem; display: flex; align-items: center; justify-content: center; min-height: 100vh; width: 100%; max-width: 100%; }
  .login-box { width: 100%; max-width: 420px; }
  .login-logo { text-align: center; margin-bottom: 2rem; }
  .login-logo-icon {
    width: 72px; height: 72px; background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 20px; display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: #fff; margin: 0 auto 0.75rem;
    box-shadow: 0 15px 35px rgba(59,130,246,0.35);
  }
  .login-logo h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; }
  .login-logo p { color: var(--bank-muted); font-size: 0.85rem; }

  /* Tabs metodo login */
  .method-tabs { display: flex; gap: 0.25rem; background: rgba(255,255,255,0.04); border-radius: 12px; padding: 4px; margin-bottom: 1.25rem; }
  .method-tab {
    flex: 1; text-align: center; padding: 0.55rem; border-radius: 9px;
    font-size: 0.82rem; font-weight: 600; color: var(--bank-muted); cursor: pointer; transition: all 0.2s; border: none; background: transparent;
  }
  .method-tab.active { background: var(--bank-primary); color: #fff; }

  /* Webcam */
  #webcam-container { position: relative; border-radius: 16px; overflow: hidden; background: #000; aspect-ratio: 4/3; margin-bottom: 1rem; }
  #webcam-video { width: 100%; height: 100%; object-fit: cover; }
  #webcam-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
  }
  #face-guide {
    width: 180px; height: 220px;
    border: 2px solid rgba(59,130,246,0.7);
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(59,130,246,0.2);
    animation: pulse-frame 2s infinite;
  }
  @keyframes pulse-frame {
    0%, 100% { box-shadow: 0 0 0 4px rgba(59,130,246,0.2); }
    50% { box-shadow: 0 0 0 8px rgba(59,130,246,0.35); }
  }
  #facial-status { text-align: center; font-size: 0.8rem; color: var(--bank-muted); margin-bottom: 0.75rem; min-height: 1.2em; }
  #facial-status.ok { color: var(--bank-success); font-weight: 600; }
  #facial-status.err { color: var(--bank-danger); }
</style>
@endpush

@section('bank-content')
<div class="login-box">
  <div class="login-logo">
    <div class="login-logo-icon">M</div>
    <h1>MaxBank</h1>
    <p>Portal Seguro do Cliente</p>
  </div>

  @if(session('error'))
    <div class="bank-alert bank-alert-danger"><i class="fas fa-exclamation-circle"></i>{{ session('error') }}</div>
  @endif

  {{-- Tabs de método --}}
  <div class="method-tabs">
    <button class="method-tab active" onclick="setMetodo('pin', this)">
      <i class="fas fa-key me-1"></i>PIN
    </button>
    <button class="method-tab" onclick="setMetodo('facial', this)">
      <i class="fas fa-camera me-1"></i>Facial
    </button>
  </div>

  <form action="{{ route('banco.autenticar') }}" method="POST" id="loginForm">
    @csrf
    <input type="hidden" name="metodo" id="metodo" value="pin">
    <input type="hidden" name="facial_score" id="facial_score" value="0">

    {{-- Identificação --}}
    <div class="bank-input-group">
      <label class="bank-input-label">Código ou Usuário</label>
      <input type="text" name="identificacao" class="bank-input" placeholder="Ex.: MNL-0000001-MAX"
             value="{{ old('identificacao') }}" required autocomplete="off">
    </div>

    {{-- Painel PIN --}}
    <div id="pin-panel">
      <div class="bank-input-group">
        <label class="bank-input-label">PIN de Acesso</label>
        <input type="password" name="pin" id="pinInput" class="bank-input" placeholder="••••••"
               maxlength="8" inputmode="numeric" autocomplete="off">
      </div>
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:0.4rem;margin-bottom:1rem;" id="numpad">
        @foreach(['1','2','3','4','5','6','7','8','9','<i class="fas fa-delete-left"></i>','0','✓'] as $k)
          <button type="button" onclick="numpadPress('{{ $k }}')"
                  class="bank-btn bank-btn-outline"
                  style="font-size:1.1rem;padding:0.9rem;border-radius:12px;">
            {!! $k !!}
          </button>
        @endforeach
      </div>
    </div>

    {{-- Painel Facial --}}
    <div id="facial-panel" style="display:none;">
      <div id="webcam-container">
        <video id="webcam-video" autoplay playsinline muted></video>
        <div id="webcam-overlay"><div id="face-guide"></div></div>
      </div>
      <div id="facial-status">Iniciando câmera...</div>
      <button type="button" id="captureBtn" class="bank-btn bank-btn-primary bank-btn-full mb-3">
        <i class="fas fa-camera me-2"></i>Capturar Rosto
      </button>
    </div>

    <button type="submit" class="bank-btn bank-btn-primary bank-btn-full bank-btn-lg" id="submitBtn">
      <i class="fas fa-unlock-alt me-2"></i>Entrar na Conta
    </button>
  </form>

  <div style="text-align:center;margin-top:1.5rem;">
    <p style="color:var(--bank-muted);font-size:0.78rem;">
      <i class="fas fa-shield-alt me-1"></i>
      Conexão criptografada e segura &mdash; MaxCheckout
    </p>
  </div>
</div>
@endsection

@push('bank-scripts')
<script>
  function setMetodo(m, btn) {
    document.getElementById('metodo').value = m;
    document.querySelectorAll('.method-tab').forEach(t => t.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('pin-panel').style.display = m === 'pin' ? 'block' : 'none';
    document.getElementById('facial-panel').style.display = m === 'facial' ? 'block' : 'none';
    if (m === 'facial') initWebcam();
  }

  // Numpad
  function numpadPress(k) {
    const inp = document.getElementById('pinInput');
    if (k.includes('delete') || k.includes('fa-')) {
      inp.value = inp.value.slice(0, -1);
    } else if (k === '✓') {
      document.getElementById('loginForm').submit();
    } else if (inp.value.length < 8) {
      inp.value += k;
    }
  }

  // Webcam
  async function initWebcam() {
    const status = document.getElementById('facial-status');
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
      document.getElementById('webcam-video').srcObject = stream;
      status.textContent = 'Posicione seu rosto no guia oval';
      status.className = '';
    } catch(e) {
      status.textContent = 'Câmera indisponível. Use o PIN.';
      status.className = 'err';
    }
  }

  document.getElementById('captureBtn')?.addEventListener('click', function() {
    const status = document.getElementById('facial-status');
    status.textContent = 'Analisando... Aguarde';
    // Simulação: em produção, usar face-api.js para extrair vetor e comparar
    setTimeout(() => {
      // Simula score de 0.85 para fins de demo
      document.getElementById('facial_score').value = '0.85';
      status.textContent = '✓ Rosto identificado!';
      status.className = 'ok';
    }, 1500);
  });
</script>
@endpush
