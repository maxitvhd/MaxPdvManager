@extends('layouts.user_type.auth')

@section('content')
<div class="container-fluid py-3">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 px-1">
        <div>
            <h4 class="mb-0 fw-bold"><i class="fas fa-robot text-primary me-2"></i>Criar Tema com IA</h4>
            <p class="text-sm text-muted mb-0">Descreva o tema e a IA gera o template Blade completo com variáveis dinâmicas</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.maxdivulga.themes') }}" class="btn btn-sm btn-outline-secondary mb-0">
                <i class="fas fa-list me-1"></i> Temas
            </a>
        </div>
    </div>

    <div class="row g-3" style="height: calc(100vh - 180px); min-height: 600px;">

        {{-- ── Coluna 1: Formulário ── --}}
        <div class="col-lg-3 d-flex flex-column gap-3">

            {{-- Identidade --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-primary py-2 px-3">
                    <h6 class="mb-0 text-white text-sm"><i class="fas fa-tag me-1"></i> Identificação</h6>
                </div>
                <div class="card-body p-3">
                    <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Nome do Tema</label>
                    <input type="text" id="themeName" class="form-control form-control-sm mb-2" placeholder="Ex: Azul Premium 2025" value="">
                    <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Identifier (snake_case)</label>
                    <input type="text" id="themeIdentifier" class="form-control form-control-sm" placeholder="azul_premium_2025">
                    <small class="text-muted text-xs">Apenas letras minúsculas, números e underscore</small>
                </div>
            </div>

            {{-- Prompt --}}
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient-info py-2 px-3">
                    <h6 class="mb-0 text-white text-sm"><i class="fas fa-magic me-1"></i> Descreva o Tema</h6>
                </div>
                <div class="card-body p-3">
                    <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Descrição para a IA</label>
                    <textarea id="descricao" class="form-control form-control-sm mb-3" rows="4"
                        placeholder="Ex: Encarte moderno com fundo azul marinho, logo grande centralizada, produtos em 3 colunas com cards arredondados, rodapé dourado com sombra..."></textarea>

                    <div class="row g-2 mb-2">
                        <div class="col-6">
                            <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Cor Primária</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="corPrimaria" value="#003A7A" class="form-control form-control-color" style="height:32px;width:100%;">
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Cor Destaque</label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="corSecundaria" value="#FFD700" class="form-control form-control-color" style="height:32px;width:100%;">
                            </div>
                        </div>
                    </div>

                    <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Colunas de Produtos</label>
                    <div class="d-flex gap-2 mb-2">
                        @foreach([2,3,4] as $c)
                        <button type="button" class="btn btn-xs {{ $c == 3 ? 'btn-primary' : 'btn-outline-secondary' }} mb-0 flex-1 col-btn"
                            data-cols="{{ $c }}" onclick="setCols({{ $c }}, this)">{{ $c }} col</button>
                        @endforeach
                    </div>
                    <input type="hidden" id="colunas" value="3">

                    <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Estilo do Card</label>
                    <select id="estiloCard" class="form-select form-select-sm mb-2">
                        <option value="detalhado">Detalhado (imagem + nome + preços)</option>
                        <option value="minimalista">Minimalista (nome + preço grande)</option>
                        <option value="premium">Premium (fundo gradiente, sombras)</option>
                    </select>

                    <label class="text-xs font-weight-bold text-uppercase text-secondary mb-1">Dimensões</label>
                    <select id="altura" class="form-select form-select-sm mb-2">
                        <option value="1080x1920">Folheto Vertical (1080×1920px)</option>
                        <option value="1920x1080">Horizontal Full HD (1920×1080px)</option>
                        <option value="1080x1080">Quadrado Instagram (1080×1080px)</option>
                        <option value="800x1200">A4 Vertical (~800×1200px)</option>
                    </select>

                    <div class="d-flex gap-3 mb-0 mt-1">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="mostrarDesconto" checked>
                            <label class="form-check-label text-xs" for="mostrarDesconto">Badge desconto</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="aiaCopy" checked>
                            <label class="form-check-label text-xs" for="aiaCopy">Área copy IA</label>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Botão Gerar --}}
            <button class="btn btn-lg btn-primary mb-0 w-100" id="btnGerar" onclick="gerarTema()">
                <i class="fas fa-robot me-2"></i> Gerar com IA
            </button>

            {{-- Salvar --}}
            <button class="btn btn-success mb-0 w-100" id="btnSalvar" onclick="salvarTema()" disabled>
                <i class="fas fa-save me-2"></i> Salvar Template
            </button>

        </div>

        {{-- ── Coluna 2: Monaco Editor ── --}}
        <div class="col-lg-5 d-flex flex-column">
            <div class="card shadow-sm border-0 flex-grow-1 overflow-hidden d-flex flex-column">
                <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between">
                    <span class="text-sm fw-bold"><i class="fas fa-code me-1 text-warning"></i> Código Blade Gerado</span>
                    <div class="d-flex gap-2 align-items-center">
                        <span id="editorStatus" class="text-muted text-xs"></span>
                        <button class="btn btn-xs btn-outline-secondary mb-0" onclick="refreshPreview()" title="Actualizar preview">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                </div>
                <div id="editor" class="flex-grow-1" style="min-height:0;"></div>
            </div>
        </div>

        {{-- ── Coluna 3: Preview ── --}}
        <div class="col-lg-4 d-flex flex-column">
            <div class="card shadow-sm border-0 flex-grow-1 overflow-hidden d-flex flex-column">
                <div class="card-header py-2 px-3 d-flex align-items-center justify-content-between">
                    <span class="text-sm fw-bold"><i class="fas fa-eye me-1 text-success"></i> Preview ao Vivo</span>
                    <span id="previewStatus" class="text-muted text-xs">Aguardando código...</span>
                </div>
                <div class="flex-grow-1 overflow-auto d-flex align-items-start justify-content-center p-2"
                    style="background: repeating-linear-gradient(45deg,#f0f0f0,#f0f0f0 10px,#e8e8e8 10px,#e8e8e8 20px);">
                    <div id="previewWrapper" style="transform-origin:top center; transform:scale(0.33);">
                        <iframe id="previewIframe" style="width:1080px;height:1920px;border:none;display:block;border-radius:8px;box-shadow:0 20px 60px rgba(0,0,0,.4);"
                            srcdoc="<div style='font-family:sans-serif;padding:60px;color:#aaa;text-align:center;font-size:24px;margin-top:200px;'>Código gerado aparecerá aqui no preview</div>">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/editor/editor.main.min.css">
<style>
    .col-btn.btn { flex: 1; padding: 4px !important; font-size: .75rem !important; }
    .form-control-color { padding: 0 !important; border-radius: 6px; cursor: pointer; }
</style>
@endpush

@push('scripts')
<script>
const generateUrl = '{{ route("admin.maxdivulga.theme_generate_ai") }}';
const saveUrl     = '{{ route("admin.maxdivulga.theme_save_ai") }}';
const csrfToken   = '{{ csrf_token() }}';
</script>
@verbatim
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script>
let monacoEditor = null;
let previewTimer = null;

// ── Monaco ───────────────────────────────────────────────────
require.config({ paths: { vs: 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' } });
require(['vs/editor/editor.main'], function () {
    monacoEditor = monaco.editor.create(document.getElementById('editor'), {
        value: '',
        language: 'html',
        theme: 'vs-dark',
        automaticLayout: true,
        fontSize: 13,
        minimap: { enabled: false },
        wordWrap: 'on',
        lineNumbers: 'on',
        scrollBeyondLastLine: false,
    });

    // Auto-preview ao editar
    monacoEditor.onDidChangeModelContent(() => {
        clearTimeout(previewTimer);
        previewTimer = setTimeout(() => refreshPreview(), 1500);
        document.getElementById('btnSalvar').disabled = !monacoEditor.getValue().trim();
    });
});

// ── Helpers ──────────────────────────────────────────────────
function setCols(n, btn) {
    document.getElementById('colunas').value = n;
    document.querySelectorAll('.col-btn').forEach(b => {
        b.className = b.dataset.cols == n
            ? 'btn btn-xs btn-primary mb-0 flex-1 col-btn'
            : 'btn btn-xs btn-outline-secondary mb-0 flex-1 col-btn';
    });
}

// ── Gerar com IA ─────────────────────────────────────────────
async function gerarTema() {
    const btn = document.getElementById('btnGerar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Gerando com IA...';
    document.getElementById('editorStatus').textContent = '⏳ Aguardando resposta da IA...';
    document.getElementById('editorStatus').style.color = '#6c757d';

    const body = {
        descricao:        document.getElementById('descricao').value,
        cor_primaria:     document.getElementById('corPrimaria').value,
        cor_secundaria:   document.getElementById('corSecundaria').value,
        colunas:          document.getElementById('colunas').value,
        estilo_card:      document.getElementById('estiloCard').value,
        mostrar_desconto: document.getElementById('mostrarDesconto').checked,
        area_ia_copy:     document.getElementById('aiaCopy').checked,
        altura:           document.getElementById('altura').value,
    };

    try {
        const res  = await fetch(generateUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify(body)
        });
        const data = await res.json();
        if (!res.ok || data.error) throw new Error(data.error || data.message || 'Erro da IA');

        if (monacoEditor) {
            monacoEditor.setValue(data.code);
            monaco.editor.setModelLanguage(monacoEditor.getModel(), 'html');
        }
        document.getElementById('editorStatus').textContent = '✓ Código gerado com sucesso!';
        document.getElementById('editorStatus').style.color = '#10b981';
        document.getElementById('btnSalvar').disabled = false;
        setTimeout(() => refreshPreview(), 300);

    } catch (e) {
        document.getElementById('editorStatus').textContent = '✗ ' + e.message;
        document.getElementById('editorStatus').style.color = '#ef4444';
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-robot me-2"></i> Gerar com IA';
}

// ── Preview ──────────────────────────────────────────────────
function refreshPreview() {
    const code = monacoEditor ? monacoEditor.getValue() : '';
    if (!code.trim()) return;

    document.getElementById('previewStatus').textContent = '⏳ Renderizando...';

    // Renderiza o Blade via endpoint de preview
    // Como não temos renderizador client-side do Blade, mostramos o HTML estático
    // substituindo variáveis Blade por placeholders legíveis para preview
    const previewHtml = code
        .replace(/@php[\s\S]*?@endphp/g, '') // Remove blocos php
        .replace(/@forelse\([^)]*\)/g, '<!-- LOOP PRODUTOS -->')
        .replace(/@empty/g, '<!-- EMPTY -->')
        .replace(/@endforelse/g, '<!-- /LOOP -->')
        .replace(/@foreach\([^)]*\)/g, '<!-- FOREACH -->')
        .replace(/@endforeach/g, '<!-- /FOREACH -->')
        .replace(/@if\([^)]*\)/g, '')
        .replace(/@endif/g, '')
        .replace(/@else/g, '')
        .replace(/\{\{[^}]*\$loja\['nome'\][^}]*\}\}/g, '🏪 Nome da Loja')
        .replace(/\{\{[^}]*\$loja\['telefone'\][^}]*\}\}/g, '📞 (11) 9900-0000')
        .replace(/\{\{[^}]*\$loja\['endereco'\][^}]*\}\}/g, '📍 Rua Exemplo, 123')
        .replace(/\{\{[^}]*\$loja\['cnpj'\][^}]*\}\}/g, 'CNPJ: 00.000.000/0001-00')
        .replace(/\{\{[^}]*\$headline[^}]*\}\}/g, '🤖 OFERTA INCRÍVEL DA SEMANA!')
        .replace(/\{\{[^}]*\$subtitulo[^}]*\}\}/g, '🤖 Preços que você não vai encontrar em outro lugar.')
        .replace(/\{\{[^}]*\$prod\['nome'\][^}]*\}\}/g, '📦 Produto Exemplo')
        .replace(/\{\{[^}]*\$prod\['preco_novo'\][^}]*\}\}/g, '9,99')
        .replace(/\{\{[^}]*\$prod\['preco_original'\][^}]*\}\}/g, '14,99')
        .replace(/\{\{[^}]*\$cols[^}]*\}\}/g, '3')
        .replace(/\{\{[^}]*\$rowH[^}]*\}\}/g, '280')
        .replace(/\{\{[^}]*\$imgMaxH[^}]*\}\}/g, '120')
        .replace(/\{\{[^}]*\$nomeFs[^}]*\}\}/g, '0.9')
        .replace(/\{\{[^}]*\$precoFs[^}]*\}\}/g, '1.6')
        .replace(/\{\{[^}]*\$pad[^}]*\}\}/g, '12')
        .replace(/\{\{[^}]*\$campaign[^}]*\}\}/g, '001')
        .replace(/\{\{[^}]*Carbon[^}]*\}\}/g, '01 de Março')
        .replace(/\{\{.*?\}\}/g, '···')   // restantes
        .replace(/@\w+[^>]*/g, '');       // demais diretivas

    const iframe = document.getElementById('previewIframe');
    iframe.srcdoc = previewHtml;
    document.getElementById('previewStatus').textContent = '✓ Preview atualizado (dados de amostra)';
}

// ── Salvar ───────────────────────────────────────────────────
async function salvarTema() {
    const name       = document.getElementById('themeName').value.trim();
    const identifier = document.getElementById('themeIdentifier').value.trim();
    const code       = monacoEditor ? monacoEditor.getValue().trim() : '';

    if (!name)       { alert('Informe o Nome do Tema'); return; }
    if (!identifier) { alert('Informe o Identifier'); return; }
    if (!/^[a-z0-9_]+$/.test(identifier)) {
        alert('Identifier deve ter apenas letras minúsculas, números e underscore'); return;
    }
    if (!code) { alert('Gere o código com IA primeiro'); return; }

    const btn = document.getElementById('btnSalvar');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

    try {
        const res  = await fetch(saveUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
            body: JSON.stringify({ name, identifier, code })
        });
        const data = await res.json();

        if (data.success) {
            if (confirm(`✓ ${data.message}\n\nDeseja ir para a lista de temas?`)) {
                window.location.href = data.theme_url;
            }
        } else {
            const errors = data.errors ? Object.values(data.errors).flat().join('\n') : (data.message || 'Erro ao salvar');
            alert('✗ ' + errors);
        }
    } catch (e) {
        alert('Erro: ' + e.message);
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save me-2"></i> Salvar Template';
}

// Auto-preencher identifier ao digitar nome
document.getElementById('themeName').addEventListener('input', function() {
    const identifier = this.value
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '') // remove acentos
        .replace(/[^a-z0-9\s_]/g, '')
        .trim()
        .replace(/\s+/g, '_');
    document.getElementById('themeIdentifier').value = identifier;
});
</script>
@endverbatim
@endpush
