@extends('layouts.user_type.auth')

@section('content')
    <div class="container-fluid py-3 d-flex flex-column" style="height: calc(100vh - 80px);">

        {{-- Header --}}
        <div class="d-flex align-items-center justify-content-between mb-3 px-1 flex-shrink-0">
            <div>
                <h4 class="mb-0 fw-bold text-dark">
                    <i class="fas fa-code text-warning me-2"></i> Editor — {{ $theme->name }}
                </h4>
                <p class="text-xs text-muted mb-0">
                    {{ resource_path('views/' . str_replace('.', '/', $theme->path)) }}.blade.php</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('lojista.maxdivulga.themes') }}" class="btn btn-sm btn-outline-secondary mb-0">
                    <i class="fas fa-palette me-1"></i> Theme Studio
                </a>
                <button id="btnSave" class="btn btn-sm btn-success mb-0" onclick="saveCode()">
                    <i class="fas fa-save me-1"></i> Salvar
                </button>
                <button class="btn btn-sm btn-primary mb-0" onclick="openPreview()">
                    <i class="fas fa-eye me-1"></i> Preview
                </button>
            </div>
        </div>

        {{-- Alertas --}}
        <div id="alertBox" class="flex-shrink-0"></div>

        {{-- Toolbar --}}
        <div class="d-flex align-items-center gap-3 mb-2 px-1 flex-shrink-0">
            <div class="d-flex gap-1 align-items-center">
                <label class="text-xs font-weight-bold text-muted mb-0 me-1">Tema editor:</label>
                <select id="editorTheme" class="form-select form-select-sm" style="width:auto;"
                    onchange="setEditorTheme(this.value)">
                    <option value="vs-dark" selected>🌙 Escuro</option>
                    <option value="vs">☀️ Claro</option>
                    <option value="hc-black">⚫ Alto Contraste</option>
                </select>
            </div>
            <div class="d-flex gap-1 align-items-center">
                <label class="text-xs font-weight-bold text-muted mb-0 me-1">Tamanho:</label>
                <select id="fontSize" class="form-select form-select-sm" style="width:auto;"
                    onchange="setFontSize(+this.value)">
                    <option value="12">12px</option>
                    <option value="13" selected>13px</option>
                    <option value="14">14px</option>
                    <option value="16">16px</option>
                    <option value="18">18px</option>
                </select>
            </div>
            <div class="ms-auto d-flex align-items-center gap-2">
                <span id="saveStatus" class="text-xs text-muted"></span>
                <span class="badge bg-warning text-dark text-xxs">
                    <i class="fas fa-exclamation-triangle me-1"></i> Backup automático ao salvar
                </span>
            </div>
        </div>

        {{-- Monaco Editor --}}
        <div class="card border-0 shadow-sm flex-grow-1 overflow-hidden">
            <div id="monacoEditor" style="width:100%; height:100%;"></div>
        </div>

    </div>
@endsection

@push('scripts')
    {{-- Monaco Editor via CDN --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs/loader.min.js"></script>
    <script>
        let editor;
        const themeId = {{ $theme->id }};
        const savePath = '{{ route("lojista.maxdivulga.theme_editor_save", $theme) }}';
        const previewBase = '{{ route("lojista.maxdivulga.theme_preview") }}?theme_id={{ $theme->id }}&qty=9';
        const csrfToken = '{{ csrf_token() }}';

        // Código original passado do controller
        const originalCode = @json($code);

        require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.45.0/min/vs' } });

        require(['vs/editor/editor.main'], function () {
            // Registrar linguagem Blade (como HTML com PHP embutido)
            monaco.languages.register({ id: 'blade' });
            monaco.languages.setMonarchTokensProvider('blade', {
                tokenizer: {
                    root: [
                        [/@(if|else|elseif|endif|foreach|endforeach|forelse|endforelse|php|endphp|extends|section|endsection|yield|push|endpush|include)/, 'keyword'],
                        [/\{\{.*?\}\}/, 'string'],
                        [/\{!!.*?!!\}/, 'string.escape'],
                        [/<\?php/, 'keyword', '@phpBlock'],
                        [/<!--/, 'comment', '@comment'],
                            [/<\/?\w+/, 'tag'],
                            [/[a-z-]+="/, 'attribute.name', '@attribute'],
                            [/=/, 'operator'],
                        ],
                        phpBlock: [
                            [/\?>/, 'keyword', '@pop'],
                            [/\$[\w]+/, 'variable'],
                            [/"[^"]*"/, 'string'],
                            [/'[^']*'/, 'string'],
                            [/\/\/.*$/, 'comment'],
                            [/[0-9]+/, 'number'],
                        ],
                        comment: [
                            [/--> /, 'comment', '@pop'],
                            [/./, 'comment']
                        ],
                        attribute: [
                            [/"/, 'attribute.name', '@pop'],
                            [/[^"]+/, 'attribute.value'],
                        ]
                }
            });

            editor = monaco.editor.create(document.getElementById('monacoEditor'), {
                value: originalCode,
                language: 'blade',
                theme: 'vs-dark',
                fontSize: 13,
                minimap: { enabled: true },
                scrollBeyondLastLine: false,
                wordWrap: 'on',
                automaticLayout: true,
                formatOnPaste: true,
                tabSize: 4,
                insertSpaces: true,
                renderLineHighlight: 'all',
                smoothScrolling: true,
                cursorSmoothCaretAnimation: 'on',
                folding: true,
                lineNumbers: 'on',
            });

            // Atalho Ctrl+S para salvar
            editor.addCommand(monaco.KeyMod.CtrlCmd | monaco.KeyCode.KeyS, () => saveCode());

            // Indicador de modificação
            editor.onDidChangeModelContent(() => {
                document.getElementById('saveStatus').textContent = '● Modificado';
                document.getElementById('saveStatus').className = 'text-xs text-warning';
            });
        });

        function setEditorTheme(theme) {
            if (editor) monaco.editor.setTheme(theme);
        }

        function setFontSize(size) {
            if (editor) editor.updateOptions({ fontSize: size });
        }

        function showAlert(msg, type = 'success') {
            const box = document.getElementById('alertBox');
            box.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show py-2 px-3 mb-2" role="alert">
                ${msg}
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>`;
            setTimeout(() => { box.innerHTML = ''; }, 4000);
        }

        async function saveCode() {
            if (!editor) return;
            const btn = document.getElementById('btnSave');
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

            try {
                const res = await fetch(savePath, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ code: editor.getValue() })
                });

                const data = await res.json();

                if (data.success) {
                    showAlert('<i class="fas fa-check me-2"></i>' + data.message, 'success');
                    document.getElementById('saveStatus').textContent = '✓ Salvo';
                    document.getElementById('saveStatus').className = 'text-xs text-success';
                } else {
                    showAlert('<i class="fas fa-times me-2"></i>' + data.message, 'danger');
                }
            } catch (e) {
                showAlert('<i class="fas fa-times me-2"></i> Erro ao salvar: ' + e.message, 'danger');
            }

            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar';
        }

        function openPreview() {
            window.open(previewBase, '_blank');
        }
    </script>

    <style>
        /* Remove padding padrão do conteúdo principal para aproveitar a altura toda */
        .main-content>.container-fluid {
            padding-left: 12px;
            padding-right: 12px;
        }

        .monaco-editor {
            border-radius: 0.5rem;
        }
    </style>
@endpush