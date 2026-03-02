@extends('layouts.user_type.auth')

@section('content')
<style>
/* ====== Layout Editor Styles ====== */
.editor-wrap { display:flex; gap:10px; height: calc(100vh - 110px); }
.panel { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); overflow:hidden; display:flex; flex-direction:column; }
.panel-header { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.05rem; padding:8px 12px; background:#f8f9fa; border-bottom:1px solid #e9ecef; color:#495057; flex-shrink:0; }

/* Left panel */
.tools-panel { width:215px; flex-shrink:0; overflow-y:auto; }
.tool-btn { display:flex; align-items:center; gap:7px; padding:7px 12px; font-size:.78rem; cursor:pointer; border:none; background:none; width:100%; text-align:left; transition:background .15s; color:#344767; }
.tool-btn:hover { background:#f0f2ff; color:#5e72e4; }
.tool-btn i { width:14px; text-align:center; color:#7c8db5; }
.section-divider { border-top:1px solid #e9ecef; }

/* Gradient palettes */
.grad-palettes { display:flex; flex-wrap:wrap; gap:5px; padding:8px 12px; }
.grad-swatch { width:36px; height:20px; border-radius:5px; cursor:pointer; border:2px solid transparent; transition:border-color .15s; }
.grad-swatch:hover, .grad-swatch.active { border-color:#5e72e4; }
.grad-custom { display:flex; gap:4px; align-items:center; padding:0 12px 8px; }
.grad-custom input[type=color] { width:36px; height:28px; border:1px solid #dee2e6; border-radius:5px; cursor:pointer; padding:1px; }

/* Product search */
#prod-search { width:100%; font-size:.75rem; padding:5px 8px; border:1px solid #dee2e6; border-radius:6px; margin-bottom:6px; outline:none; }
.prod-list { overflow-y:auto; max-height:220px; }
.prod-item { display:flex; align-items:center; gap:7px; padding:5px 8px; border-radius:8px; cursor:pointer; border:1px solid #f0f0f0; margin-bottom:4px; transition:background .12s; }
.prod-item:hover { background:#f0f2ff; border-color:#c7d2fe; }
.prod-img { width:34px; height:34px; object-fit:cover; border-radius:6px; flex-shrink:0; background:#f0f0f0; }
.prod-name { font-size:.73rem; font-weight:700; color:#344767; line-height:1.2; }
.prod-price { font-size:.7rem; color:#43e97b; font-weight:700; }

/* Canvas panel */
.canvas-panel { flex:1; overflow:hidden; }
.canvas-toolbar { display:flex; align-items:center; gap:6px; padding:6px 10px; border-bottom:1px solid #e9ecef; flex-wrap:wrap; flex-shrink:0; }
.canvas-wrap { flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; background:#e9ecef; padding:16px; }
#main-canvas { box-shadow:0 4px 24px rgba(0,0,0,.25); }

/* Right panel */
.props-panel { width:205px; flex-shrink:0; overflow-y:auto; }
.prop-group { padding:8px 12px; border-bottom:1px solid #f0f0f0; }
.prop-group label { font-size:.7rem; font-weight:600; color:#7c8db5; display:block; margin-bottom:3px; }
.prop-group input, .prop-group select { width:100%; font-size:.78rem; padding:4px 7px; border:1px solid #dee2e6; border-radius:6px; color:#344767; }

/* Buttons */
.undo-redo-btn { background:#f0f2ff; border:none; border-radius:6px; padding:4px 9px; font-size:.78rem; cursor:pointer; color:#5e72e4; }
.undo-redo-btn:hover { background:#5e72e4; color:#fff; }
.save-btn { background:linear-gradient(135deg,#43e97b,#38f9d7); border:none; border-radius:7px; padding:6px 16px; font-size:.8rem; font-weight:700; color:#fff; cursor:pointer; }
.res-select { font-size:.78rem; padding:4px 8px; border:1px solid #dee2e6; border-radius:6px; }
</style>

<div class="container-fluid py-2">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0"><i class="fas fa-edit me-2 text-primary"></i>Editor de Layout TvDoor</h6>
    <div class="d-flex gap-2">
      <a href="{{ route('lojista.tvdoor.layouts.index') }}" class="btn btn-sm btn-outline-secondary">← Voltar</a>
      <button class="save-btn" style="background:linear-gradient(135deg,#4facfe,#00f2fe)" onclick="salvarSemSair()" id="btn-save-only">
        <i class="fas fa-save me-1"></i> Salvar
      </button>
      <button class="save-btn" onclick="salvarLayout()" id="btn-save-exit">
        <i class="fas fa-check-circle me-1"></i> Salvar e Voltar
      </button>
      <button class="save-btn" style="background:linear-gradient(135deg,#f7971e,#ffd200);color:#333" onclick="previewLayout()">
        <i class="fas fa-eye me-1"></i> Prévia
      </button>
    </div>
  </div>

  <div class="editor-wrap">
    <!-- ===== PAINEL ESQUERDO ===== -->
    <div class="panel tools-panel">
      <!-- Nome e Duração -->
      <div class="prop-group" style="padding:12px;background:#f8f9fa;border-bottom:1px solid #e9ecef;">
        <label>Nome do Layout</label>
        <input type="text" id="layout-name" value="{{ $layout->name ?? 'Novo Layout' }}">
        <label class="mt-2">Duração (segundos)</label>
        <input type="number" id="layout-duration" value="{{ $layout->duration ?? 15 }}" min="5">
      </div>

      <!-- Elementos -->
      <div class="panel-header">Elementos</div>
      <button class="tool-btn" onclick="addText()"><i class="fas fa-font"></i> Texto</button>
      <button class="tool-btn" onclick="addClock()"><i class="fas fa-clock"></i> Relógio</button>
      <button class="tool-btn" onclick="addRect()"><i class="fas fa-square"></i> Retângulo</button>
      <button class="tool-btn" onclick="addCircle()"><i class="far fa-circle"></i> Círculo</button>
      <button class="tool-btn" onclick="addLine()"><i class="fas fa-minus"></i> Linha</button>
      <button class="tool-btn" onclick="triggerFile('img-upload')"><i class="fas fa-image"></i> Imagem</button>
      <button class="tool-btn" onclick="triggerFile('gif-upload')"><i class="fas fa-film"></i> GIF Animado</button>
      <button class="tool-btn" onclick="triggerFile('video-upload')"><i class="fas fa-video"></i> Vídeo (embed)</button>
      <input type="file" id="img-upload"   accept="image/*"       style="display:none" onchange="addImage(this)">
      <input type="file" id="gif-upload"   accept="image/gif"     style="display:none" onchange="addGif(this)">
      <input type="file" id="video-upload" accept="video/*"        style="display:none" onchange="addVideo(this)">

      <!-- Fundo do Canvas -->
      <div class="section-divider">
        <div class="panel-header">Fundo do Canvas</div>
        <div style="padding:8px 12px 4px;">
          <label style="font-size:.7rem;font-weight:600;color:#7c8db5;display:block;margin-bottom:4px;">Tipo de Fundo</label>
          <select id="bg-type" class="res-select w-100 mb-2" onchange="updateBgType()">
            <option value="solid">Cor Sólida</option>
            <option value="gradient">Gradiente</option>
            <option value="image">Imagem</option>
          </select>

          <!-- Sólida -->
          <div id="bg-solid">
            <input type="color" id="bg-color" value="#1a1a2e" class="w-100" style="height:34px;border-radius:6px;border:1px solid #dee2e6;cursor:pointer;" onchange="applyBgSolid()">
          </div>

          <!-- Gradiente -->
          <div id="bg-gradient" style="display:none">
            <div style="font-size:.7rem;font-weight:600;color:#7c8db5;padding:4px 0 4px;">Paletas Predefinidas</div>
            <div class="grad-palettes" id="grad-palettes"></div>
            <div style="font-size:.7rem;font-weight:600;color:#7c8db5;padding:4px 0 3px;">Personalizado</div>
            <div class="grad-custom">
              <input type="color" id="grad-c1" value="#0f0c29" onchange="applyBgGradient()">
              <span style="font-size:.7rem;color:#aaa">→</span>
              <input type="color" id="grad-c2" value="#302b63" onchange="applyBgGradient()">
              <input type="color" id="grad-c3" value="#24243e" onchange="applyBgGradient()" title="Cor intermediária (opcional)">
            </div>
            <div style="padding:0 12px 8px;">
              <select id="grad-dir" class="res-select w-100 mb-1" onchange="applyBgGradient()">
                <option value="h">Horizontal →</option>
                <option value="v">Vertical ↓</option>
                <option value="d">Diagonal ↘</option>
                <option value="r">Diagonal ↗</option>
              </select>
            </div>
          </div>

          <!-- Imagem de Fundo -->
          <div id="bg-image-sec" style="display:none">
            <button class="tool-btn w-100 mt-1" onclick="triggerFile('bg-img-input')" style="border:1px dashed #dee2e6;border-radius:6px;justify-content:center;">
              <i class="fas fa-upload me-1"></i> Carregar Fundo
            </button>
            <input type="file" id="bg-img-input" accept="image/*" style="display:none" onchange="applyBgImage(this)">
          </div>
        </div>
      </div>

      <!-- Produtos -->
      <div class="section-divider">
        <div class="panel-header">Produtos do Catálogo</div>
        <div style="padding:8px 12px 4px;">
          <input type="text" id="prod-search" placeholder="🔍 Buscar por nome ou código..."
                 oninput="debounceSearch()" style="margin-bottom:6px;">
          <div class="prod-list" id="prod-list">
            <div id="prod-loading" style="text-align:center;padding:12px;font-size:.75rem;color:#999;">
              <i class="fas fa-spinner fa-spin"></i> Carregando...
            </div>
          </div>
          <div id="prod-pagination" style="display:flex;justify-content:space-between;align-items:center;padding:4px 0;font-size:.7rem;color:#888;">
            <span id="prod-info"></span>
            <button id="prod-more" onclick="loadMoreProdutos()" class="undo-redo-btn" style="display:none;font-size:.7rem;padding:3px 8px;">
              <i class="fas fa-chevron-down me-1"></i>Ver mais
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- ===== CANVAS CENTRAL ===== -->
    <div class="panel canvas-panel">
      <div class="canvas-toolbar">
        <select class="res-select" id="res-select" onchange="changeResolution()">
          <option value="1920x1080">Full HD (1920×1080)</option>
          <option value="1280x720">HD (1280×720)</option>
          <option value="1080x1920">Vertical 9:16</option>
          <option value="1080x1080">Quadrado</option>
          <option value="3840x2160">4K</option>
          <option value="custom">Personalizado...</option>
        </select>
        <div id="custom-res" style="display:none;gap:4px;align-items:center;" class="d-flex">
          <input type="number" id="cw" value="1920" class="res-select" style="width:65px;" placeholder="W">
          <span>×</span>
          <input type="number" id="ch" value="1080" class="res-select" style="width:65px;" placeholder="H">
          <button class="undo-redo-btn" onclick="applyCustomRes()">OK</button>
        </div>
        <div class="zoom-controls d-flex align-items-center gap-1 mx-2">
          <button class="undo-redo-btn" onclick="zoomOut()" title="Diminuir Zoom"><i class="fas fa-search-minus"></i></button>
          <span id="zoom-label" style="font-size: .72rem; color: #888; min-width: 35px; text-align: center;">100%</span>
          <button class="undo-redo-btn" onclick="zoomIn()" title="Aumentar Zoom"><i class="fas fa-search-plus"></i></button>
          <button class="undo-redo-btn" onclick="resetZoom()" title="Ajustar à tela"><i class="fas fa-expand"></i></button>
        </div>
        <div style="flex:1"></div>
        <button class="undo-redo-btn" title="Desfazer" onclick="undoAction()"><i class="fas fa-undo"></i></button>
        <button class="undo-redo-btn" title="Duplicar" onclick="duplicateSelected()"><i class="fas fa-copy"></i></button>
        <div style="border-left:1px solid #ddd; height:20px; margin:0 5px;"></div>
        <button class="undo-redo-btn" title="Agrupar" onclick="groupObjects()"><i class="fas fa-object-group"></i></button>
        <button class="undo-redo-btn" title="Desagrupar" onclick="ungroupObjects()"><i class="fas fa-object-ungroup"></i></button>
        <div style="border-left:1px solid #ddd; height:20px; margin:0 5px;"></div>
        <button class="undo-redo-btn" title="Trazer para frente" onclick="bringForward()"><i class="fas fa-layer-group"></i></button>
        <button class="undo-redo-btn" title="Enviar para trás" onclick="sendBackward()"><i class="fas fa-arrow-down"></i></button>
        <button class="undo-redo-btn text-danger" title="Excluir selecionado" onclick="deleteSelected()"><i class="fas fa-trash"></i></button>
      </div>
      <div class="canvas-wrap">
        <canvas id="main-canvas"></canvas>
      </div>
    </div>

    <!-- ===== PAINEL DIREITO: PROPRIEDADES ===== -->
    <div class="panel props-panel">
      <div class="panel-header">Propriedades</div>
      <div id="no-selection" style="padding:20px;font-size:.8rem;color:#999;text-align:center;">
        <i class="fas fa-mouse-pointer d-block mb-2" style="font-size:1.5rem;opacity:.3;"></i>
        Selecione um elemento para editar.
      </div>
      <div id="obj-props" style="display:none;">
        <div class="prop-group">
          <label>Texto</label>
          <input type="text" id="prop-text" oninput="setProp('text', this.value)">
        </div>
        <div class="prop-group">
          <label>Cor do Texto</label>
          <input type="color" id="prop-fill" onchange="setProp('fill', this.value)" style="height:30px;cursor:pointer;">
        </div>
        <div class="prop-group">
          <label>Tamanho da Fonte</label>
          <input type="number" id="prop-fontsize" oninput="setPropNum('fontSize', this.value)" min="8" max="500">
        </div>
        <div class="prop-group">
          <label>Família da Fonte</label>
          <select id="prop-fontfamily" onchange="setProp('fontFamily', this.value)">
            <option>Segoe UI, sans-serif</option>
            <option>Arial, sans-serif</option>
            <option>Georgia, serif</option>
            <option>Impact, sans-serif</option>
            <option>Courier New, monospace</option>
            <option>Verdana, sans-serif</option>
          </select>
        </div>
        <div class="prop-group">
          <label>Estilo</label>
          <div class="d-flex gap-1">
            <button class="undo-redo-btn" onclick="toggleBold()"><i class="fas fa-bold"></i></button>
            <button class="undo-redo-btn" onclick="toggleItalic()"><i class="fas fa-italic"></i></button>
            <button class="undo-redo-btn" onclick="toggleUnderline()"><i class="fas fa-underline"></i></button>
          </div>
        </div>
        <div class="prop-group">
          <label>Altura da Linha (<span id="lineheight-val">1.16</span>)</label>
          <input type="range" id="prop-lineheight" min="0.5" max="3" step="0.05" oninput="setPropNum('lineHeight', this.value); document.getElementById('lineheight-val').innerText = this.value">
        </div>
        <div class="prop-group">
          <label>Espaçamento Letras (<span id="charspacing-val">0</span>)</label>
          <input type="range" id="prop-charspacing" min="-100" max="1000" step="1" oninput="setPropNum('charSpacing', this.value); document.getElementById('charspacing-val').innerText = this.value">
        </div>
        <div class="prop-group">
          <label>Cor de Fundo (objeto)</label>
          <input type="color" id="prop-bg" onchange="setProp('backgroundColor', this.value)" style="height:30px;cursor:pointer;">
        </div>
        <div class="prop-group">
          <label>Opacidade (<span id="opacity-val">100</span>%)</label>
          <input type="range" id="prop-opacity" min="0" max="1" step="0.01" value="1" oninput="setOpacity(this.value)">
        </div>
        <div class="prop-group">
          <label>Alinhamento do Texto</label>
          <div class="d-flex gap-1">
            <button class="undo-redo-btn" onclick="setProp('textAlign','left')"><i class="fas fa-align-left"></i></button>
            <button class="undo-redo-btn" onclick="setProp('textAlign','center')"><i class="fas fa-align-center"></i></button>
            <button class="undo-redo-btn" onclick="setProp('textAlign','right')"><i class="fas fa-align-right"></i></button>
          </div>
        </div>
        <div class="prop-group">
          <label>Posição X</label>
          <input type="number" id="prop-x" oninput="setPropNum('left', this.value)">
        </div>
        <div class="prop-group">
          <label>Posição Y</label>
          <input type="number" id="prop-y" oninput="setPropNum('top', this.value)">
        </div>
        <div class="prop-group">
          <label>Largura</label>
          <input type="number" id="prop-w" oninput="setPropNum('width', this.value)">
        </div>
        <div class="prop-group">
          <label>Altura</label>
          <input type="number" id="prop-h" oninput="setPropNum('height', this.value)">
        </div>
        <div class="prop-group">
          <label>Ângulo (rotação)</label>
          <input type="number" id="prop-angle" min="-360" max="360" oninput="setPropNum('angle', this.value)">
        </div>
      </div>

      <!-- Salvar (Oculto ou consolidado) -->
      <div class="panel-header" style="margin-top:auto;">Ações</div>
      <div class="prop-group">
        <label>Resolução Alvo</label>
        <div id="res-label" style="font-size:.8rem;font-weight:700;color:#5e72e4;">{{ $layout->resolution ?? '1920x1080' }}</div>
        <input type="hidden" id="layout-resolution" value="{{ $layout->resolution ?? '1920x1080' }}">
      </div>
      <div class="prop-group">
        <button class="save-btn w-100" onclick="salvarLayout()"><i class="fas fa-save me-1"></i> Salvar</button>
      </div>
    </div>
  </div>
</div>

<!-- Form oculto para salvar layout -->
  @isset($layout)
  <form id="save-form" action="{{ route('lojista.tvdoor.layouts.update', $layout->id) }}" method="POST" style="display:none">
    @csrf @method('PUT')
    <input type="hidden" name="name" id="save-name">
    <input type="hidden" name="duration" id="save-duration">
    <input type="hidden" name="content" id="save-content">
    <input type="hidden" name="resolution" id="save-resolution" value="{{ $layout->resolution ?? '1920x1080' }}">
  </form>
  @else
  <form id="save-form" action="{{ route('lojista.tvdoor.layouts.store') }}" method="POST" style="display:none">
    @csrf
    <input type="hidden" name="name" id="save-name">
    <input type="hidden" name="duration" id="save-duration">
    <input type="hidden" name="content" id="save-content">
    <input type="hidden" name="resolution" id="save-resolution" value="1920x1080">
  </form>
  @endisset
<!-- Fabric.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
let canvasW = 1920, canvasH = 1080;
let history = [];

// ===== CANVAS =====
// Inicializa no tamanho real, o CSS vai escalar o container
const canvas = new fabric.Canvas('main-canvas', {
    width:  canvasW,
    height: canvasH,
    preserveObjectStacking: true,
});

let currentZoom = 1.0;
let autoScale = 1.0;

// Escala o canvas visualmente via CSS para caber na tela
function updateCanvasScaling() {
    const wrap = document.querySelector('.canvas-wrap');
    if (!wrap) return;
    const cw = wrap.clientWidth - 40;
    const ch = wrap.clientHeight - 40;
    
    if (cw <= 0 || ch <= 0) {
        setTimeout(updateCanvasScaling, 200);
        return;
    }

    autoScale = Math.min(cw / canvasW, ch / canvasH, 1.0);
    applyTransform();
}

function applyTransform() {
    const el = canvas.wrapperEl;
    const finalFactor = autoScale * currentZoom;
    el.style.transform = `scale(${finalFactor})`;
    el.style.transformOrigin = 'center center';
    document.getElementById('zoom-label').innerText = Math.round(finalFactor * 100) + '%';
}

function zoomIn() {
    currentZoom += 0.1;
    applyTransform();
}

function zoomOut() {
    currentZoom = Math.max(0.1, currentZoom - 0.1);
    applyTransform();
}

function resetZoom() {
    currentZoom = 1.0;
    updateCanvasScaling();
}

window.addEventListener('resize', updateCanvasScaling);
setTimeout(updateCanvasScaling, 500);

// ===== RESOLUÇÃO =====
function changeResolution() {
    const val = document.getElementById('res-select').value;
    document.getElementById('custom-res').style.display = val === 'custom' ? 'flex' : 'none';
    if (val === 'custom') return;
    const [w, h] = val.split('x').map(Number);
    setResolution(w, h);
}
function applyCustomRes() {
    setResolution(parseInt(document.getElementById('cw').value), parseInt(document.getElementById('ch').value));
}
function setResolution(w, h) {
    canvasW = w; canvasH = h;
    canvas.setWidth(w);
    canvas.setHeight(h);
    canvas.renderAll();
    updateCanvasScaling();
    document.getElementById('res-label').innerText = `${w} × ${h}`;
    document.getElementById('layout-resolution').value = `${w}x${h}`;
}

// Salva estado para undo
function saveHistory() {
    history.push(JSON.stringify(canvas.toJSON(['data'])));
    if (history.length > 30) history.shift();
}
function undoAction() {
    if (history.length < 2) return;
    history.pop();
    const prev = history[history.length - 1];
    canvas.loadFromJSON(prev, () => { canvas.renderAll(); });
}
canvas.on('object:added',    saveHistory);
canvas.on('object:modified', saveHistory);
canvas.on('object:removed',  saveHistory);

// ===== FUNDO =====
function updateBgType() {
    const t = document.getElementById('bg-type').value;
    document.getElementById('bg-solid').style.display      = t === 'solid'    ? '' : 'none';
    document.getElementById('bg-gradient').style.display  = t === 'gradient'  ? '' : 'none';
    document.getElementById('bg-image-sec').style.display = t === 'image'     ? '' : 'none';
}

function applyBgSolid() {
    canvas.setBackgroundImage(null);
    canvas.setBackgroundColor(document.getElementById('bg-color').value, canvas.renderAll.bind(canvas));
}

// Paletas predefinidas
const GRAD_PALETTES = [
    { c1:'#0f0c29', c2:'#302b63', c3:'#24243e', dir:'d', label:'Noite' },
    { c1:'#ff416c', c2:'#ff4b2b', c3:'#ff416c', dir:'h', label:'Fogo' },
    { c1:'#00c6ff', c2:'#0072ff', c3:'#00c6ff', dir:'h', label:'Oceano' },
    { c1:'#11998e', c2:'#38ef7d', c3:'#11998e', dir:'d', label:'Floresta' },
    { c1:'#f7971e', c2:'#ffd200', c3:'#f7971e', dir:'h', label:'Dourado' },
    { c1:'#1a1a2e', c2:'#16213e', c3:'#0f3460', dir:'d', label:'Espaço' },
    { c1:'#314755', c2:'#26a0da', c3:'#314755', dir:'v', label:'Aço' },
    { c1:'#DA22FF', c2:'#9733EE', c3:'#DA22FF', dir:'d', label:'Roxo' },
    { c1:'#000000', c2:'#434343', c3:'#000000', dir:'v', label:'Preto' },
    { c1:'#005C97', c2:'#363795', c3:'#005C97', dir:'h', label:'Azul' },
    { c1:'#cb2d3e', c2:'#ef473a', c3:'#cb2d3e', dir:'d', label:'Vermelho' },
    { c1:'#0a3d62', c2:'#0a3d62', c3:'#60a3bc', dir:'v', label:'Mar' },
];

function buildGradPalettes() {
    const el = document.getElementById('grad-palettes');
    GRAD_PALETTES.forEach((p, i) => {
        const sw = document.createElement('div');
        sw.className = 'grad-swatch';
        sw.title = p.label;
        sw.style.background = `linear-gradient(135deg, ${p.c1}, ${p.c3 || p.c2})`;
        sw.onclick = () => {
            document.getElementById('grad-c1').value = p.c1;
            document.getElementById('grad-c2').value = p.c2;
            document.getElementById('grad-c3').value = p.c3 || p.c1;
            document.getElementById('grad-dir').value = p.dir;
            document.querySelectorAll('.grad-swatch').forEach(s => s.classList.remove('active'));
            sw.classList.add('active');
            applyBgGradient();
        };
        el.appendChild(sw);
    });
}
buildGradPalettes();

function applyBgGradient() {
    canvas.setBackgroundImage(null);
    const c1  = document.getElementById('grad-c1').value;
    const c2  = document.getElementById('grad-c2').value;
    const c3  = document.getElementById('grad-c3').value;
    const dir = document.getElementById('grad-dir').value;

    // Coords em pixels do canvas
    const w = canvas.width, h = canvas.height;
    let coords;
    if (dir === 'h') coords = {x1:0, y1:0, x2:w, y2:0};
    else if (dir === 'v') coords = {x1:0, y1:0, x2:0, y2:h};
    else if (dir === 'd') coords = {x1:0, y1:0, x2:w, y2:h};
    else coords = {x1:w, y1:0, x2:0, y2:h}; // reverse diagonal

    canvas.setBackgroundColor(new fabric.Gradient({
        type: 'linear',
        gradientUnits: 'pixels',
        coords: coords,
        colorStops: [
            { offset: 0,   color: c1 },
            { offset: 0.5, color: c2 },
            { offset: 1,   color: c3 }
        ]
    }), canvas.renderAll.bind(canvas));
}

async function applyBgImage(input) {
    const file = input.files[0];
    if (!file) return;

    // Indicador visual no botão (opcional, mas bom ter feedback)
    const btn = document.querySelector('button[onclick*="bg-img-input"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subindo...';
    btn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        const res = await fetch('{{ route("lojista.tvdoor.layouts.upload_asset") }}', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            fabric.Image.fromURL(data.url, img => {
                // Ajusta para cobrir o canvas
                img.set({
                    originX: 'left',
                    originY: 'top',
                    scaleX: canvas.width / img.width,
                    scaleY: canvas.height / img.height
                });
                canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
            }, { crossOrigin: 'anonymous' });
        } else {
            alert('Erro no upload de fundo: ' + data.message);
        }
    } catch (e) {
        alert('Erro ao enviar imagem de fundo.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
        input.value = '';
    }
}

// ===== ELEMENTOS =====
function triggerFile(id) { document.getElementById(id).click(); }

function addText() {
    const t = new fabric.IText('Clique para editar', {
        left: 60, top: 60, fontSize: 40, fill: '#ffffff',
        fontFamily: 'Segoe UI, sans-serif',
    });
    canvas.add(t); canvas.setActiveObject(t); canvas.renderAll();
}

function addClock() {
    const t = new fabric.Text(new Date().toLocaleTimeString('pt-BR'), {
        left: 60, top: 60, fontSize: 80, fill: '#ffffff',
        fontFamily: 'Segoe UI, sans-serif', fontWeight: 'bold',
        data: { type: 'clock' }
    });
    canvas.add(t); canvas.setActiveObject(t); canvas.renderAll();
    setInterval(() => { t.set('text', new Date().toLocaleTimeString('pt-BR')); canvas.renderAll(); }, 1000);
}

function addRect() {
    canvas.add(new fabric.Rect({ left:100, top:100, width:300, height:150, fill:'rgba(108,99,255,0.7)', rx:10, ry:10 }));
    canvas.renderAll();
}

function addCircle() {
    canvas.add(new fabric.Circle({ left:150, top:150, radius:80, fill:'rgba(72,219,251,0.7)' }));
    canvas.renderAll();
}

function addLine() {
    canvas.add(new fabric.Line([0, 0, 400, 0], { stroke:'#ffffff', strokeWidth:4, left:80, top:200 }));
    canvas.renderAll();
}

function addImage(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        fabric.Image.fromURL(e.target.result, img => {
            img.scaleToWidth(250);
            img.set({ left: 100, top: 100 });
            canvas.add(img); canvas.setActiveObject(img); canvas.renderAll();
        });
    };
    reader.readAsDataURL(file);
    input.value = '';
}

async function addGif(input) {
    const file = input.files[0];
    if (!file) return;

    // Indicador visual simples
    const btn = document.querySelector('button[onclick*="gif-upload"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subindo...';
    btn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        const res = await fetch('{{ route("lojista.tvdoor.layouts.upload_asset") }}', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            fabric.Image.fromURL(data.url, img => {
                img.scaleToWidth(300);
                img.set({ left: 100, top: 100, data: { type: 'gif', src: data.url } });
                canvas.add(img); canvas.setActiveObject(img); canvas.renderAll();
            });
        } else {
            alert('Erro no upload: ' + data.message);
        }
    } catch (e) {
        alert('Erro ao enviar GIF.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
        input.value = '';
    }
}

async function addVideo(input) {
    const file = input.files[0];
    if (!file) return;

    const btn = document.querySelector('button[onclick*="video-upload"]');
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Subindo...';
    btn.disabled = true;

    try {
        const formData = new FormData();
        formData.append('file', file);
        formData.append('_token', '{{ csrf_token() }}');

        const res = await fetch('{{ route("lojista.tvdoor.layouts.upload_asset") }}', {
            method: 'POST',
            body: formData
        });
        const data = await res.json();

        if (data.success) {
            // Representa o vídeo como um retângulo placeholder no canvas
            const rect = new fabric.Rect({
                left: 100, top: 100, width: 400, height: 225,
                fill: '#000', stroke: '#6c63ff', strokeWidth: 3,
                rx: 8, ry: 8,
                data: { type: 'video', src: data.url, filename: file.name }
            });
            const label = new fabric.Text('▶ ' + file.name, {
                left: 110, top: 195, fontSize: 16, fill: '#6c63ff',
                fontFamily: 'Segoe UI, sans-serif', selectable: false
            });
            canvas.add(rect);
            canvas.add(label);
            const sel = new fabric.ActiveSelection([rect, label], { canvas: canvas });
            canvas.setActiveObject(sel);
            canvas.renderAll();
        } else {
            alert('Erro no upload: ' + data.message);
        }
    } catch (e) {
        alert('Erro ao enviar vídeo.');
    } finally {
        btn.innerHTML = originalText;
        btn.disabled = false;
        input.value = '';
    }
}

function addProduct(name, price, imgUrl) {
    const startX = 150;
    const startY = 150;
    const cardW = 320; // Aumentado para acomodar preço grande
    const cardH = 400; // Aumentado para melhor espaçamento

    // Fundo do Card (Mais escuro e com borda neon)
    const rect = new fabric.Rect({
        left: startX, top: startY,
        width: cardW, height: cardH,
        fill: 'rgba(5, 5, 20, 0.98)',
        rx: 22, ry: 22,
        stroke: '#6c63ff', strokeWidth: 4,
        shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.8)', blur: 25, offsetX: 10, offsetY: 10 }),
        data: { part: 'bg' }
    });

    // Nome (Maior e com mais espaçamento)
    const nameText = new fabric.IText(name.toUpperCase(), {
        left: startX + 20, top: startY + 240, width: 280,
        fontSize: 22, fill: '#ffffff',
        fontWeight: 'bold', fontFamily: 'Impact, sans-serif',
        textAlign: 'center', splitByGrapheme: true,
        lineHeight: 1.1, charSpacing: 30,
        data: { part: 'name' }
    });

    // Preço (GIGANTE e VERDE NEON)
    const priceText = new fabric.IText('R$ ' + price, {
        left: startX + 20, top: startY + 310, width: 280,
        fontSize: 48, fill: '#43e97b',
        fontWeight: '900', fontFamily: 'Impact, sans-serif',
        textAlign: 'center',
        shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.5)', blur: 8 }),
        data: { part: 'price' }
    });

    const finishAdd = (imgObj) => {
        canvas.add(rect);
        if (imgObj) canvas.add(imgObj);
        canvas.add(nameText);
        canvas.add(priceText);

        const selItems = [rect, nameText, priceText];
        if (imgObj) selItems.push(imgObj);
        
        const sel = new fabric.ActiveSelection(selItems, { canvas: canvas });
        canvas.setActiveObject(sel);
        canvas.renderAll();
    };

    if (imgUrl) {
        fabric.Image.fromURL(imgUrl, img => {
            img.scaleToWidth(240);
            img.set({ 
                left: startX + 40, top: startY + 25, 
                shadow: new fabric.Shadow({ color: 'rgba(0,0,0,0.5)', blur: 20 }),
                data: { part: 'image' }
            });
            finishAdd(img);
        }, { crossOrigin: 'anonymous' });
    } else {
        finishAdd(null);
    }
}

// ===== BUSCA DE PRODUTOS (AJAX, 5 por página) =====
let prodPage = 0;
let prodQuery = '';
let prodTotal = 0;
let prodSearchTimer = null;

function debounceSearch() {
    clearTimeout(prodSearchTimer);
    prodSearchTimer = setTimeout(() => {
        prodPage = 0;
        prodQuery = document.getElementById('prod-search').value.trim();
        document.getElementById('prod-list').innerHTML = '<div id="prod-loading" style="text-align:center;padding:12px;font-size:.75rem;color:#999;"><i class="fas fa-spinner fa-spin"></i> Buscando...</div>';
        searchProdutos();
    }, 400);
}

async function searchProdutos() {
    try {
        const url = '{{ route("lojista.tvdoor.layouts.search_products") }}?q=' + encodeURIComponent(prodQuery) + '&page=' + prodPage;
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        const data = await res.json();
        prodTotal = data.total;

        const list = document.getElementById('prod-list');
        const loading = document.getElementById('prod-loading');
        if (loading) loading.remove();

        if (prodPage === 0) {
            list.innerHTML = '';
        }

        if (data.produtos.length === 0 && prodPage === 0) {
            list.innerHTML = '<p style="font-size:.75rem;color:#999;text-align:center;padding:12px;">Nenhum produto encontrado.</p>';
        }

        data.produtos.forEach(p => {
            const div = document.createElement('div');
            div.className = 'prod-item';
            div.onclick = () => addProduct(p.nome, p.preco, p.imagem_url || '');
            div.innerHTML = `
                <img class="prod-img" src="${p.imagem_url || 'https://placehold.co/34x34/f0f0f0/aaa?text=?'}"
                     onerror="this.src='https://placehold.co/34x34/f0f0f0/aaa?text=?'"
                     alt="${p.nome}" crossorigin="anonymous">
                <div>
                    <div class="prod-name">${p.nome.substring(0, 20)}${p.nome.length > 20 ? '…' : ''}</div>
                    <div class="prod-price">R$ ${p.preco}</div>
                    ${p.codigo ? `<div style="font-size:.62rem;color:#aaa;">${p.codigo}</div>` : ''}
                </div>`;
            list.appendChild(div);
        });

        const loaded = (prodPage + 1) * 5;
        const moreBtn = document.getElementById('prod-more');
        const infoEl  = document.getElementById('prod-info');
        infoEl.innerText = `${Math.min(loaded, prodTotal)} de ${prodTotal}`;
        moreBtn.style.display = loaded < prodTotal ? '' : 'none';
    } catch (e) {
        console.error('Erro ao buscar produtos:', e);
    }
}

function loadMoreProdutos() {
    prodPage++;
    searchProdutos();
}

// Carrega os primeiros produtos ao abrir o editor
document.addEventListener('DOMContentLoaded', () => searchProdutos());

// ===== AÇÕES DE CANVAS =====
function deleteSelected() {
    const obj = canvas.getActiveObject();
    if (obj) { canvas.remove(obj); canvas.discardActiveObject(); canvas.renderAll(); }
}
function duplicateSelected() {
    const obj = canvas.getActiveObject();
    if (!obj) return;
    obj.clone(cloned => {
        cloned.set({ left: obj.left + 20, top: obj.top + 20 });
        canvas.add(cloned); canvas.setActiveObject(cloned); canvas.renderAll();
    }, ['data']);
}
function bringForward()  { const o = canvas.getActiveObject(); if(o){ canvas.bringForward(o); canvas.renderAll(); } }
function sendBackward()  { const o = canvas.getActiveObject(); if(o){ canvas.sendBackwards(o); canvas.renderAll(); } }
function toggleBold()    { const o = canvas.getActiveObject(); if(o){ o.set('fontWeight', o.fontWeight === 'bold' ? 'normal' : 'bold'); canvas.renderAll(); } }
function toggleItalic()  { const o = canvas.getActiveObject(); if(o){ o.set('fontStyle', o.fontStyle === 'italic' ? 'normal' : 'italic'); canvas.renderAll(); } }
function toggleUnderline(){ const o = canvas.getActiveObject(); if(o){ o.set('underline', !o.underline); canvas.renderAll(); } }

function groupObjects() {
    if (!canvas.getActiveObject()) return;
    if (canvas.getActiveObject().type !== 'activeSelection') return;
    canvas.getActiveObject().toGroup();
    canvas.requestRenderAll();
}

function ungroupObjects() {
    if (!canvas.getActiveObject()) return;
    if (canvas.getActiveObject().type !== 'group') return;
    canvas.getActiveObject().toActiveSelection();
    canvas.requestRenderAll();
}

// ===== PROPRIEDADES =====
canvas.on('selection:created', showProps);
canvas.on('selection:updated', showProps);
canvas.on('selection:cleared', () => {
    document.getElementById('no-selection').style.display = '';
    document.getElementById('obj-props').style.display = 'none';
});

function showProps() {
    let obj = canvas.getActiveObject();
    if (!obj) return;

    // Se for seleção múltipla, pegamos as propriedades do primeiro objeto para visualização
    let displayObj = obj;
    if (obj.type === 'activeSelection') {
        const objects = obj.getObjects();
        displayObj = objects[0] || obj;
    }

    document.getElementById('no-selection').style.display = 'none';
    document.getElementById('obj-props').style.display = '';

    // Campos básicos
    document.getElementById('prop-text').value       = displayObj.text || '';
    document.getElementById('prop-fill').value       = (typeof displayObj.fill === 'string' && displayObj.fill.startsWith('#')) ? displayObj.fill : '#ffffff';
    document.getElementById('prop-fontsize').value   = displayObj.fontSize || 20;
    document.getElementById('prop-fontfamily').value = displayObj.fontFamily || 'Segoe UI, sans-serif';
    document.getElementById('prop-bg').value         = displayObj.backgroundColor || (displayObj.type === 'rect' ? displayObj.fill : '#000000');
    document.getElementById('prop-opacity').value    = displayObj.opacity ?? 1;
    document.getElementById('opacity-val').innerText = Math.round((displayObj.opacity ?? 1) * 100);
    document.getElementById('prop-x').value          = Math.round(obj.left || 0); // Posição do grupo/objeto atual
    document.getElementById('prop-y').value          = Math.round(obj.top  || 0);
    document.getElementById('prop-w').value          = Math.round(obj.getScaledWidth()  || 0);
    document.getElementById('prop-h').value          = Math.round(obj.getScaledHeight() || 0);
    document.getElementById('prop-angle').value      = Math.round(obj.angle || 0);

    // Propriedades avançadas de texto
    const propLineHeight = document.getElementById('prop-lineheight');
    const propCharSpacing = document.getElementById('prop-charspacing');

    if (displayObj.lineHeight !== undefined) {
        propLineHeight.value = displayObj.lineHeight;
        document.getElementById('lineheight-val').innerText = displayObj.lineHeight.toFixed(2);
        propLineHeight.parentElement.style.display = 'block';
    } else {
        propLineHeight.parentElement.style.display = 'none';
    }

    if (displayObj.charSpacing !== undefined) {
        propCharSpacing.value = displayObj.charSpacing;
        document.getElementById('charspacing-val').innerText = displayObj.charSpacing;
        propCharSpacing.parentElement.style.display = 'block';
    } else {
        propCharSpacing.parentElement.style.display = 'none';
    }
}
function setOpacity(val) {
    setPropNum('opacity', val);
    document.getElementById('opacity-val').innerText = Math.round(val * 100);
}
function setProp(prop, val) {
    const o = canvas.getActiveObject();
    if (!o) return;

    if (o.type === 'activeSelection') {
        o.forEachObject(obj => {
            // Só aplica certas propriedades se o objeto suportar (ex: text props em imagens/rects)
            if (['lineHeight', 'charSpacing', 'text', 'fontFamily', 'textAlign'].includes(prop) && !obj.text) return;
            obj.set(prop, val);
        });
    } else {
        o.set(prop, val);
    }
    canvas.renderAll();
    canvas.fire('object:modified'); // Notifica para salvar estado se necessário
}
function setPropNum(prop, val) { setProp(prop, parseFloat(val)); }
canvas.on('object:modified', showProps);

// ===== SALVAR LAYOUT =====
// ===== SALVAR LAYOUT (COM SAÍDA) =====
function salvarLayout() {
    const name = document.getElementById('layout-name').value.trim();
    const duration = document.getElementById('layout-duration').value;
    if (!name) { alert('Digite um nome para o layout!'); return; }

    const content = obterConteudoLayout();

    document.getElementById('save-name').value       = name;
    document.getElementById('save-duration').value   = duration;
    document.getElementById('save-content').value    = content;
    document.getElementById('save-resolution').value = document.getElementById('layout-resolution').value;
    document.getElementById('save-form').submit();
}

// ===== HELPER: extrai JSON atual do canvas =====
function obterConteudoLayout() {
    // IMPORTANTE: Descartar seleção ativa antes de converter para JSON
    // Se houver uma ActiveSelection, o Fabric salva coordenadas relativas ao grupo de seleção, 
    // o que causa o erro de "tela preta" ou deslocamento ao recarregar.
    const active = canvas.getActiveObject();
    if (active) {
        canvas.discardActiveObject();
        canvas.renderAll();
    }

    const json = canvas.toJSON(['data']);
    
    // Se o usuário estava editando e resetamos a seleção, tentamos restaurar para não atrapalhar o fluxo
    if (active) {
        canvas.setActiveObject(active);
        canvas.renderAll();
    }

    return JSON.stringify({
        fabric:     json,
        width:      canvasW,
        height:     canvasH,
        resolution: document.getElementById('layout-resolution').value,
    });
}

// ===== SALVAR SEM SAIR (AJAX) =====
async function salvarSemSair() {
    const name = document.getElementById('layout-name').value.trim();
    const duration = document.getElementById('layout-duration').value;
    if (!name) { alert('Digite um nome para o layout!'); return; }

    const btn = document.getElementById('btn-save-only');
    const origText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i> Salvando...';
    btn.disabled = true;

    const content = obterConteudoLayout();
    const resolution = document.getElementById('layout-resolution').value;

    const formData = new FormData();
    formData.append('_token', '{{ csrf_token() }}');
    @isset($layout)
    formData.append('_method', 'PUT');
    @endisset
    formData.append('name', name);
    formData.append('duration', duration);
    formData.append('content', content);
    formData.append('resolution', resolution);

    try {
        const url = document.getElementById('save-form').action;
        const res = await fetch(url, { method: 'POST', body: formData });
        const text = await res.text();
        // Se redirecionar, houve sucesso (Laravel normalmente retorna redirect 302)
        showToast('✅ Layout salvo com sucesso!', 'success');

        // Atualiza o ID da prévia se recém criado
        if (res.ok && !document.getElementById('save-form').action.includes('/update')) {
            // Tenta extrair ID do redirect
            const match = text.match(/\/layouts\/(\d+)\/edit/);
            if (match) {
                document.getElementById('save-form').action = document.getElementById('save-form').action.replace('layouts/store', `layouts/${match[1]}/update`);
            }
        }
    } catch (e) {
        showToast('❌ Erro ao salvar. Tente novamente.', 'error');
    } finally {
        btn.innerHTML = origText;
        btn.disabled = false;
    }
}

// ===== PRÉVIA EM NOVA ABA =====
function previewLayout() {
    const content = obterConteudoLayout();
    // Encoda e abre em nova aba via sessionStorage
    sessionStorage.setItem('tvdoor_preview', content);
    window.open('{{ route("lojista.tvdoor.layouts.preview") }}', '_blank');
}

// ===== TOAST NOTIFICATION =====
function showToast(msg, type = 'success') {
    let toast = document.getElementById('editor-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'editor-toast';
        toast.style.cssText = `
            position:fixed; bottom:20px; right:20px; z-index:99999;
            padding:12px 20px; border-radius:10px; font-size:.85rem;
            font-weight:600; color:#fff; box-shadow:0 4px 20px rgba(0,0,0,.3);
            transition:all .3s; opacity:0; transform:translateY(20px);
        `;
        document.body.appendChild(toast);
    }
    toast.style.background = type === 'success' ? 'linear-gradient(135deg,#43e97b,#38f9d7)' : 'linear-gradient(135deg,#ff416c,#ff4b2b)';
    toast.innerText = msg;
    toast.style.opacity = '1';
    toast.style.transform = 'translateY(0)';
    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
    }, 3000);
}


// ===== INICIALIZAR =====
applyBgSolid();
saveHistory(); // Estado inicial

// ===== CARREGAR LAYOUT EXISTENTE =====
@isset($layout)
(function() {
    // 1. Define a resolução primeiro
    const resValue = @json($layout->resolution ?? '1920x1080');
    const [rw, rh] = resValue.split('x').map(Number);
    if (rw && rh) setResolution(rw, rh);

    // 2. Carrega o conteúdo
    let data = @json($layout->content);
    if (data) {
        try {
            // Se for string vinda do banco (raro com Laravel cast, mas acontece), decodifica
            const canvasData = typeof data === 'string' ? JSON.parse(data) : data;
            
            // Suporta formatos {"fabric": {...}} ou direto {...}
            const toLoad = canvasData.fabric || canvasData;
            
            if (toLoad && (toLoad.objects || toLoad.backgroundImage)) {
                // Pequeno delay para garantir que o Fabric Canvas esteja estável
                setTimeout(() => {
                    canvas.loadFromJSON(toLoad, () => {
                        console.log("Layout carregado com sucesso!");
                        
                        // Garante que o fundo seja aplicado se estiver no JSON
                        if (toLoad.background && typeof toLoad.background === 'string') {
                            canvas.setBackgroundColor(toLoad.background, () => canvas.renderAll());
                        }

                        canvas.renderAll();
                        updateCanvasScaling();
                        
                        // Força redimensionamento visual após imagens carregarem
                        setTimeout(updateCanvasScaling, 500);
                        setTimeout(updateCanvasScaling, 1500);
                    });
                }, 100);
            }
        } catch(e) { 
            console.error("Erro ao carregar layout:", e);
        }
    }
})();
@endisset
</script>
@endsection
