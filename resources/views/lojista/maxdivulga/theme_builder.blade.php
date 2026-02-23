<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Builder Visual — {{ $theme->name }} | MaxPublica</title>

{{-- VvvebJs --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/givanz/VvvebJs@master/css/vvveb.css">

{{-- Bootstrap Icons --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<style>
  /* ── Reset & Body ── */
  * { box-sizing: border-box; margin: 0; padding: 0; }
  html, body { height: 100%; font-family: 'Segoe UI', sans-serif; background: #111827; }
  body { display: flex; flex-direction: column; overflow: hidden; }

  /* ── Top Toolbar ── */
  #maxpub-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    height: 52px; padding: 0 18px;
    background: linear-gradient(to right, #1f2937, #111827);
    border-bottom: 1px solid #374151;
    flex-shrink: 0; z-index: 1000;
  }
  #maxpub-toolbar .brand {
    display: flex; align-items: center; gap: 10px; color: #fff;
  }
  #maxpub-toolbar .brand i { color: #f59e0b; font-size: 1.2rem; }
  #maxpub-toolbar .brand strong { font-size: .95rem; }
  #maxpub-toolbar .brand small { color: #9ca3af; font-size: .78rem; }
  #maxpub-toolbar .actions { display: flex; gap: 8px; }
  .tb-btn {
    display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px;
    border-radius: 6px; font-size: .82rem; font-weight: 600; cursor: pointer;
    border: 1px solid transparent; transition: all .15s;
  }
  .tb-btn-save   { background: #10b981; color: #fff; border-color: #059669; }
  .tb-btn-save:hover { background: #059669; }
  .tb-btn-code   { background: transparent; color: #f59e0b; border-color: #f59e0b; }
  .tb-btn-code:hover { background: rgba(245,158,11,.1); }
  .tb-btn-studio { background: transparent; color: #9ca3af; border-color: #4b5563; }
  .tb-btn-studio:hover { background: rgba(75,85,99,.3); }

  /* ── Badge ── */
  .admin-badge {
    background: #dc2626; color: #fff; font-size: .65rem; font-weight: 700;
    padding: 2px 7px; border-radius: 4px; text-transform: uppercase; letter-spacing: .5px;
  }

  /* ── Main Layout ── */
  #vvveb-builder {
    display: flex; flex: 1; overflow: hidden;
  }

  /* ── Left Panel ── */
  #left-panel {
    width: 240px; background: #1f2937; border-right: 1px solid #374151;
    display: flex; flex-direction: column; flex-shrink: 0; overflow: hidden;
  }
  #left-panel .panel-header {
    padding: 10px 14px; background: #111827; border-bottom: 1px solid #374151;
    color: #9ca3af; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
  }
  .component-search {
    padding: 8px 10px; background: #111827; border-bottom: 1px solid #374151;
  }
  .component-search input {
    width: 100%; background: #374151; border: none; border-radius: 6px;
    padding: 5px 10px; color: #fff; font-size: .82rem; outline: none;
  }
  .component-search input::placeholder { color: #6b7280; }

  /* ── Component panels (VvvebJs hooks) ── */
  #components-list { flex: 1; overflow-y: auto; }

  /* ── Center: Iframe ── */
  #iframe-panel {
    flex: 1; display: flex; flex-direction: column; overflow: hidden; background: #0f172a;
  }
  #iframe-toolbar {
    display: flex; align-items: center; gap: 10px; padding: 8px 14px;
    background: #1e293b; border-bottom: 1px solid #334155; flex-shrink: 0;
  }
  #iframe-toolbar .device-btns button {
    background: #334155; border: none; color: #94a3b8; padding: 4px 10px;
    border-radius: 4px; cursor: pointer; font-size: .78rem;
    transition: all .15s;
  }
  #iframe-toolbar .device-btns button.active,
  #iframe-toolbar .device-btns button:hover { background: #3b82f6; color: #fff; }
  #iframe-toolbar .preview-label {
    font-size: .75rem; color: #64748b; margin-left: auto;
  }
  #iframe-container {
    flex: 1; overflow: auto;
    display: flex; align-items: flex-start; justify-content: center;
    padding: 20px; background: #0f172a;
    background-image: radial-gradient(circle, #1e293b 1px, transparent 1px);
    background-size: 24px 24px;
  }
  #iframe-wrapper {
    transform-origin: top center;
    transition: transform .3s, width .3s;
    box-shadow: 0 25px 80px rgba(0,0,0,.7);
    border-radius: 8px; overflow: hidden;
  }
  #iframe {
    display: block; border: none;
    width: 1080px; height: 1920px;
    background: #fff;
  }

  /* ── Right Panel ── */
  #right-panel {
    width: 260px; background: #1f2937; border-left: 1px solid #374151;
    display: flex; flex-direction: column; flex-shrink: 0; overflow: hidden;
  }
  #right-panel .panel-header {
    padding: 10px 14px; background: #111827; border-bottom: 1px solid #374151;
    color: #9ca3af; font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;
  }
  #properties-panel { flex: 1; overflow-y: auto; padding: 10px; }

  /* ── Alert ── */
  #top-alert {
    display: none; position: fixed; top: 60px; left: 50%; transform: translateX(-50%);
    z-index: 9999; min-width: 340px; padding: 12px 20px;
    border-radius: 8px; font-size: .88rem; font-weight: 600;
    box-shadow: 0 8px 24px rgba(0,0,0,.4);
  }
  #top-alert.success { background: #10b981; color: #fff; }
  #top-alert.error   { background: #ef4444; color: #fff; }

  /* ── Spinner ── */
  .frame-loading { position: absolute; inset: 0; display: flex; align-items: center; justify-content: center; flex-direction: column; gap: 12px; color: #94a3b8; pointer-events: none; }
  .frame-loading .spinner { width: 36px; height: 36px; border: 3px solid #334155; border-top-color: #3b82f6; border-radius: 50%; animation: spin 1s linear infinite; }
  @keyframes spin { to { transform: rotate(360deg); } }

  /* ── VvvebJs overrides ── */
  .vvveb-component { cursor: move; }
  #vvveb-builder .component-item { padding: 8px 12px !important; color: #d1d5db !important; background: #1f2937 !important; border: 1px solid #374151 !important; border-radius: 6px !important; margin: 4px 8px !important; font-size: .82rem !important; }
  #vvveb-builder .component-item:hover { background: #374151 !important; border-color: #6d28d9 !important; }
  #vvveb-builder .component-header { background: #374151 !important; color: #9ca3af !important; padding: 6px 14px !important; font-size: .7rem !important; font-weight: 700 !important; text-transform: uppercase !important; }
</style>
</head>

<body id="vvveb-builder">

{{-- Alert Floater --}}
<div id="top-alert"></div>

{{-- ── Toolbar ── --}}
<div id="maxpub-toolbar">
  <div class="brand">
    <i class="bi bi-magic"></i>
    <div>
      <strong>Builder Visual</strong>
      <small class="ms-2">{{ $theme->name }}</small>
    </div>
    <span class="admin-badge">Admin Only</span>
  </div>
  <div class="actions">
    <a href="{{ route('lojista.maxdivulga.theme_editor', $theme) }}" class="tb-btn tb-btn-code" target="_blank">
      <i class="bi bi-code-slash"></i> Código
    </a>
    <a href="{{ route('lojista.maxdivulga.themes') }}" class="tb-btn tb-btn-studio">
      <i class="bi bi-palette"></i> Studio
    </a>
    <button class="tb-btn tb-btn-save" onclick="saveTheme()" id="btnSave">
      <i class="bi bi-save"></i> Salvar Tema
    </button>
  </div>
</div>

{{-- ── Main Builder ── --}}
<div style="display:flex; flex:1; overflow:hidden;">

  {{-- Left: Components --}}
  <div id="left-panel">
    <div class="panel-header"><i class="bi bi-grid-3x3-gap-fill me-1"></i> Componentes</div>
    <div class="component-search">
      <input type="text" placeholder="🔍 Filtrar componentes..." id="compSearch" oninput="filterComponents(this.value)">
    </div>
    <div id="components-list" style="padding:8px 4px; overflow-y:auto; flex:1;"></div>
  </div>

  {{-- Center: Canvas --}}
  <div id="iframe-panel">
    <div id="iframe-toolbar">
      <div class="device-btns">
        <button class="active" onclick="setZoom(0.35, this)" title="Folheto real (1080px)">📄 Folheto</button>
        <button onclick="setZoom(0.6, this)" title="Zoom 60%">60%</button>
        <button onclick="setZoom(0.8, this)" title="Zoom 80%">80%</button>
        <button onclick="setZoom(1, this)" title="Tamanho real">100%</button>
      </div>
      <span class="preview-label" id="frameStatus">Carregando template...</span>
    </div>
    <div id="iframe-container">
      <div id="iframe-wrapper" style="transform: scale(0.35);">
        <iframe id="iframe"
          src="{{ $frameUrl }}"
          onload="onFrameLoad()"
          allow="same-origin">
        </iframe>
      </div>
    </div>
  </div>

  {{-- Right: Properties --}}
  <div id="right-panel">
    <div class="panel-header"><i class="bi bi-sliders me-1"></i> Propriedades</div>
    <div id="properties-panel">
      <p class="text-center" style="color:#6b7280; font-size:.8rem; margin-top:30px;">
        Clique em um elemento no canvas para editar suas propriedades aqui.
      </p>
    </div>
  </div>

</div>

{{-- VvvebJs --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
  @php
    $cfg = [
      'frameUrl' => $frameUrl,
      'saveUrl'  => route('lojista.maxdivulga.theme_builder_save', $theme),
      'csrfToken'=> csrf_token(),
      'themeName'=> $theme->name,
    ];
  @endphp
  window._CFG = {!! json_encode($cfg) !!};
</script>
<script src="https://cdn.jsdelivr.net/gh/givanz/VvvebJs@master/js/vvveb.js"></script>
<script>
// ── Configuração e inicialização ────────────────────────────
$(function(){

  // Inicializa VvvebJs com o iframe do template
  if (typeof Vvveb !== 'undefined') {
    Vvveb.Builder.init(window._CFG.frameUrl, function(){
      document.getElementById('frameStatus').textContent = '✓ Template carregado — clique para editar';
      document.querySelector('.device-btns button').classList.add('active');
    });

    // Adiciona componentes customizados do MaxPublica
    addMaxPublicaComponents();
  } else {
    // Fallback: VvvebJs não carregou (CDN indisponível), usa editor simplificado
    initFallbackEditor();
  }
});

// ── Componentes do MaxPublica ────────────────────────────────
function addMaxPublicaComponents() {
  const components = [
    {
      id: 'maxpub-header',
      icon: '📋',
      label: 'Header (Logo + Data)',
      category: '🏗️ Estrutura',
      html: '<div style="background:linear-gradient(135deg,#003A7A,#0057B8);color:#FFD700;display:flex;align-items:center;padding:20px 40px;border-bottom:6px solid #FFD700;min-height:240px;"><div style="font-size:4rem;font-weight:900;color:#fff;text-shadow:3px 3px 0 #003A7A;">OFERTA</div></div>',
    },
    {
      id: 'maxpub-copy',
      icon: '✏️',
      label: 'Área Copy (IA)',
      category: '🏗️ Estrutura',
      html: '<div style="background:linear-gradient(to bottom,#FFD700,#ffca00);color:#003A7A;padding:18px 36px;text-align:center;"><div style="font-size:2.2rem;font-weight:900;">🤖 Headline da IA</div><div style="font-size:1.2rem;font-weight:800;margin-top:5px;">🤖 Subtítulo da IA</div></div>',
    },
    {
      id: 'maxpub-produto-card',
      icon: '🃏',
      label: 'Card Produto',
      category: '📦 Componentes',
      html: '<div style="background:#fff;border:2px solid #0057B8;border-radius:12px;overflow:hidden;position:relative;display:flex;flex-direction:column;align-items:center;padding:12px;"><div style="position:absolute;top:0;right:0;background:linear-gradient(135deg,#0057B8,#003A7A);color:#fff;font-size:.7rem;font-weight:900;padding:4px 10px;border-radius:0 11px 0 8px;">OFERTA</div><div style="font-size:.9rem;font-weight:800;text-align:center;text-transform:uppercase;margin:8px 0 4px;">Nome do Produto</div><div style="font-size:.8rem;color:#888;text-decoration:line-through;margin-bottom:3px;">de R$ 0,00</div><div style="background:linear-gradient(to bottom,#0057B8,#003A7A);color:#fff;font-weight:900;font-size:1.5rem;text-align:center;padding:4px 8px;border-radius:8px;width:100%;">R$ 0,00</div></div>',
    },
    {
      id: 'maxpub-ai-titulo',
      icon: '🤖',
      label: 'Título IA',
      category: '📦 Componentes',
      html: '<div style="min-height:60px;border:2px dashed #7c3aed;background:rgba(124,58,237,.06);display:flex;align-items:center;justify-content:center;padding:10px;color:#7c3aed;font-weight:bold;font-family:sans-serif;font-size:1rem;">🤖 TÍTULO — Gerado pela IA</div>',
    },
    {
      id: 'maxpub-ai-sub',
      icon: '🤖',
      label: 'Subtítulo IA',
      category: '📦 Componentes',
      html: '<div style="min-height:40px;border:2px dashed #7c3aed;background:rgba(124,58,237,.06);display:flex;align-items:center;justify-content:center;padding:8px;color:#7c3aed;font-family:sans-serif;font-size:.9rem;">🤖 SUBTÍTULO — Gerado pela IA</div>',
    },
    {
      id: 'maxpub-rodape',
      icon: '🏪',
      label: 'Rodapé',
      category: '🏗️ Estrutura',
      html: '<div style="background:linear-gradient(to right,#003A7A,#0057B8,#003A7A);color:#fff;padding:22px 40px;text-align:center;border-top:5px solid #FFD700;"><div style="font-size:1.8rem;font-weight:900;text-transform:uppercase;">Nome da Loja</div><div style="font-size:1.1rem;font-weight:700;margin-top:6px;">📍 Endereço | 📞 Telefone</div></div>',
    },
    {
      id: 'maxpub-icon',
      icon: '⭐',
      label: 'Ícone',
      category: '📦 Componentes',
      html: '<span style="font-size:2rem;color:#FFD700;">★</span>',
    },
    {
      id: 'maxpub-divisor',
      icon: '〰️',
      label: 'Divisor Dourado',
      category: '📦 Componentes',
      html: '<div style="height:4px;background:linear-gradient(to right,transparent,#FFD700,transparent);margin:16px 0;"></div>',
    },
  ];

  const list = document.getElementById('components-list');
  let currentCategory = '';

  components.forEach(comp => {
    if (comp.category !== currentCategory) {
      currentCategory = comp.category;
      const header = document.createElement('div');
      header.className = 'component-header';
      header.textContent = comp.category;
      header.style.cssText = 'padding:6px 14px;background:#111827;color:#9ca3af;font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:1px;border-bottom:1px solid #374151;margin-top:4px;';
      list.appendChild(header);
    }

    const item = document.createElement('div');
    item.className = 'component-item';
    item.dataset.compId = comp.id;
    item.dataset.html   = comp.html;
    item.innerHTML = `<span style="margin-right:6px;">${comp.icon}</span> ${comp.label}`;
    item.style.cssText = 'padding:8px 12px;border-bottom:1px solid #374151;cursor:grab;color:#d1d5db;font-size:.82rem;user-select:none;transition:background .1s;';
    item.addEventListener('mouseover', () => item.style.background = '#374151');
    item.addEventListener('mouseout',  () => item.style.background = '');

    // Drag para inserir no iframe
    item.setAttribute('draggable', true);
    item.addEventListener('dragstart', e => {
      e.dataTransfer.setData('text/html', comp.html);
      e.dataTransfer.setData('text/plain', comp.id);
    });

    // Click para inserir no body do iframe
    item.addEventListener('click', () => insertIntoFrame(comp.html));

    list.appendChild(item);
  });

  // Configura o iframe para aceitar drops
  setupIframeDrop();
}

function insertIntoFrame(html) {
  try {
    const iframeDoc = document.getElementById('iframe').contentDocument;
    if (iframeDoc && iframeDoc.body) {
      iframeDoc.body.insertAdjacentHTML('beforeend', html);
      flashStatus('✓ Componente inserido');
    }
  } catch(e) {
    flashStatus('⚠ Não foi possível inserir', true);
  }
}

function setupIframeDrop() {
  const iframeEl = document.getElementById('iframe');
  iframeEl.addEventListener('load', () => {
    try {
      const iframeDoc = iframeEl.contentDocument;
      if (!iframeDoc) return;
      iframeDoc.addEventListener('dragover', e => e.preventDefault());
      iframeDoc.addEventListener('drop', e => {
        e.preventDefault();
        const html = e.dataTransfer.getData('text/html');
        if (html) {
          const el = iframeDoc.createElement ? iframeDoc.createElement('div') : null;
          if (el) {
            el.innerHTML = html;
            const target = iframeDoc.elementFromPoint(e.clientX, e.clientY);
            if (target) target.appendChild(el.firstChild);
            else iframeDoc.body.insertAdjacentHTML('beforeend', html);
            flashStatus('✓ Componente inserido');
          }
        }
      });

      // Click em elementos → mostra propriedades básicas
      iframeDoc.addEventListener('click', e => {
        if (e.target && e.target !== iframeDoc.body) showProperties(e.target);
      });

    } catch(e) { /* cross-origin */ }
  });
}

// ── Propriedades simples ─────────────────────────────────────
function showProperties(el) {
  const panel = document.getElementById('properties-panel');
  const cs    = el.style;
  panel.innerHTML = `
    <div style="padding:4px 0;">
      <label style="font-size:.75rem;color:#9ca3af;display:block;margin-bottom:4px;">Tag: <strong style="color:#e5e7eb;">${el.tagName.toLowerCase()}</strong></label>
      <hr style="border-color:#374151;">
      <label style="font-size:.75rem;color:#9ca3af;">Cor de Fundo</label>
      <input type="color" value="${rgbToHex(el.style.backgroundColor) || '#ffffff'}"
        style="width:100%;height:32px;border:none;background:none;cursor:pointer;margin-bottom:8px;"
        oninput="el.style.backgroundColor=this.value" id="prop-bg">
      <label style="font-size:.75rem;color:#9ca3af;">Cor do Texto</label>
      <input type="color" value="${rgbToHex(el.style.color) || '#000000'}"
        style="width:100%;height:32px;border:none;background:none;cursor:pointer;margin-bottom:8px;"
        oninput="el.style.color=this.value">
      <label style="font-size:.75rem;color:#9ca3af;">Tamanho da Fonte (rem)</label>
      <input type="range" min="0.6" max="6" step="0.05"
        value="${parseFloat(el.style.fontSize) || 1}"
        style="width:100%;margin-bottom:4px;"
        oninput="el.style.fontSize=this.value+'rem'; this.nextElementSibling.textContent=parseFloat(this.value).toFixed(2)+'rem'">
      <small style="color:#6b7280;">1.00rem</small>
      <label style="font-size:.75rem;color:#9ca3af; margin-top:8px; display:block;">Padding (px)</label>
      <input type="range" min="0" max="80" step="2"
        value="${parseInt(el.style.padding) || 10}"
        style="width:100%;margin-bottom:4px;"
        oninput="el.style.padding=this.value+'px'; this.nextElementSibling.textContent=this.value+'px'">
      <small style="color:#6b7280;">10px</small>
      <label style="font-size:.75rem;color:#9ca3af; margin-top:8px; display:block;">Border Radius (px)</label>
      <input type="range" min="0" max="60" step="2"
        value="${parseInt(el.style.borderRadius) || 0}"
        style="width:100%;margin-bottom:4px;"
        oninput="el.style.borderRadius=this.value+'px'; this.nextElementSibling.textContent=this.value+'px'">
      <small style="color:#6b7280;">0px</small>
      <div style="margin-top:12px;">
        <button onclick="deleteElement()" style="background:#dc2626;color:#fff;border:none;padding:6px 12px;border-radius:4px;width:100%;cursor:pointer;font-size:.8rem;"><i>🗑</i> Remover Elemento</button>
      </div>
    </div>`;

  // Guarda referência ao elemento selecionado
  window._selectedEl = el;

  // Re-aplica listeners (oninput nos inputs criados via innerHTML afetam window._selectedEl)
  const inputs = panel.querySelectorAll('input[type=range], input[type=color]');
  inputs[0] && (inputs[0].oninput = (e) => { window._selectedEl.style.backgroundColor = e.target.value; });
  inputs[1] && (inputs[1].oninput = (e) => { window._selectedEl.style.color           = e.target.value; });
  inputs[2] && (inputs[2].oninput = (e) => { window._selectedEl.style.fontSize        = e.target.value + 'rem'; inputs[2].nextElementSibling.textContent = parseFloat(e.target.value).toFixed(2) + 'rem'; });
  inputs[3] && (inputs[3].oninput = (e) => { window._selectedEl.style.padding         = e.target.value + 'px'; inputs[3].nextElementSibling.textContent = e.target.value + 'px'; });
  inputs[4] && (inputs[4].oninput = (e) => { window._selectedEl.style.borderRadius    = e.target.value + 'px'; inputs[4].nextElementSibling.textContent = e.target.value + 'px'; });
}

function deleteElement() {
  if (window._selectedEl) {
    window._selectedEl.remove();
    document.getElementById('properties-panel').innerHTML = '<p class="text-center" style="color:#6b7280;font-size:.8rem;margin-top:30px;">Elemento removido.</p>';
    window._selectedEl = null;
  }
}

// ── Zoom / Device preview ────────────────────────────────────
function setZoom(scale, btn) {
  document.getElementById('iframe-wrapper').style.transform = `scale(${scale})`;
  document.querySelectorAll('.device-btns button').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
}

function onFrameLoad() {
  document.getElementById('frameStatus').textContent = '✓ Template carregado — clique para editar';
  setupIframeDrop();
}

// ── Filtro de componentes ────────────────────────────────────
function filterComponents(q) {
  document.querySelectorAll('#components-list .component-item').forEach(item => {
    item.style.display = q === '' || item.textContent.toLowerCase().includes(q.toLowerCase()) ? '' : 'none';
  });
}

// ── Salvar ───────────────────────────────────────────────────
async function saveTheme() {
  const btn = document.getElementById('btnSave');
  btn.disabled = true;
  btn.innerHTML = '<span class="spinner">↻</span> Salvando...';
  flashStatus('☁ Salvando...', false, true);

  // Captura CSS do iframe
  let css = '';
  let html = '';
  try {
    const iframeDoc = document.getElementById('iframe').contentDocument;
    if (iframeDoc) {
      html = iframeDoc.documentElement.outerHTML;
      // Extrai CSS inline adicionados via edições
      const inlineStyles = iframeDoc.querySelectorAll('[style]');
      let inlineCss = '';
      inlineStyles.forEach(el => {
        if (el.id) inlineCss += `#${el.id} { ${el.style.cssText} }\n`;
      });
      css = inlineCss;
    }
  } catch(e) {
    // Se cross-origin, usa VvvebJs
    if (typeof Vvveb !== 'undefined' && Vvveb.Builder) {
      try { html = Vvveb.Builder.getHtml(); } catch(e2) {}
    }
  }

  // Se VvvebJs está disponível, usa ele
  if (typeof Vvveb !== 'undefined' && Vvveb.Builder) {
    try {
      html = Vvveb.Builder.getHtml();
      css  = Vvveb.Builder.getCss ? Vvveb.Builder.getCss() : css;
    } catch(e) {}
  }

  try {
    const res  = await fetch(window._CFG.saveUrl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window._CFG.csrfToken, 'Accept': 'application/json' },
      body: JSON.stringify({ html, css })
    });
    const data = await res.json();
    flashStatus(data.success ? '✓ ' + data.message : '✗ ' + data.message, !data.success);
  } catch(e) {
    flashStatus('✗ Erro: ' + e.message, true);
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="bi bi-save"></i> Salvar Tema';
}

// ── Utilitários ──────────────────────────────────────────────
function flashStatus(msg, isError = false, neutral = false) {
  const el = document.getElementById('top-alert');
  el.textContent = msg;
  el.className = neutral ? '' : (isError ? 'error' : 'success');
  el.style.cssText = `display:block;position:fixed;top:60px;left:50%;transform:translateX(-50%);z-index:9999;min-width:340px;padding:12px 20px;border-radius:8px;font-size:.88rem;font-weight:600;box-shadow:0 8px 24px rgba(0,0,0,.4);${isError ? 'background:#ef4444;color:#fff' : neutral ? 'background:#1e293b;color:#94a3b8;border:1px solid #334155' : 'background:#10b981;color:#fff'}`;
  if (!neutral) setTimeout(() => el.style.display = 'none', 4000);
}

function flashStatus(msg, isError, neutral) {
  document.getElementById('frameStatus').textContent = msg;
  if (isError === true) document.getElementById('frameStatus').style.color = '#ef4444';
  else document.getElementById('frameStatus').style.color = '#94a3b8';
  const alert = document.getElementById('top-alert');
  if (msg && !neutral) {
    alert.textContent = msg;
    alert.style.background = isError ? '#ef4444' : '#10b981';
    alert.style.color = '#fff';
    alert.style.display = 'block';
    if (!isError) setTimeout(() => alert.style.display = 'none', 4000);
  }
}

function rgbToHex(rgb) {
  if (!rgb) return '';
  const m = rgb.match(/^rgb\((\d+),\s*(\d+),\s*(\d+)\)$/);
  if (!m) return rgb;
  return '#' + [m[1], m[2], m[3]].map(n => parseInt(n).toString(16).padStart(2, '0')).join('');
}

// ── Fallback se VvvebJs não carregar ────────────────────────
function initFallbackEditor() {
  document.getElementById('frameStatus').textContent = '⚠ VvvebJs via CDN — verifique a conexão com a internet';
  addMaxPublicaComponents(); // Componentes customizados ainda funcionam
}
</script>
</body>
</html>