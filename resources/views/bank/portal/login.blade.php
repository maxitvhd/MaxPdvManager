@extends('bank.portal.layout')
@section('titulo', 'Login')

@push('bank-styles')
<style>
  .bank-nav { display: none; }
  .bank-main {
    padding: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: 100vh;
    width: 100%;
    max-width: 100%;
  }
  .login-box { width: 100%; max-width: 420px; }

  /* Logo */
  .login-logo { text-align: center; margin-bottom: 2rem; }
  .login-logo-icon {
    width: 72px; height: 72px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: #fff;
    margin: 0 auto 0.75rem;
    box-shadow: 0 15px 35px rgba(59,130,246,0.4);
  }
  .login-logo h1 { font-size: 1.6rem; font-weight: 800; letter-spacing: -0.5px; }
  .login-logo p { color: var(--bank-muted); font-size: 0.82rem; }

  /* Seletor de método */
  .method-selector {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
  }
  .method-card {
    display: flex; flex-direction: column; align-items: center;
    gap: 0.4rem; padding: 1rem 0.75rem;
    background: rgba(255,255,255,0.04);
    border: 1.5px solid var(--bank-border);
    border-radius: 14px;
    cursor: pointer; transition: all 0.2s;
    font-size: 0.82rem; font-weight: 600; color: var(--bank-muted);
    user-select: none;
  }
  .method-card i { font-size: 1.5rem; }
  .method-card.active {
    border-color: var(--bank-primary);
    background: rgba(59,130,246,0.1);
    color: var(--bank-primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.15);
  }
  .method-card:hover:not(.active) {
    border-color: rgba(255,255,255,0.2);
    color: var(--bank-text);
  }

  /* Webcam */
  #webcam-box {
    position: relative; border-radius: 16px; overflow: hidden;
    background: #000; aspect-ratio: 4/3; margin-bottom: 0.75rem;
  }
  #webcam-video { width: 100%; height: 100%; object-fit: cover; }
  #face-guide-wrap {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
  }
  #face-oval {
    width: 170px; height: 215px;
    border: 2.5px solid rgba(59,130,246,0.85);
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(59,130,246,0.2), inset 0 0 30px rgba(59,130,246,0.1);
    animation: oval-pulse 2.2s ease-in-out infinite;
  }
  #face-oval.ok {
    border-color: rgba(16,185,129,0.9);
    box-shadow: 0 0 0 4px rgba(16,185,129,0.25);
    animation: none;
  }
  @keyframes oval-pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(59,130,246,0.2); }
    50% { box-shadow: 0 0 0 8px rgba(59,130,246,0.35); }
  }
  #cam-status {
    text-align: center; font-size: 0.78rem; color: var(--bank-muted);
    margin-bottom: 0.5rem; min-height: 1.3em;
  }
  #cam-status.ok  { color: #10b981; font-weight: 600; }
  #cam-status.err { color: #ef4444; }

  /* Numpad */
  .numpad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0.4rem;
    margin-top: 0.5rem;
  }
  .numpad-key {
    display: flex; align-items: center; justify-content: center;
    height: 3.2rem;
    background: rgba(255,255,255,0.05);
    border: 1px solid var(--bank-border);
    border-radius: 12px;
    font-size: 1.15rem; font-weight: 600; color: var(--bank-text);
    cursor: pointer; transition: all 0.15s;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
  }
  .numpad-key:hover { background: rgba(59,130,246,0.12); border-color: rgba(59,130,246,0.4); }
  .numpad-key:active { transform: scale(0.94); background: rgba(59,130,246,0.2); }
  .numpad-key.del { color: var(--bank-muted); font-size: 1rem; }
  .numpad-key.confirm {
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-color: transparent; color: #fff;
    box-shadow: 0 4px 12px rgba(59,130,246,0.35);
  }
  .numpad-key.confirm:hover { box-shadow: 0 6px 18px rgba(59,130,246,0.45); }

  /* Display PIN */
  .pin-display {
    display: flex; justify-content: center; gap: 0.6rem;
    margin-bottom: 0.75rem;
  }
  .pin-dot {
    width: 12px; height: 12px;
    border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.25);
    background: transparent;
    transition: all 0.2s;
  }
  .pin-dot.filled {
    background: var(--bank-primary);
    border-color: var(--bank-primary);
    box-shadow: 0 0 8px rgba(59,130,246,0.5);
  }

  /* Seção de passos */
  .step-header {
    font-size: 0.7rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.8px;
    color: var(--bank-muted); margin-bottom: 0.4rem;
  }
</style>
@endpush

@section('bank-content')
<div class="login-box">

  {{-- Logo --}}
  <div class="login-logo">
    <div class="login-logo-icon">M</div>
    <h1>MaxBank</h1>
    <p>Portal Seguro do Cliente</p>
  </div>

  @if(session('error'))
    <div class="bank-alert bank-alert-danger mb-3">
      <i class="fas fa-exclamation-circle"></i>{{ session('error') }}
    </div>
  @endif

  {{-- Seletor de Método --}}
  <p class="step-header">Como deseja entrar?</p>
  <div class="method-selector mb-4">
    <div class="method-card active" id="card-usuario" onclick="selecionarMetodo('usuario')">
      <i class="fas fa-user-circle"></i>
      <span>Usuário + PIN</span>
    </div>
    <div class="method-card" id="card-facial" onclick="selecionarMetodo('facial')">
      <i class="fas fa-camera"></i>
      <span>Facial + PIN</span>
    </div>
  </div>

  <form action="{{ route('banco.autenticar') }}" method="POST" id="loginForm">
    @csrf
    <input type="hidden" name="metodo" id="metodo" value="usuario">
    <input type="hidden" name="pin" id="pinHidden" value="">
    <input type="hidden" name="facial_score" id="facialScore" value="0">
    <input type="hidden" name="facial_codigo" id="facialCodigo" value="">

    {{-- ============ PAINEL: USUÁRIO + PIN ============ --}}
    <div id="panel-usuario">
      <p class="step-header mb-1">Passo 1 — Identificação</p>
      <div class="bank-input-group mb-3">
        <label class="bank-input-label">Código ou Usuário</label>
        <input type="text" name="identificacao" id="inputIdentificacao" class="bank-input"
               placeholder="Ex.: MNL-0000001-MAX ou webmaximo"
               value="{{ old('identificacao') }}" autocomplete="off">
      </div>

      <p class="step-header mb-1">Passo 2 — PIN de Acesso</p>
      <div class="pin-display" id="pinDotsUsuario">
        @for($i=0;$i<8;$i++)<div class="pin-dot" id="dot-u-{{$i}}"></div>@endfor
      </div>
      <div class="numpad-grid">
        @foreach([1,2,3,4,5,6,7,8,9,'del',0,'ok'] as $k)
          @if($k==='del')
            <div class="numpad-key del" onclick="numpad('del','usuario')">
              <i class="fas fa-delete-left"></i>
            </div>
          @elseif($k==='ok')
            <div class="numpad-key confirm" onclick="submitLogin()">
              <i class="fas fa-arrow-right"></i>
            </div>
          @else
            <div class="numpad-key" onclick="numpad('{{$k}}','usuario')">{{$k}}</div>
          @endif
        @endforeach
      </div>
    </div>

    {{-- ============ PAINEL: FACIAL + PIN ============ --}}
    <div id="panel-facial" style="display:none;">
      <p class="step-header mb-1">Passo 1 — Reconhecimento Facial</p>
      <div id="webcam-box">
        <video id="webcam-video" autoplay playsinline muted></video>
        <div id="face-guide-wrap"><div id="face-oval"></div></div>
      </div>
      <div id="cam-status">Iniciando câmera...</div>
      <button type="button" id="captureBtn" class="bank-btn bank-btn-primary bank-btn-full mb-3">
        <i class="fas fa-camera me-2"></i>Capturar Rosto
      </button>

      {{-- PIN aparece após capturar o rosto --}}
      <div id="pin-facial-section" style="display:none;">
        <p class="step-header mb-1">Passo 2 — Confirme seu PIN</p>
        <div class="pin-display" id="pinDotsFacial">
          @for($i=0;$i<8;$i++)<div class="pin-dot" id="dot-f-{{$i}}"></div>@endfor
        </div>
        <div class="numpad-grid">
          @foreach([1,2,3,4,5,6,7,8,9,'del',0,'ok'] as $k)
            @if($k==='del')
              <div class="numpad-key del" onclick="numpad('del','facial')">
                <i class="fas fa-delete-left"></i>
              </div>
            @elseif($k==='ok')
              <div class="numpad-key confirm" onclick="submitLogin()">
                <i class="fas fa-arrow-right"></i>
              </div>
            @else
              <div class="numpad-key" onclick="numpad('{{$k}}','facial')">{{$k}}</div>
            @endif
          @endforeach
        </div>
      </div>
    </div>

  </form>

  <div style="text-align:center;margin-top:1.5rem;">
    <p style="color:var(--bank-muted);font-size:0.72rem;">
      <i class="fas fa-shield-alt me-1"></i>Conexão criptografada — MaxCheckout
    </p>
  </div>
</div>
@endsection

@push('bank-scripts')
<script>
  let pinValor    = '';       // PIN atual sendo digitado
  let modoAtivo   = 'usuario'; // 'usuario' ou 'facial'
  let facialOK    = false;

  /* ===== SELETOR DE MÉTODO ===== */
  function selecionarMetodo(modo) {
    modoAtivo = modo;
    document.getElementById('metodo').value = modo;
    document.getElementById('card-usuario').classList.toggle('active', modo === 'usuario');
    document.getElementById('card-facial').classList.toggle('active', modo === 'facial');
    document.getElementById('panel-usuario').style.display = modo === 'usuario' ? 'block' : 'none';
    document.getElementById('panel-facial').style.display  = modo === 'facial'  ? 'block' : 'none';
    pinValor = '';
    atualizarDots();
    if (modo === 'facial') iniciarWebcam();
  }

  /* ===== NUMPAD ===== */
  function numpad(tecla, painel) {
    if (painel !== modoAtivo) return;
    if (modoAtivo === 'facial' && !facialOK) return; // PIN só depois do facial

    if (tecla === 'del') {
      pinValor = pinValor.slice(0, -1);
    } else {
      if (pinValor.length < 8) pinValor += tecla;
    }
    atualizarDots();
    document.getElementById('pinHidden').value = pinValor;
  }

  function atualizarDots() {
    const prefix = modoAtivo === 'usuario' ? 'dot-u-' : 'dot-f-';
    for (let i = 0; i < 8; i++) {
      const dot = document.getElementById(prefix + i);
      if (dot) dot.classList.toggle('filled', i < pinValor.length);
    }
  }

  /* ===== SUBMIT ===== */
  function submitLogin() {
    if (pinValor.length < 4) {
      mostrarAlerta('Digite seu PIN (mínimo 4 dígitos).');
      return;
    }
    if (modoAtivo === 'usuario') {
      const id = document.getElementById('inputIdentificacao').value.trim();
      if (!id) {
        mostrarAlerta('Informe seu usuário ou código.');
        document.getElementById('inputIdentificacao').focus();
        return;
      }
    }
    if (modoAtivo === 'facial' && !facialOK) {
      mostrarAlerta('Capture seu rosto primeiro.');
      return;
    }
    document.getElementById('loginForm').submit();
  }

  function mostrarAlerta(msg) {
    let el = document.getElementById('alertDinamico');
    if (!el) {
      el = document.createElement('div');
      el.id = 'alertDinamico';
      el.className = 'bank-alert bank-alert-warning';
      el.style.cssText = 'position:fixed;top:1rem;left:50%;transform:translateX(-50%);z-index:999;min-width:260px;max-width:90vw;';
      document.body.appendChild(el);
    }
    el.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + msg;
    el.style.display = 'flex';
    clearTimeout(el._timer);
    el._timer = setTimeout(() => el.style.display = 'none', 3000);
  }

  /* ===== WEBCAM ===== */
  let mediaStream = null;

  async function iniciarWebcam() {
    const status = document.getElementById('cam-status');
    try {
      mediaStream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
      document.getElementById('webcam-video').srcObject = mediaStream;
      status.textContent = 'Posicione seu rosto no guia oval e clique em Capturar';
      status.className = '';
    } catch(e) {
      status.textContent = 'Câmera indisponível. Use o modo Usuário + PIN.';
      status.className = 'err';
      document.getElementById('captureBtn').disabled = true;
    }
  }

  document.getElementById('captureBtn')?.addEventListener('click', function() {
    const status  = document.getElementById('cam-status');
    const oval    = document.getElementById('face-oval');
    const btn     = this;
    btn.disabled  = true;
    status.textContent = 'Analisando rosto... aguarde';
    status.className   = '';

    // Em produção: carregar face-api.js e comparar o vetor com o banco
    // Aqui simulamos detecção bem-sucedida após 1.5s
    setTimeout(() => {
      // Simula score 0.88 — substituir pela comparação real do face-api.js
      const score = 0.88;
      document.getElementById('facialScore').value = score.toString();

      oval.classList.add('ok');
      status.textContent = '✓ Rosto reconhecido! Agora digite seu PIN.';
      status.className   = 'ok';
      facialOK           = true;
      btn.innerHTML      = '<i class="fas fa-check-circle me-2"></i>Rosto Capturado';
      btn.style.background = 'linear-gradient(135deg,#10b981,#059669)';

      // Mostrar seção do PIN
      document.getElementById('pin-facial-section').style.display = 'block';
      document.getElementById('pin-facial-section').scrollIntoView({ behavior: 'smooth' });
    }, 1500);
  });

  /* Para câmera ao sair do modo facial */
  function selecionarMetodo(modo) {
    if (modoAtivo === 'facial' && mediaStream) {
      mediaStream.getTracks().forEach(t => t.stop());
      mediaStream = null;
    }
    modoAtivo = modo;
    facialOK  = false;
    pinValor  = '';
    document.getElementById('metodo').value = modo;
    document.getElementById('card-usuario').classList.toggle('active', modo === 'usuario');
    document.getElementById('card-facial').classList.toggle('active', modo === 'facial');
    document.getElementById('panel-usuario').style.display = modo === 'usuario' ? 'block' : 'none';
    document.getElementById('panel-facial').style.display  = modo === 'facial'  ? 'block' : 'none';
    document.getElementById('pin-facial-section').style.display = 'none';
    document.getElementById('face-oval').classList.remove('ok');
    document.getElementById('pinHidden').value = '';
    atualizarDots();
    if (modo === 'facial') iniciarWebcam();
  }
</script>
@endpush
