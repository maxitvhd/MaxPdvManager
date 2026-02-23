@extends('layouts.user_type.auth')

@section('content')
<div class="d-flex flex-column" style="height:calc(100vh - 60px); padding: 10px 14px 0;">

    {{-- Header --}}
    <div class="d-flex align-items-center justify-content-between mb-2 flex-shrink-0">
        <div>
            <h5 class="mb-0 fw-bold text-dark">
                <i class="fas fa-code text-warning me-2"></i>Editor — <span class="text-primary">{{ $theme->name }}</span>
            </h5>
            <p class="text-xxs text-muted mb-0">{{ resource_path('views/' . str_replace('.', '/', $theme->path)) }}.blade.php</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            {{-- Preview qty --}}
            <div class="d-flex align-items-center gap-1">
                <label class="text-xs text-muted mb-0">Preview</label>
                <input type="number" id="previewQty" value="6" min="1" max="20" class="form-control form-control-sm" style="width:58px;" oninput="debouncePreview()">
                <label class="text-xs text-muted mb-0">produtos</label>
            </div>
            {{-- Toggle preview --}}
            <button id="btnTogglePreview" class="btn btn-xs btn-outline-info mb-0" onclick="togglePreview()">
                <i class="fas fa-columns me-1"></i> Split
            </button>
            <a href="{{ route('lojista.maxdivulga.theme_builder', $theme) }}" class="btn btn-xs btn-outline-warning mb-0" target="_blank">
                <i class="fas fa-magic me-1"></i> Builder Visual
            </a>
            <a href="{{ route('lojista.maxdivulga.themes') }}" class="btn btn-xs btn-outline-secondary mb-0">
                <i class="fas fa-palette me-1"></i> Studio
            </a>
            <button id="btnSave" class="btn btn-sm btn-success mb-0" onclick="saveCode()">
                <i class="fas fa-save me-1"></i> Salvar
            </button>
        </div>
    </div>

    {{-- Alert --}}
    <div id="alertBox" class="flex-shrink-0" style="min-height:0"></div>

    {{-- Toolbar --}}
    <div class="d-flex align-items-center gap-3 mb-2 bg-dark rounded px-3 py-1 flex-shrink-0">
        <div class="d-flex gap-1 align-items-center">
            <label class="text-xs text-white-50 mb-0 me-1">Tema:</label>
            <select id="editorTheme" class="form-select form-select-sm bg-dark text-white border-secondary" style="width:auto;" onchange="setEditorTheme(this.value)">
                <option value="vs-dark" selected>🌙 Escuro</option>
                <option value="vs">☀️ Claro</option>
                <option value="hc-black">⚫ Alto Contraste</option>
            </select>
        </div>
        <div class="d-flex gap-1 align-items-center">
            <label class="text-xs text-white-50 mb-0 me-1">Fonte:</label>
            <select id="fontSize" class="form-select form-select-sm bg-dark text-white border-secondary" style="width:auto;" onchange="setFontSize(+this.value)">
                <option value="12">12px</option>
                <option value="13" selected>13px</option>
                <option value="14">14px</option>
                <option value="16">16px</option>
            </select>
        </div>
        <div class="ms-auto d-flex align-items-center gap-3">
            <span id="saveStatus" class="text-xs text-white-50"></span>
            <span id="previewStatus" class="text-xs text-white-50"></span>
            <span class="badge bg-warning text-dark text-xxs"><i class="fas fa-shield-alt me-1"></i>Backup automático</span>
        </div>
    </div>

    {{-- Split: editor + preview --}}
    <div class="d-flex gap-2 flex-grow-1 overflow-hidden">

        {{-- Monaco Editor --}}
        <div id="editorContainer" class="flex-grow-1 rounded overflow-hidden border border-dark shadow">
            <div id="monacoEditor" style="width:100%;height:100%;"></div>
        </div>

        {{-- Live Preview (iframe) --}}
        <div id="previewPanel" class="rounded overflow-hidden border border-secondary shadow d-flex flex-column" style="width:380px;min-width:280px;flex-shrink:0;display:none!important;">
            <div class="bg-dark text-white-50 text-xs px-3 py-1 d-flex align-items-center justify-content-between flex-shrink-0">
                <span><i class="fas fa-eye me-1"></i> Preview ao vivo</span>
                <span id="previewBadge" class="badge bg-secondary text-xs">●</span>
            </div>
            <div class="flex-grow-1 overflow-auto bg-gray-200" style="background:#ddd;">
                <div id="previewScaler" style="transform:scale(0.33);transform-origin:top left;width:1080px;">
                    <iframe id="previewIframe" style="width:1080px;height:1920px;border:none;display:block;" srcdoc="<p style='font:14px sans-serif;padding:30px;color:#999'>Aguardando mudanças...</p>"></iframe>
                </div>
            </div>
        </div>

    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
<script>
    let editor, previewVisible = false, debounceTimer = null;

    const themeId     = {{ $theme->id }};
    const savePath    = '{{ route("lojista.maxdivulga.theme_editor_save", $theme) }}';
    const renderPath  = '{{ route("lojista.maxdivulga.theme_render_code") }}';
    const csrfToken   = '{{ csrf_token() }}';
    const initialCode = @json($code);

    // Monaco config
    require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' } });
    require(['vs/editor/editor.main'], function () {
        monaco.languages.register({ id: 'blade' });
        monaco.languages.setMonarchTokensProvider('blade', {
            tokenizer: {
                root: [
                    [/@(if|else|elseif|endif|foreach|endforeach|forelse|endforelse|php|endphp|extends|section|endsection|yield|push|endpush|include)b/, 'keyword'],
                    [/\{\{.*?\}\}/, 'string'],
                    [/\{!!.*?!!\}/, 'string.escape'],
                    [/<\w+/, 'tag'],
                    [/\/\/.*$/, 'comment'],
                    [/\$[\w]+/, 'variable'],
                ],
            }
        });

        editor = monaco.editor.create(document.getElementById('monacoEditor'), {
            value: initialCode,
            language: 'blade',
            theme: 'vs-dark',
            fontSize: 13,
            minimap: { enabled: true },
            scrollBeyondLastLine: false,
            wordWrap: 'on',
            automaticLayout: true,
            tabSize: 4,
            folding: true,
            lineNumbers: 'on',
            smoothScrolling: true,
            cursorSmoothCaretAnimation: 'on',
        });

        editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => saveCode());
        editor.onDidChangeModelContent(() => {
            document.getElementById('saveStatus').textContent = '● Modificado';
            document.getElementById('saveStatus').className   = 'text-xs text-warning';
            debouncePreview();
        });
    });

    // ── Preview ao vivo ──────────────────────────────
    function debouncePreview() {
        if (!previewVisible) return;
        clearTimeout(debounceTimer);
        setBadge('🔄', 'secondary');
        debounceTimer = setTimeout(renderPreview, 1800);
    }

    async function renderPreview() {
        if (!editor || !previewVisible) return;
        setBadge('⏳', 'warning');
        const qty = parseInt(document.getElementById('previewQty').value) || 6;
        try {
            const res = await fetch(renderPath, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'text/html' },
                body: JSON.stringify({ code: editor.getValue(), theme_id: themeId, qty })
            });
            const html = await res.text();
            document.getElementById('previewIframe').srcdoc = html;
            setBadge('✓', 'success');
        } catch (e) {
            setBadge('✗', 'danger');
        }
    }

    function setBadge(icon, color) {
        const b = document.getElementById('previewBadge');
        b.textContent = icon;
        b.className   = `badge bg-${color} text-xs`;
    }

    // ── Toggle Split ────────────────────────────────
    function togglePreview() {
        previewVisible = !previewVisible;
        const panel = document.getElementById('previewPanel');
        panel.style.display = previewVisible ? 'flex' : 'none';
        document.getElementById('btnTogglePreview').innerHTML = previewVisible
            ? '<i class="fas fa-columns me-1"></i> Ocultar Preview'
            : '<i class="fas fa-columns me-1"></i> Split';
        if (previewVisible) renderPreview();
        if (editor) editor.layout();
    }

    // ── Salvar ───────────────────────────────────────
    function showAlert(msg, type = 'success') {
        const box = document.getElementById('alertBox');
        box.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show py-2 px-3 mb-2" role="alert">
            ${msg}<button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button></div>`;
        setTimeout(() => box.innerHTML = '', 4000);
    }

    async function saveCode() {
        if (!editor) return;
        const btn = document.getElementById('btnSave');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>';
        try {
            const res  = await fetch(savePath, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                body: JSON.stringify({ code: editor.getValue() })
            });
            const data = await res.json();
            if (data.success) {
                showAlert('<i class="fas fa-check me-2"></i>' + data.message, 'success');
                document.getElementById('saveStatus').textContent = '✓ Salvo';
                document.getElementById('saveStatus').className   = 'text-xs text-success';
            } else {
                showAlert('<i class="fas fa-times me-2"></i>' + data.message, 'danger');
            }
        } catch (e) { showAlert('Erro: ' + e.message, 'danger'); }
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar';
    }

    function setEditorTheme(t) { if (editor) monaco.editor.setTheme(t); }
    function setFontSize(s)    { if (editor) editor.updateOptions({ fontSize: s }); }
</script>

<style>
    .main-content .container-fluid { padding-left: 0 !important; padding-right: 0 !important; }
    #previewPanel { flex-direction: column; }
</style>
@endpush