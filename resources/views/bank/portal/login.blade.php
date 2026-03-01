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
  .login-logo { text-align: center; margin-bottom: 1.75rem; }
  .login-logo-icon {
    width: 68px; height: 68px;
    background: linear-gradient(135deg, #3b82f6, #1d4ed8);
    border-radius: 20px;
    display: flex; align-items: center; justify-content: center;
    font-size: 2rem; font-weight: 800; color: #fff;
    margin: 0 auto 0.75rem;
    box-shadow: 0 12px 30px rgba(59,130,246,0.45);
  }
  .login-logo h1 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.5px; }
  .login-logo p  { color: var(--bank-muted); font-size: 0.8rem; margin: 0; }

  /* Cards de seleção de modo */
  .method-selector { display: grid; grid-template-columns: 1fr 1fr; gap: 0.5rem; margin-bottom: 1.5rem; }
  .method-card {
    display: flex; flex-direction: column; align-items: center; gap: 0.35rem;
    padding: 0.9rem 0.75rem;
    background: rgba(255,255,255,0.04);
    border: 1.5px solid var(--bank-border); border-radius: 14px;
    cursor: pointer; transition: all 0.2s;
    font-size: 0.8rem; font-weight: 600; color: var(--bank-muted);
    user-select: none; -webkit-tap-highlight-color: transparent;
  }
  .method-card i { font-size: 1.4rem; }
  .method-card.active {
    border-color: var(--bank-primary);
    background: rgba(59,130,246,0.1);
    color: var(--bank-primary);
    box-shadow: 0 0 0 3px rgba(59,130,246,0.12);
  }
  .method-card:hover:not(.active) { border-color: rgba(255,255,255,0.18); color: var(--bank-text); }

  /* --- WEBCAM (corrigido) --- */
  #webcam-box {
    width: 100%;
    height: 260px;              /* altura fixa — evita distorção */
    border-radius: 16px;
    overflow: hidden;
    background: #000;
    position: relative;
    margin-bottom: 0.75rem;
    flex-shrink: 0;
  }
  #webcam-video {
    width: 100%; height: 100%;
    object-fit: cover;          /* cobre sem distorcer */
    display: block;
    transform: scaleX(-1);      /* espelhar como selfie */
  }
  #cam-overlay {
    position: absolute; inset: 0;
    display: flex; align-items: center; justify-content: center;
    pointer-events: none;
  }
  #face-oval {
    width: 155px; height: 200px;
    border: 2.5px solid rgba(59,130,246,0.85);
    border-radius: 50%;
    box-shadow: 0 0 0 4px rgba(59,130,246,0.18), inset 0 0 0 2px rgba(59,130,246,0.08);
    transition: border-color 0.4s, box-shadow 0.4s;
    animation: oval-pulse 2.4s ease-in-out infinite;
  }
  #face-oval.ok {
    border-color: rgba(16,185,129,0.9);
    box-shadow: 0 0 0 4px rgba(16,185,129,0.25);
    animation: none;
  }
  #face-oval.err {
    border-color: rgba(239,68,68,0.9);
    box-shadow: 0 0 0 4px rgba(239,68,68,0.2);
    animation: none;
  }
  @keyframes oval-pulse {
    0%, 100% { box-shadow: 0 0 0 4px rgba(59,130,246,0.15); }
    50%       { box-shadow: 0 0 0 9px rgba(59,130,246,0.3); }
  }
  #cam-status {
    text-align: center; font-size: 0.77rem; color: var(--bank-muted);
    margin-bottom: 0.6rem; min-height: 1.3em;
  }
  #cam-status.ok  { color: #10b981; font-weight: 600; }
  #cam-status.err { color: #ef4444; }

  /* PIN — bolinhas */
  .pin-display {
    display: flex; justify-content: center; gap: 0.55rem; margin-bottom: 0.75rem;
  }
  .pin-dot {
    width: 13px; height: 13px; border-radius: 50%;
    border: 2px solid rgba(255,255,255,0.2);
    background: transparent; transition: all 0.18s;
  }
  .pin-dot.filled {
    background: var(--bank-primary); border-color: var(--bank-primary);
    box-shadow: 0 0 8px rgba(59,130,246,0.5);
  }

  /* Numpad */
  .numpad-grid {
    display: grid; grid-template-columns: repeat(3, 1fr); gap: 0.4rem; margin-top: 0.25rem;
  }
  .numpad-key {
    display: flex; align-items: center; justify-content: center;
    height: 3.1rem;
    background: rgba(255,255,255,0.05); border: 1px solid var(--bank-border);
    border-radius: 12px; font-size: 1.15rem; font-weight: 600; color: var(--bank-text);
    cursor: pointer; transition: all 0.15s; user-select: none;
    -webkit-tap-highlight-color: transparent;
  }
  .numpad-key:hover  { background: rgba(59,130,246,0.1); border-color: rgba(59,130,246,0.35); }
  .numpad-key:active { transform: scale(0.93); background: rgba(59,130,246,0.18); }
  .numpad-key.del { color: var(--bank-muted); font-size: 0.95rem; }
  .numpad-key.confirm {
    background: linear-gradient(135deg,#3b82f6,#1d4ed8); border-color: transparent; color: #fff;
    box-shadow: 0 4px 12px rgba(59,130,246,0.35);
  }
  .numpad-key.confirm:hover { box-shadow: 0 6px 18px rgba(59,130,246,0.48); }

  /* Spinner no botão de captura */
  .spin { animation: spin 1s linear infinite; display: inline-block; }
  @keyframes spin { to { transform: rotate(360deg); } }
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

  {{-- Seletor de método --}}
  <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--bank-muted);margin-bottom:0.4rem;">
    Como deseja entrar?
  </p>
  <div class="method-selector">
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
    <input type="hidden" name="metodo"        id="metodo"       value="usuario">
    <input type="hidden" name="pin"           id="pinHidden"    value="">
    <input type="hidden" name="facial_score"  id="facialScore"  value="0">
    <input type="hidden" name="facial_codigo" id="facialCodigo" value="">

    {{-- ===== PAINEL: USUÁRIO + PIN ===== --}}
    <div id="panel-usuario">
      <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--bank-muted);margin-bottom:0.35rem;">
        Passo 1 — Identificação
      </p>
      <div class="bank-input-group mb-3">
        <label class="bank-input-label">Código ou Usuário</label>
        <input type="text" name="identificacao" id="inputId" class="bank-input"
               placeholder="Ex.: MNL-0000001-MAX ou webmaximo"
               value="{{ old('identificacao') }}" autocomplete="off">
      </div>

      <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--bank-muted);margin-bottom:0.35rem;">
        Passo 2 — PIN
      </p>
      <div class="pin-display" id="dotsU">
        @for($i=0;$i<8;$i++)<div class="pin-dot" id="du{{$i}}"></div>@endfor
      </div>
      <div class="numpad-grid">
        @foreach([1,2,3,4,5,6,7,8,9,'del',0,'ok'] as $k)
          @if($k==='del')
            <div class="numpad-key del" onclick="numpad('del')"><i class="fas fa-delete-left"></i></div>
          @elseif($k==='ok')
            <div class="numpad-key confirm" onclick="submitLogin()"><i class="fas fa-unlock-alt"></i></div>
          @else
            <div class="numpad-key" onclick="numpad('{{$k}}')">{{$k}}</div>
          @endif
        @endforeach
      </div>
    </div>

    {{-- ===== PAINEL: FACIAL + PIN ===== --}}
    <div id="panel-facial" style="display:none;">
      <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--bank-muted);margin-bottom:0.4rem;">
        Passo 1 — Reconhecimento Facial
      </p>
      <div id="webcam-box">
        <video id="webcam-video" autoplay playsinline muted></video>
        <div id="cam-overlay"><div id="face-oval"></div></div>
      </div>
      <div id="cam-status">Iniciando câmera...</div>

      <button type="button" id="captureBtn" class="bank-btn bank-btn-primary bank-btn-full mb-3">
        <i class="fas fa-camera me-2"></i>Capturar e Reconhecer Rosto
      </button>

      {{-- PIN aparece APÓS reconhecimento --}}
      <div id="pin-facial-section" style="display:none;">
        <div class="bank-divider"></div>
        <p style="font-size:0.68rem;font-weight:700;text-transform:uppercase;letter-spacing:0.8px;color:var(--bank-muted);margin-bottom:0.35rem;">
          Passo 2 — Confirme seu PIN
        </p>
        <div class="pin-display" id="dotsF">
          @for($i=0;$i<8;$i++)<div class="pin-dot" id="df{{$i}}"></div>@endfor
        </div>
        <div class="numpad-grid">
          @foreach([1,2,3,4,5,6,7,8,9,'del',0,'ok'] as $k)
            @if($k==='del')
              <div class="numpad-key del" onclick="numpad('del')"><i class="fas fa-delete-left"></i></div>
            @elseif($k==='ok')
              <div class="numpad-key confirm" onclick="submitLogin()"><i class="fas fa-unlock-alt"></i></div>
            @else
              <div class="numpad-key" onclick="numpad('{{$k}}')">{{$k}}</div>
            @endif
          @endforeach
        </div>
      </div>
    </div>

  </form>

  <div style="text-align:center;margin-top:1.25rem;">
    <p style="color:var(--bank-muted);font-size:0.7rem;">
      <i class="fas fa-shield-alt me-1"></i>Conexão criptografada — MaxCheckout
    </p>
  </div>
</div>
@endsection

@push('bank-scripts')
<script>
  /* ============================================================
     ESTADO GLOBAL
  ============================================================ */
  let pinValor   = '';
  let modoAtivo  = 'usuario';
  let facialOK   = false;
  let mediaStream = null;
  const CSRF     = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

  /* ============================================================
     SELETOR DE MODO
  ============================================================ */
  function selecionarMetodo(modo) {
    // Para câmera anterior se houver
    if (mediaStream) {
      mediaStream.getTracks().forEach(t => t.stop());
      mediaStream = null;
    }

    modoAtivo  = modo;
    facialOK   = false;
    pinValor   = '';
    document.getElementById('metodo').value     = modo;
    document.getElementById('pinHidden').value  = '';
    document.getElementById('facialScore').value  = '0';
    document.getElementById('facialCodigo').value = '';

    document.getElementById('card-usuario').classList.toggle('active', modo === 'usuario');
    document.getElementById('card-facial').classList.toggle('active', modo === 'facial');
    document.getElementById('panel-usuario').style.display = modo === 'usuario' ? 'block' : 'none';
    document.getElementById('panel-facial').style.display  = modo === 'facial'  ? 'block' : 'none';
    document.getElementById('pin-facial-section').style.display = 'none';
    document.getElementById('face-oval').className = '';

    atualizarDots();
    if (modo === 'facial') iniciarWebcam();
  }

  /* ============================================================
     NUMPAD
  ============================================================ */
  function numpad(tecla) {
    // No modo facial, só aceita PIN depois do reconhecimento
    if (modoAtivo === 'facial' && !facialOK) return;

    if (tecla === 'del') {
      pinValor = pinValor.slice(0, -1);
    } else if (pinValor.length < 8) {
      pinValor += tecla;
    }
    document.getElementById('pinHidden').value = pinValor;
    atualizarDots();
  }

  function atualizarDots() {
    const prefix = modoAtivo === 'usuario' ? 'du' : 'df';
    for (let i = 0; i < 8; i++) {
      const d = document.getElementById(prefix + i);
      if (d) d.classList.toggle('filled', i < pinValor.length);
    }
  }

  /* ============================================================
     SUBMIT
  ============================================================ */
  function submitLogin() {
    if (pinValor.length < 4) {
      alerta('Digite seu PIN (mínimo 4 dígitos).', 'warning'); return;
    }
    if (modoAtivo === 'usuario' && !document.getElementById('inputId').value.trim()) {
      alerta('Informe seu usuário ou código.', 'warning');
      document.getElementById('inputId').focus(); return;
    }
    if (modoAtivo === 'facial' && !facialOK) {
      alerta('Capture seu rosto primeiro.', 'warning'); return;
    }
    document.getElementById('loginForm').submit();
  }

  /* ============================================================
     WEBCAM
  ============================================================ */
  async function iniciarWebcam() {
    const status = document.getElementById('cam-status');
    try {
      mediaStream = await navigator.mediaDevices.getUserMedia({
        video: { facingMode: 'user', width: { ideal: 640 }, height: { ideal: 480 } }
      });
      document.getElementById('webcam-video').srcObject = mediaStream;
      status.textContent = 'Posicione seu rosto no guia oval';
      status.className   = '';
    } catch(e) {
      status.textContent = 'Câmera indisponível. Use Usuário + PIN.';
      status.className   = 'err';
      document.getElementById('captureBtn').disabled = true;
    }
  }

  /* ============================================================
     CAPTURAR ROSTO → chamar servidor para reconhecer
  ============================================================ */
  document.getElementById('captureBtn')?.addEventListener('click', async function () {
    const btn    = this;
    const status = document.getElementById('cam-status');
    const oval   = document.getElementById('face-oval');
    const video  = document.getElementById('webcam-video');

    // Captura frame do vídeo
    const canvas = document.createElement('canvas');
    canvas.width  = video.videoWidth  || 640;
    canvas.height = video.videoHeight || 480;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0);

    // Extrai vetor facial via face-api.js (se disponível) ou usa simulação
    let vetorJSON = null;

    if (typeof faceapi !== 'undefined') {
      // Usa face-api.js real para extrair o descriptor de 128 floats
      status.textContent = 'Detectando rosto...';
      try {
        const detection = await faceapi
          .detectSingleFace(canvas, new faceapi.TinyFaceDetectorOptions())
          .withFaceLandmarks()
          .withFaceDescriptor();

        if (!detection) {
          oval.className = 'err';
          status.textContent = 'Nenhum rosto detectado. Tente novamente.';
          status.className = 'err';
          return;
        }
        vetorJSON = JSON.stringify(Array.from(detection.descriptor));
      } catch(e) {
        oval.className = 'err';
        status.textContent = 'Erro ao processar rosto. Tente novamente.';
        status.className = 'err';
        return;
      }
    } else {
      // face-api.js não carregado — informa ao usuário
      oval.className = 'err';
      status.textContent = 'Módulo facial não carregado. Use Usuário + PIN.';
      status.className   = 'err';
      alerta('face-api.js não está carregado. Use o modo Usuário + PIN no momento.', 'warning');
      return;
    }

    // Exibe spinner no botão
    btn.disabled   = true;
    btn.innerHTML  = '<i class="fas fa-circle-notch spin me-2"></i>Reconhecendo...';

    // Chama o endpoint server-side para comparar o vetor com o banco
    try {
      const resp = await fetch("{{ route('banco.reconhecer') }}", {
        method: 'POST',
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded',
          'X-CSRF-TOKEN': CSRF,
        },
        body: 'vetor=' + encodeURIComponent(vetorJSON),
      });

      const data = await resp.json();

      if (data.ok) {
        // SUCESSO — rosto reconhecido
        document.getElementById('facialScore').value  = data.score.toString();
        document.getElementById('facialCodigo').value = data.codigo;

        oval.className = 'ok';
        status.textContent = `✓ Olá! Conta encontrada. Agora digite seu PIN.`;
        status.className   = 'ok';
        facialOK = true;

        btn.innerHTML = '<i class="fas fa-check-circle me-2"></i>Rosto Reconhecido';
        btn.style.background = 'linear-gradient(135deg,#10b981,#059669)';

        // Mostra seção do PIN
        const pinSection = document.getElementById('pin-facial-section');
        pinSection.style.display = 'block';
        setTimeout(() => pinSection.scrollIntoView({ behavior: 'smooth', block: 'nearest' }), 100);
      } else {
        // FALHA — rosto não reconhecido
        oval.className = 'err';
        status.textContent = data.msg || 'Rosto não reconhecido. Tente novamente.';
        status.className   = 'err';

        btn.disabled  = false;
        btn.innerHTML = '<i class="fas fa-camera me-2"></i>Tentar Novamente';
      }
    } catch(err) {
      oval.className = 'err';
      status.textContent = 'Erro de comunicação com o servidor.';
      status.className   = 'err';
      btn.disabled  = false;
      btn.innerHTML = '<i class="fas fa-camera me-2"></i>Tentar Novamente';
    }
  });

  /* ============================================================
     ALERTA FLUTUANTE
  ============================================================ */
  function alerta(msg, tipo) {
    let el = document.getElementById('alertFloat');
    if (!el) {
      el = document.createElement('div');
      el.id = 'alertFloat';
      el.style.cssText = 'position:fixed;top:1rem;left:50%;transform:translateX(-50%);z-index:9999;min-width:260px;max-width:88vw;';
      document.body.appendChild(el);
    }
    const cor = tipo === 'warning' ? 'bank-alert-warning' : 'bank-alert-danger';
    el.className = 'bank-alert ' + cor;
    el.style.display = 'flex';
    el.innerHTML = '<i class="fas fa-exclamation-triangle"></i> ' + msg;
    clearTimeout(el._t);
    el._t = setTimeout(() => el.style.display = 'none', 3500);
  }
</script>
@endpush
