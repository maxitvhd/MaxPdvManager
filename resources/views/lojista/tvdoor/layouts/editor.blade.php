@extends('layouts.user_type.auth')

@section('content')
<style>
.editor-wrap { display:flex; gap:12px; height: calc(100vh - 120px); }
.panel { background:#fff; border-radius:12px; box-shadow:0 2px 12px rgba(0,0,0,.08); overflow:hidden; }
.panel-header { font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.05rem; padding:10px 14px; background:#f8f9fa; border-bottom:1px solid #e9ecef; color:#495057; }
.tools-panel { width:200px; flex-shrink:0; display:flex; flex-direction:column; }
.tool-btn { display:flex; align-items:center; gap:8px; padding:8px 14px; font-size:.8rem; cursor:pointer; border:none; background:none; width:100%; text-align:left; transition:background .15s; color:#344767; }
.tool-btn:hover { background:#f0f2ff; color:#5e72e4; }
.tool-btn i { width:16px; text-align:center; color:#7c8db5; }
.bg-panel { border-top:1px solid #e9ecef; }
.canvas-panel { flex:1; position:relative; display:flex; flex-direction:column; }
.canvas-toolbar { display:flex; align-items:center; gap:8px; padding:8px 12px; border-bottom:1px solid #e9ecef; flex-wrap:wrap; }
.canvas-wrap { flex:1; overflow:auto; display:flex; align-items:center; justify-content:center; background:#e9ecef; padding:20px; }
#main-canvas { box-shadow:0 4px 24px rgba(0,0,0,.25); }
.props-panel { width:210px; flex-shrink:0; display:flex; flex-direction:column; overflow-y:auto; }
.prop-group { padding:10px 14px; border-bottom:1px solid #f0f0f0; }
.prop-group label { font-size:.72rem; font-weight:600; color:#7c8db5; display:block; margin-bottom:4px; }
.prop-group input, .prop-group select { width:100%; font-size:.8rem; padding:5px 8px; border:1px solid #dee2e6; border-radius:6px; color:#344767; }
.grad-stop { display:flex; gap:6px; align-items:center; margin-bottom:6px; }
.undo-redo-btn { background:#f0f2ff; border:none; border-radius:6px; padding:5px 10px; font-size:.8rem; cursor:pointer; color:#5e72e4; }
.undo-redo-btn:hover { background:#5e72e4; color:#fff; }
.save-btn { background:linear-gradient(135deg,#43e97b,#38f9d7); border:none; border-radius:6px; padding:6px 16px; font-size:.8rem; font-weight:700; color:#fff; cursor:pointer; }
.res-select { font-size:.8rem; padding:4px 8px; border:1px solid #dee2e6; border-radius:6px; }
</style>

<div class="container-fluid py-3">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0">Editor de Layout TvDoor</h6>
    <div class="d-flex gap-2">
      <a href="{{ route('lojista.tvdoor.layouts.index') }}" class="btn btn-sm btn-outline-secondary">← Voltar</a>
      <button class="save-btn" onclick="salvarLayout()"><i class="fas fa-save me-1"></i> Salvar Layout</button>
    </div>
  </div>

  <div class="editor-wrap">
    <!-- ========== Painel Esquerdo: Elementos ========== -->
    <div class="panel tools-panel">
      <div class="panel-header">Elementos</div>
      <button class="tool-btn" onclick="addText()"><i class="fas fa-font"></i> Texto</button>
      <button class="tool-btn" onclick="addClock()"><i class="fas fa-clock"></i> Relógio</button>
      <button class="tool-btn" onclick="addRect()"><i class="fas fa-square"></i> Retângulo</button>
      <button class="tool-btn" onclick="addCircle()"><i class="far fa-circle"></i> Círculo</button>
      <button class="tool-btn" onclick="addLine()"><i class="fas fa-minus"></i> Linha</button>
      <button class="tool-btn" onclick="document.getElementById('img-upload').click()"><i class="fas fa-image"></i> Imagem</button>
      <input type="file" id="img-upload" accept="image/*" style="display:none" onchange="addImage(this)">

      <div class="bg-panel">
        <div class="panel-header">Fundo do Canvas</div>
        <div style="padding:10px 14px;">
          <label style="font-size:.72rem; font-weight:600; color:#7c8db5; display:block; margin-bottom:6px;">Tipo</label>
          <select id="bg-type" class="res-select w-100 mb-2" onchange="updateBgType()">
            <option value="solid">Cor Sólida</option>
            <option value="gradient">Gradiente</option>
            <option value="image">Imagem</option>
          </select>

          <div id="bg-solid">
            <input type="color" id="bg-color" value="#1a1a2e" class="w-100" style="height:36px;border-radius:6px;border:1px solid #dee2e6;cursor:pointer;" onchange="applyBgSolid()">
          </div>

          <div id="bg-gradient" style="display:none">
            <div class="grad-stop">
              <input type="color" id="grad-c1" value="#0f0c29" onchange="applyBgGradient()">
              <input type="color" id="grad-c2" value="#302b63" onchange="applyBgGradient()">
            </div>
            <select id="grad-dir" class="res-select w-100" onchange="applyBgGradient()">
              <option value="h">Horizontal</option>
              <option value="v">Vertical</option>
              <option value="d">Diagonal</option>
            </select>
          </div>

          <div id="bg-image-sec" style="display:none">
            <button class="tool-btn w-100 mt-1" onclick="document.getElementById('bg-img-input').click()" style="border:1px dashed #dee2e6;border-radius:6px;justify-content:center;">
              <i class="fas fa-upload me-1"></i> Carregar Fundo
            </button>
            <input type="file" id="bg-img-input" accept="image/*" style="display:none" onchange="applyBgImage(this)">
          </div>
        </div>
      </div>

      <div class="bg-panel">
        <div class="panel-header">Produtos</div>
        <div style="padding:10px 14px; max-height:200px; overflow-y:auto;">
          @forelse($produtos as $prod)
          <div onclick="addProduct('{{ addslashes($prod->nome) }}', '{{ number_format($prod->preco_venda, 2, ',', '.') }}', '{{ $prod->imagem ? asset('storage/'.$prod->imagem) : '' }}')"
               style="display:flex;align-items:center;gap:8px;padding:6px;border-radius:8px;cursor:pointer;margin-bottom:4px;border:1px solid #f0f0f0;">
            <img src="{{ $prod->imagem ? asset('storage/'.$prod->imagem) : 'https://via.placeholder.com/40' }}" style="width:36px;height:36px;object-fit:cover;border-radius:6px;">
            <div>
              <div style="font-size:.75rem;font-weight:700;color:#344767;">{{ Str::limit($prod->nome, 18) }}</div>
              <div style="font-size:.7rem;color:#43e97b;font-weight:700;">R$ {{ number_format($prod->preco_venda, 2, ',', '.') }}</div>
            </div>
          </div>
          @empty
          <p style="font-size:.75rem;color:#999;">Catálogo vazio.</p>
          @endforelse
        </div>
      </div>
    </div>

    <!-- ========== Canvas Central ========== -->
    <div class="panel canvas-panel">
      <div class="canvas-toolbar">
        <select class="res-select" id="res-select" onchange="changeResolution()">
          <option value="1920x1080">Full HD (1920×1080)</option>
          <option value="1280x720">HD (1280×720)</option>
          <option value="1080x1920">Vertical 9:16 (1080×1920)</option>
          <option value="1080x1080">Quadrado (1080×1080)</option>
          <option value="3840x2160">4K (3840×2160)</option>
          <option value="custom">Personalizado...</option>
        </select>
        <div id="custom-res" style="display:none;gap:4px;align-items:center;" class="d-flex">
          <input type="number" id="cw" value="1920" class="res-select" style="width:70px;" placeholder="W">
          <span>×</span>
          <input type="number" id="ch" value="1080" class="res-select" style="width:70px;" placeholder="H">
          <button class="undo-redo-btn" onclick="applyCustomRes()">OK</button>
        </div>
        <span style="font-size:.75rem;color:#aaa;" id="res-label">1920 × 1080</span>
        <div style="flex:1"></div>
        <button class="undo-redo-btn" onclick="canvas.undo ? canvas.undo() : null"><i class="fas fa-undo"></i></button>
        <button class="undo-redo-btn" onclick="deleteSelected()"><i class="fas fa-trash"></i></button>
        <button class="undo-redo-btn" onclick="canvas.setActiveObject(null);canvas.renderAll()"><i class="fas fa-mouse-pointer"></i> Deselect</button>
      </div>
      <div class="canvas-wrap">
        <canvas id="main-canvas"></canvas>
      </div>
    </div>

    <!-- ========== Painel Direito: Propriedades ========== -->
    <div class="panel props-panel">
      <div class="panel-header">Propriedades</div>
      <div id="no-selection" style="padding:20px;font-size:.8rem;color:#999;text-align:center;">
        Selecione um elemento para editar.
      </div>
      <div id="obj-props" style="display:none;">
        <div class="prop-group">
          <label>Texto</label>
          <input type="text" id="prop-text" oninput="setProp('text', this.value)">
        </div>
        <div class="prop-group">
          <label>Cor do Texto</label>
          <input type="color" id="prop-fill" onchange="setProp('fill', this.value)" style="height:32px;cursor:pointer;">
        </div>
        <div class="prop-group">
          <label>Tamanho da Fonte</label>
          <input type="number" id="prop-fontsize" oninput="setPropNum('fontSize', this.value)" min="8" max="300">
        </div>
        <div class="prop-group">
          <label>Negrito</label>
          <select id="prop-bold" onchange="setProp('fontWeight', this.value)">
            <option value="normal">Normal</option>
            <option value="bold">Negrito</option>
          </select>
        </div>
        <div class="prop-group">
          <label>Fundo do Objeto</label>
          <input type="color" id="prop-bg" onchange="setProp('backgroundColor', this.value)" style="height:32px;cursor:pointer;">
        </div>
        <div class="prop-group">
          <label>Opacidade</label>
          <input type="range" id="prop-opacity" min="0" max="1" step="0.05" value="1" oninput="setPropNum('opacity', this.value)">
        </div>
        <div class="prop-group">
          <label>X</label>
          <input type="number" id="prop-x" oninput="setPropNum('left', this.value)">
        </div>
        <div class="prop-group">
          <label>Y</label>
          <input type="number" id="prop-y" oninput="setPropNum('top', this.value)">
        </div>
      </div>

      <div class="panel-header mt-2">Salvar</div>
      <div class="prop-group">
        <label>Nome do Layout</label>
        <input type="text" id="layout-name" placeholder="Ex: Promoção de Verão">
      </div>
      <div class="prop-group">
        <label>Resolução Alvo</label>
        <input type="text" id="layout-resolution" value="1920x1080" readonly style="color:#aaa;">
      </div>
    </div>
  </div>
</div>

<!-- Form oculto para salvar layout -->
@isset($layout)
  <form id="save-form" action="{{ route('lojista.tvdoor.layouts.update', $layout->id) }}" method="POST" style="display:none">
    @csrf @method('PUT')
@else
  <form id="save-form" action="{{ route('lojista.tvdoor.layouts.store') }}" method="POST" style="display:none">
    @csrf
@endisset
  <input type="hidden" name="name"       id="save-name">
  <input type="hidden" name="content"    id="save-content">
  <input type="hidden" name="resolution" id="save-resolution">
</form>

<!-- Fabric.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
<script>
let canvasW = 1920, canvasH = 1080;
const SCALE = 0.45; // escala de exibição para caber na tela

// Inicializar Fabric Canvas
const canvas = new fabric.Canvas('main-canvas', {
    width: canvasW * SCALE,
    height: canvasH * SCALE,
    backgroundColor: '#1a1a2e',
    preserveObjectStacking: true,
});

// ===== Funções de Resolução =====
function changeResolution() {
    const val = document.getElementById('res-select').value;
    document.getElementById('custom-res').style.display = val === 'custom' ? 'flex' : 'none';
    if (val === 'custom') return;
    const [w, h] = val.split('x').map(Number);
    setResolution(w, h);
}

function applyCustomRes() {
    const w = parseInt(document.getElementById('cw').value);
    const h = parseInt(document.getElementById('ch').value);
    setResolution(w, h);
}

function setResolution(w, h) {
    canvasW = w; canvasH = h;
    canvas.setWidth(w * SCALE);
    canvas.setHeight(h * SCALE);
    canvas.renderAll();
    document.getElementById('res-label').innerText = `${w} × ${h}`;
    document.getElementById('layout-resolution').value = `${w}x${h}`;
}

// ===== Funções de Fundo =====
function updateBgType() {
    const t = document.getElementById('bg-type').value;
    document.getElementById('bg-solid').style.display       = t === 'solid'    ? '' : 'none';
    document.getElementById('bg-gradient').style.display   = t === 'gradient'  ? '' : 'none';
    document.getElementById('bg-image-sec').style.display  = t === 'image'     ? '' : 'none';
}

function applyBgSolid() {
    canvas.setBackgroundColor(document.getElementById('bg-color').value, canvas.renderAll.bind(canvas));
}

function applyBgGradient() {
    const c1 = document.getElementById('grad-c1').value;
    const c2 = document.getElementById('grad-c2').value;
    const dir = document.getElementById('grad-dir').value;
    const coords = dir === 'h' ? {x1:0,y1:0,x2:1,y2:0} : dir === 'v' ? {x1:0,y1:0,x2:0,y2:1} : {x1:0,y1:0,x2:1,y2:1};
    canvas.setBackgroundColor(new fabric.Gradient({
        type: 'linear',
        gradientUnits: 'percentage',
        coords: coords,
        colorStops: [{offset:0, color:c1}, {offset:1, color:c2}]
    }), canvas.renderAll.bind(canvas));
}

function applyBgImage(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        fabric.Image.fromURL(e.target.result, img => {
            img.scaleToWidth(canvas.width);
            canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas));
        });
    };
    reader.readAsDataURL(file);
}

// ===== Adicionar Elementos =====
function addText() {
    const t = new fabric.IText('Clique para editar', {
        left: 50, top: 50, fontSize: 40, fill: '#ffffff',
        fontFamily: 'Segoe UI, sans-serif',
    });
    canvas.add(t); canvas.setActiveObject(t); canvas.renderAll();
}

let clockObj = null;
function addClock() {
    const t = new fabric.Text(new Date().toLocaleTimeString('pt-BR'), {
        left: 60, top: 60, fontSize: 80, fill: '#ffffff',
        fontFamily: 'Segoe UI, sans-serif',
        fontWeight: 'bold',
        data: { type: 'clock' }
    });
    canvas.add(t); canvas.setActiveObject(t); canvas.renderAll();
    // Atualiza o relógio a cada segundo
    setInterval(() => {
        t.set('text', new Date().toLocaleTimeString('pt-BR'));
        canvas.renderAll();
    }, 1000);
}

function addRect() {
    canvas.add(new fabric.Rect({ left:100, top:100, width:200*SCALE*2, height:120*SCALE*2, fill:'rgba(108,99,255,0.7)', rx:8, ry:8 }));
    canvas.renderAll();
}

function addCircle() {
    canvas.add(new fabric.Circle({ left:150, top:150, radius:60, fill:'rgba(72,219,251,0.7)' }));
    canvas.renderAll();
}

function addLine() {
    canvas.add(new fabric.Line([50, 50, 400, 50], { stroke:'#fff', strokeWidth:3, left:50, top:150 }));
    canvas.renderAll();
}

function addImage(input) {
    const file = input.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        fabric.Image.fromURL(e.target.result, img => {
            img.scaleToWidth(200);
            img.set({ left: 100, top: 100 });
            canvas.add(img); canvas.setActiveObject(img); canvas.renderAll();
        });
    };
    reader.readAsDataURL(file);
    input.value = '';
}

function addProduct(name, price, imgUrl) {
    const group = [];
    const rect = new fabric.Rect({ width:220, height:260, fill:'rgba(255,255,255,0.1)', rx:12, ry:12, stroke:'rgba(255,255,255,0.2)', strokeWidth:1 });
    const nameText = new fabric.Text(name, { top:150, left:10, width:200, fontSize:18, fill:'#fff', fontWeight:'bold', fontFamily:'Segoe UI', textAlign:'center' });
    const priceText = new fabric.Text('R$ ' + price, { top:185, left:10, width:200, fontSize:22, fill:'#43e97b', fontWeight:'bold', fontFamily:'Segoe UI', textAlign:'center' });

    const createGroup = (imgObj) => {
        const objs = imgObj ? [rect, imgObj, nameText, priceText] : [rect, nameText, priceText];
        const g = new fabric.Group(objs, { left:100, top:100 });
        canvas.add(g); canvas.setActiveObject(g); canvas.renderAll();
    };

    if (imgUrl) {
        fabric.Image.fromURL(imgUrl, img => {
            img.scaleToWidth(160);
            img.set({ top:10, left:30 });
            createGroup(img);
        }, { crossOrigin: 'anonymous' });
    } else {
        createGroup(null);
    }
}

function deleteSelected() {
    const obj = canvas.getActiveObject();
    if (obj) { canvas.remove(obj); canvas.discardActiveObject(); canvas.renderAll(); }
}

// ===== Propriedades do Objeto Selecionado =====
canvas.on('selection:created', showProps);
canvas.on('selection:updated', showProps);
canvas.on('selection:cleared', () => {
    document.getElementById('no-selection').style.display = '';
    document.getElementById('obj-props').style.display = 'none';
});

function showProps() {
    const obj = canvas.getActiveObject();
    if (!obj) return;
    document.getElementById('no-selection').style.display = 'none';
    document.getElementById('obj-props').style.display = '';
    document.getElementById('prop-text').value = obj.text || '';
    document.getElementById('prop-fill').value = obj.fill || '#ffffff';
    document.getElementById('prop-fontsize').value = obj.fontSize || 20;
    document.getElementById('prop-bold').value = obj.fontWeight || 'normal';
    document.getElementById('prop-bg').value = obj.backgroundColor || '#000000';
    document.getElementById('prop-opacity').value = obj.opacity || 1;
    document.getElementById('prop-x').value = Math.round(obj.left || 0);
    document.getElementById('prop-y').value = Math.round(obj.top || 0);
}

function setProp(prop, val) {
    const obj = canvas.getActiveObject();
    if (obj) { obj.set(prop, val); canvas.renderAll(); }
}

function setPropNum(prop, val) {
    setProp(prop, parseFloat(val));
}

canvas.on('object:modified', showProps);

// ===== Salvar Layout =====
function salvarLayout() {
    const name = document.getElementById('layout-name').value.trim();
    if (!name) { alert('Digite um nome para o layout!'); return; }

    // Serializar canvas como JSON (FabricJS format)
    const json = canvas.toJSON(['data']);
    const content = JSON.stringify({
        fabric: json,
        width: canvasW,
        height: canvasH,
        resolution: document.getElementById('layout-resolution').value,
    });

    document.getElementById('save-name').value = name;
    document.getElementById('save-content').value = content;
    document.getElementById('save-resolution').value = document.getElementById('layout-resolution').value;
    document.getElementById('save-form').submit();
}

// Inicializar fundo padrão
applyBgSolid();

// ===== MODO EDIÇÃO: Carregar layout existente =====
@isset($layout)
@php
    $existingName = $layout->name;
    $existingRes  = $layout->resolution ?? '1920x1080';
    $existingContent = is_array($layout->content) ? json_encode($layout->content) : $layout->content;
@endphp
(function() {
    document.getElementById('layout-name').value       = @json($existingName);
    document.getElementById('layout-resolution').value = @json($existingRes);

    // Ajustar resolução no select
    const resSel = document.getElementById('res-select');
    const res    = @json($existingRes);
    Array.from(resSel.options).forEach(o => o.selected = (o.value === res));
    const [rw, rh] = res.split('x').map(Number);
    if (rw && rh) setResolution(rw, rh);

    // Carregar objetos do canvas
    const existing = @json($existingContent);
    if (existing && existing.fabric) {
        canvas.loadFromJSON(existing.fabric, () => {
            canvas.renderAll();
        });
    }
})();
@endisset
</script>
@endsection
