<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,shrink-to-fit=no">
  <title>Ativação de Conta — MaxBank</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
  <style>
    :root {
      --dark: #060b16; --card:#111827; --border:rgba(255,255,255,0.08);
      --text:#e2e8f0; --muted:#94a3b8; --primary:#3b82f6; --success:#10b981; --danger:#ef4444;
    }
    * { margin:0;padding:0;box-sizing:border-box; }
    body { font-family:'Inter',sans-serif; background:var(--dark); color:var(--text); min-height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; padding:1rem; }
    .box { width:100%; max-width:440px; }
    .logo { text-align:center; margin-bottom:2rem; }
    .logo-icon { width:72px;height:72px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:800;color:#fff;margin:0 auto 0.75rem;box-shadow:0 15px 35px rgba(59,130,246,0.35); }
    .logo h1 { font-size:1.5rem;font-weight:800;letter-spacing:-0.5px; }
    .logo p { color:var(--muted);font-size:0.85rem; }
    .card { background:var(--card);border:1px solid var(--border);border-radius:16px;padding:1.5rem;margin-bottom:1rem; }
    .step { display:flex;align-items:center;gap:0.75rem;margin-bottom:0.5rem; }
    .step-num { width:28px;height:28px;border-radius:50%;background:var(--primary);display:flex;align-items:center;justify-content:center;font-size:0.78rem;font-weight:700;flex-shrink:0; }
    .step-num.done { background:var(--success); }
    .step-num.active { box-shadow:0 0 0 4px rgba(59,130,246,0.3); }
    .step-label { font-size:0.85rem;font-weight:600; }
    .label { font-size:0.72rem;font-weight:600;text-transform:uppercase;letter-spacing:0.7px;color:var(--muted);display:block;margin-bottom:0.4rem; }
    input { width:100%;background:rgba(255,255,255,0.05);border:1px solid var(--border);border-radius:10px;padding:0.7rem 1rem;font-size:0.92rem;color:var(--text);margin-bottom:0.75rem;font-family:'Inter',sans-serif;transition:border-color .2s,box-shadow .2s; }
    input:focus { outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(59,130,246,.15);background:rgba(59,130,246,.05); }
    input::placeholder { color:var(--muted); }
    .btn { display:inline-flex;align-items:center;justify-content:center;gap:0.4rem;padding:0.7rem 1.25rem;border-radius:10px;font-size:0.9rem;font-weight:600;cursor:pointer;border:none;text-decoration:none;width:100%;margin-bottom:0.5rem;transition:all .2s; }
    .btn-primary { background:linear-gradient(135deg,#3b82f6,#1d4ed8);color:#fff;box-shadow:0 4px 15px rgba(59,130,246,.35); }
    .btn-primary:hover { transform:translateY(-1px);box-shadow:0 6px 20px rgba(59,130,246,.45); }
    .btn-success { background:linear-gradient(135deg,#10b981,#059669);color:#fff;box-shadow:0 4px 15px rgba(16,185,129,.35); }
    .alert { padding:0.85rem 1rem;border-radius:10px;margin-bottom:1rem;font-size:0.85rem;display:flex;align-items:flex-start;gap:0.5rem; }
    .alert-danger  { background:rgba(239,68,68,.1);border:1px solid rgba(239,68,68,.3);color:#ef4444; }
    .alert-warning { background:rgba(245,158,11,.1);border:1px solid rgba(245,158,11,.3);color:#f59e0b; }
    .alert-success { background:rgba(16,185,129,.1);border:1px solid rgba(16,185,129,.3);color:#10b981; }
    /* Webcam */
    #webcam-box { border-radius:16px;overflow:hidden;background:#000;aspect-ratio:4/3;position:relative;margin-bottom:0.75rem; }
    video { width:100%;height:100%;object-fit:cover; }
    #cam-guide { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;pointer-events:none; }
    #cam-guide div { width:160px;height:200px;border:2px solid rgba(59,130,246,.8);border-radius:50%;box-shadow:0 0 0 4px rgba(59,130,246,.2);animation:pulse 2s infinite; }
    @keyframes pulse { 0%,100%{box-shadow:0 0 0 4px rgba(59,130,246,.2)}50%{box-shadow:0 0 0 8px rgba(59,130,246,.35)} }
    #cam-status { text-align:center;font-size:0.78rem;color:var(--muted);margin-bottom:0.5rem;min-height:1.2em; }
    #cam-status.ok  { color:var(--success);font-weight:600; }
    #cam-status.err { color:var(--danger); }
    .divider { height:1px;background:var(--border);margin:1rem 0; }
    .progress { height:6px;background:rgba(255,255,255,.08);border-radius:3px;overflow:hidden;margin:0.25rem 0 0.75rem; }
    .progress-bar { height:100%;border-radius:3px;background:linear-gradient(90deg,#3b82f6,#8b5cf6);transition:width .3s; }
    small { font-size:0.72rem;color:var(--muted); }
  </style>
</head>
<body>
<div class="box">
  <div class="logo">
    <div class="logo-icon">M</div>
    <h1>MaxBank</h1>
    <p>Ativação de Conta</p>
  </div>

  {{-- Erro --}}
  @if(isset($erro))
    <div class="card" style="text-align:center;">
      <div class="alert alert-danger">
        <i class="fas fa-exclamation-circle"></i>
        <div>
          <strong>Link Inválido</strong><br>{{ $erro }}
          @if(isset($lojeNome)) <br><small>Loja: {{ $lojeNome }}</small> @endif
        </div>
      </div>
      <i class="fas fa-link-slash" style="font-size:2.5rem;color:#ef4444;margin-bottom:0.75rem;"></i>
      <p style="font-size:0.85rem;color:var(--muted);">Contate sua loja para solicitar um novo link.</p>
    </div>

  {{-- Sucesso --}}
  @elseif(isset($sucesso) && $sucesso)
    <div class="card" style="text-align:center;">
      <i class="fas fa-check-circle" style="font-size:3rem;color:#10b981;margin-bottom:1rem;"></i>
      <h3 style="font-weight:700;margin-bottom:0.5rem;">Conta Ativada!</h3>
      <p style="color:var(--muted);font-size:0.85rem;margin-bottom:1.25rem;">
        Seu PIN e dados biométricos foram configurados com sucesso. Aguarde a aprovação da sua loja para acessar o portal.
      </p>
      <a href="{{ $loginUrl }}" class="btn btn-primary">
        <i class="fas fa-sign-in-alt me-1"></i>Ir para o Login
      </a>
    </div>

  {{-- Formulário de Ativação --}}
  @else
    {{-- Progresso --}}
    <div class="card" style="padding:1rem 1.5rem;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.25rem;">
        <small>Progresso de ativação</small><small>Passo 1 de 2</small>
      </div>
      <div class="progress"><div class="progress-bar" style="width:50%;"></div></div>
      <p style="font-size:0.78rem;color:var(--muted);">Bem-vindo(a), <strong>{{ $cliente->nome }}</strong>!</p>
    </div>

    <form action="{{ route('banco.ativar.salvar', $cliente->link_ativacao) }}" method="POST" enctype="multipart/form-data" id="formAtivacao">
      @csrf

      {{-- Passo 1: Foto Facial --}}
      <div class="card" id="step1">
        <div class="step" style="margin-bottom:1rem;">
          <div class="step-num active">1</div>
          <span class="step-label"><i class="fas fa-camera me-1" style="color:#3b82f6;"></i>Foto Facial</span>
        </div>
        <p style="font-size:0.8rem;color:var(--muted);margin-bottom:1rem;">Posicione seu rosto no centro da câmera. Esta foto será usada para reconhecimento biométrico.</p>

        <div id="webcam-box">
          <video id="camVideo" autoplay playsinline muted></video>
          <div id="cam-guide"><div></div></div>
        </div>
        <div id="cam-status">Iniciando câmera...</div>

        <canvas id="canvas" style="display:none;"></canvas>
        <input type="hidden" name="facial_vector" id="facialVector">

        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.5rem;">
          <button type="button" class="btn btn-primary" id="captureBtn" style="width:100%;">
            <i class="fas fa-camera me-1"></i>Capturar
          </button>
          <label class="btn btn-primary" style="background:rgba(59,130,246,.15);border:1px solid rgba(59,130,246,.3);color:#3b82f6;box-shadow:none;">
            <i class="fas fa-upload me-1"></i>Enviar Foto
            <input type="file" name="foto_facial" accept="image/*" capture="user" style="display:none;" onchange="previewFoto(this)">
          </label>
        </div>

        <div id="previewBox" style="display:none;margin-top:0.75rem;text-align:center;">
          <img id="previewImg" style="max-width:100%;border-radius:12px;border:2px solid #10b981;" alt="Preview">
          <div class="alert alert-success" style="margin-top:0.5rem;"><i class="fas fa-check-circle"></i>Foto capturada!</div>
        </div>

        <button type="button" class="btn btn-primary" id="nextStep" style="margin-top:0.75rem;display:none;" onclick="irParaPasso2()">
          Próximo <i class="fas fa-arrow-right ms-1"></i>
        </button>
      </div>

      {{-- Passo 2: PIN --}}
      <div class="card" id="step2" style="display:none;">
        <div class="step" style="margin-bottom:1rem;">
          <div class="step-num active">2</div>
          <span class="step-label"><i class="fas fa-key me-1" style="color:#10b981;"></i>Criar PIN de Acesso</span>
        </div>
        <p style="font-size:0.8rem;color:var(--muted);margin-bottom:1rem;">Crie um PIN de 4 a 8 dígitos. Você usará este PIN sempre que acessar o portal.</p>

        <label class="label">Novo PIN *</label>
        <input type="password" name="pin" id="pin1" required placeholder="Mínimo 4 dígitos" minlength="4" maxlength="8" inputmode="numeric">

        <label class="label">Confirmar PIN *</label>
        <input type="password" name="pin_confirmation" id="pin2" required placeholder="Repetir PIN" maxlength="8" inputmode="numeric">

        <div id="pinError" class="alert alert-danger" style="display:none;"><i class="fas fa-exclamation-circle"></i>Os PINs não coincidem.</div>

        <div class="divider"></div>
        <button type="submit" class="btn btn-success" id="submitBtn">
          <i class="fas fa-shield-alt me-2"></i>Ativar Minha Conta
        </button>
        <button type="button" class="btn" style="background:transparent;border:1px solid var(--border);color:var(--muted);" onclick="voltarPasso1()">
          <i class="fas fa-arrow-left me-1"></i>Voltar
        </button>
      </div>

    </form>
  @endif

  <p style="text-align:center;color:var(--muted);font-size:0.72rem;margin-top:1rem;">
    <i class="fas fa-lock me-1"></i>Conexão segura — MaxCheckout
  </p>
</div>

<script>
  let captured = false;

  async function initCam() {
    const st = document.getElementById('cam-status');
    try {
      const stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
      document.getElementById('camVideo').srcObject = stream;
      st.textContent = 'Posicione seu rosto no guia oval';
    } catch(e) {
      st.textContent = 'Câmera indisponível. Use "Enviar Foto".';
      st.className = 'err';
    }
  }

  initCam();

  document.getElementById('captureBtn').addEventListener('click', function() {
    const video = document.getElementById('camVideo');
    const canvas = document.getElementById('canvas');
    const ctx = canvas.getContext('2d');
    canvas.width  = video.videoWidth  || 640;
    canvas.height = video.videoHeight || 480;
    ctx.drawImage(video, 0, 0);
    const dataUrl = canvas.toDataURL('image/jpeg', 0.9);

    document.getElementById('previewImg').src   = dataUrl;
    document.getElementById('previewBox').style.display = 'block';
    document.getElementById('nextStep').style.display   = 'block';
    document.getElementById('cam-status').textContent   = '✓ Rosto capturado!';
    document.getElementById('cam-status').className     = 'ok';
    // Simula vetor facial para demo — em produção usar face-api.js real
    document.getElementById('facialVector').value = JSON.stringify(Array.from({length:128}, () => +(Math.random()*2-1).toFixed(6)));
    captured = true;
  });

  function previewFoto(input) {
    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = e => {
        document.getElementById('previewImg').src   = e.target.result;
        document.getElementById('previewBox').style.display = 'block';
        document.getElementById('nextStep').style.display   = 'block';
        document.getElementById('cam-status').textContent   = '✓ Foto selecionada!';
        document.getElementById('cam-status').className     = 'ok';
        captured = true;
      };
      reader.readAsDataURL(input.files[0]);
    }
  }

  function irParaPasso2() {
    document.getElementById('step1').style.display = 'none';
    document.getElementById('step2').style.display = 'block';
  }

  function voltarPasso1() {
    document.getElementById('step1').style.display = 'block';
    document.getElementById('step2').style.display = 'none';
  }

  document.getElementById('formAtivacao')?.addEventListener('submit', function(e) {
    const p1 = document.getElementById('pin1').value;
    const p2 = document.getElementById('pin2').value;
    if (p1 !== p2) {
      e.preventDefault();
      document.getElementById('pinError').style.display = 'flex';
      return;
    }
    document.getElementById('pinError').style.display = 'none';
  });
</script>
</body>
</html>
