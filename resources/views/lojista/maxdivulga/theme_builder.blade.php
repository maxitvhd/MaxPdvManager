@extends('layouts.user_type.auth')

@section('content')
    <div class="d-flex flex-column" style="height:calc(100vh - 60px); padding:0;">

        {{-- Top Bar --}}
        <div
            class="d-flex align-items-center justify-content-between px-3 py-2 bg-dark flex-shrink-0 border-bottom border-secondary">
            <div class="d-flex align-items-center gap-2">
                <i class="fas fa-magic text-warning fs-5"></i>
                <div>
                    <span class="fw-bold text-white">Builder Visual</span>
                    <span class="text-white-50 text-xs ms-2">— {{ $theme->name }}</span>
                </div>
                <span class="badge bg-danger text-xxs ms-1">Admin Only</span>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('lojista.maxdivulga.theme_editor', $theme) }}" class="btn btn-xs btn-outline-light mb-0">
                    <i class="fas fa-code me-1"></i> Editor de Código
                </a>
                <a href="{{ route('lojista.maxdivulga.themes') }}" class="btn btn-xs btn-outline-secondary mb-0">
                    <i class="fas fa-palette me-1"></i> Studio
                </a>
                <button id="btnSave" class="btn btn-sm btn-success mb-0" onclick="saveBuilder()">
                    <i class="fas fa-save me-1"></i> Salvar Tema
                </button>
            </div>
        </div>

        {{-- Alertas --}}
        <div id="alertBox" class="px-3 flex-shrink-0"></div>

        {{-- GrapeJS canvas --}}
        <div id="gjs" class="flex-grow-1"></div>

    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/grapesjs/dist/css/grapes.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .main-content .container-fluid,
        .main-content .row {
            padding: 0 !important;
            margin: 0 !important;
            max-width: 100% !important;
        }

        #gjs {
            width: 100%;
        }

        .gjs-logo {
            display: none;
        }

        .gjs-block-category .gjs-title {
            background: #374151;
            color: #f9fafb;
            font-size: 11px;
        }

        .gjs-block {
            border: 1px solid #374151 !important;
            color: #d1d5db;
            background: #1f2937;
        }

        .gjs-block:hover {
            background: #374151 !important;
            border-color: #6d28d9 !important;
        }

        .gjs-one-bg {
            background: #111827;
        }

        .gjs-two-bg {
            background: #1f2937;
        }

        [data-type="ai-area"] {
            border: 2px dashed #7c3aed !important;
            min-height: 50px;
            background: rgba(124, 58, 237, .06) !important;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #7c3aed;
            font-weight: bold;
            font-size: 12px;
            font-family: sans-serif;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://unpkg.com/grapesjs/dist/grapes.min.js"></script>
    {{-- Blade vars em script separado, antes do @verbatim --}}
    <script>
        window._BUILDER_CFG = {
            savePath: '{{ route("lojista.maxdivulga.theme_builder_save", $theme) }}',
            csrfToken: '{{ csrf_token() }}'
        };
    </script>
    @verbatim
        <script>
            const savePath = window._BUILDER_CFG.savePath;
            const csrfToken = window._BUILDER_CFG.csrfToken;

            const BLOCKS = {
                header: [
                    '<div data-gjs-type="header-section" style="background:linear-gradient(135deg,#003A7A,#0057B8);color:#FFD700;',
                    'display:flex;align-items:center;padding:20px 40px;border-bottom:6px solid #FFD700;min-height:240px;">',
                    '<div style="flex:0 0 auto;width:280px;overflow:hidden;display:flex;flex-direction:column;align-items:center;justify-content:center;padding-right:20px;">',
                    '<p style="color:#FFD700;font-weight:700;font-size:1.2rem;text-align:center;">[[LOJA_LOGO]]</p>',
                    '<div style="font-size:0.85rem;color:#FFD700;margin-top:8px;text-transform:uppercase;font-weight:700;">Oferta de tirar o fôlego!</div>',
                    '</div>',
                    '<div style="flex:1;padding-left:20px;">',
                    '<div style="color:#fff;font-weight:900;font-size:5.5rem;line-height:.9;text-transform:uppercase;text-shadow:3px 3px 0 #003A7A;">OFERTA</div>',
                    '<div style="font-size:3rem;font-weight:900;color:#FFD700;text-transform:uppercase;text-shadow:3px 3px 0 #003A7A;margin-bottom:12px;">ESPECIAL</div>',
                    '<div style="font-size:1.1rem;color:#fff;font-weight:700;background:rgba(0,0,0,.3);padding:7px 20px;border-radius:40px;border:2px solid #FFD700;display:inline-block;">VÁLIDO ATÉ: [[DATA_VALIDADE]]</div>',
                    '</div>',
                    '</div>'
                ].join(''),

                copyArea: [
                    '<div data-gjs-type="copy-section" style="background:linear-gradient(to bottom,#FFD700,#ffca00);color:#003A7A;',
                    'padding:18px 36px;text-align:center;border-bottom:3px solid #FFC107;">',
                    '<div style="font-size:2.2rem;font-weight:900;text-transform:uppercase;line-height:1.05;">[[AI_HEADLINE]]</div>',
                    '<div style="font-size:1.2rem;font-weight:800;margin-top:5px;">[[AI_SUBTITLE]]</div>',
                    '</div>'
                ].join(''),

                productGrid: [
                    '<div data-gjs-type="product-grid" style="display:grid;grid-template-columns:repeat([[GRID_COLS]],1fr);',
                    'gap:10px;padding:14px 18px;background:#EFF4FF;flex:1;">',
                    '[[PRODUCT_LOOP_START]]',
                    '<div data-gjs-type="product-card" style="background:#fff;border:2px solid #0057B8;border-radius:12px;',
                    'overflow:hidden;position:relative;display:flex;flex-direction:column;align-items:center;',
                    'padding:12px;box-shadow:0 5px 14px rgba(0,87,184,.12);">',
                    '<div style="position:absolute;top:0;right:0;background:linear-gradient(135deg,#0057B8,#003A7A);',
                    'color:#fff;font-size:.7rem;font-weight:900;padding:4px 10px;border-radius:0 11px 0 8px;">OFERTA</div>',
                    '<div style="width:100%;flex:1;display:flex;align-items:center;justify-content:center;padding:8px;">',
                    '[[PROD_IMAGEM]]',
                    '</div>',
                    '<div style="font-size:.9rem;font-weight:800;text-align:center;text-transform:uppercase;margin-bottom:4px;">[[PROD_NOME]]</div>',
                    '<div style="font-size:.8rem;color:#888;text-decoration:line-through;margin-bottom:3px;">de R$ [[PROD_PRECO_DE]]</div>',
                    '<div style="background:linear-gradient(to bottom,#0057B8,#003A7A);border:2px solid #FFD700;',
                    'color:#fff;font-weight:900;font-size:1.5rem;text-align:center;padding:4px 8px;border-radius:8px;width:100%;white-space:nowrap;">R$ [[PROD_PRECO]]</div>',
                    '</div>',
                    '[[PRODUCT_LOOP_END]]',
                    '</div>'
                ].join(''),

                footer: [
                    '<div data-gjs-type="footer-section" style="background:linear-gradient(to right,#003A7A,#0057B8,#003A7A);',
                    'color:#fff;padding:22px 40px;text-align:center;border-top:5px solid #FFD700;">',
                    '<div style="font-size:1.8rem;font-weight:900;text-transform:uppercase;text-shadow:2px 2px 4px rgba(0,0,0,.4);">[[LOJA_NOME]]</div>',
                    '<div style="font-size:1.1rem;font-weight:700;margin-top:6px;">📍 [[LOJA_ENDE]] | 📞 [[LOJA_FONE]]</div>',
                    '<div style="opacity:.8;font-size:.9rem;margin-top:4px;">[[LOJA_CNPJ]]</div>',
                    '</div>'
                ].join(''),

                productCard: [
                    '<div data-gjs-type="product-card" style="background:#fff;border:2px solid #FFD700;',
                    'border-radius:12px;overflow:hidden;position:relative;display:flex;flex-direction:column;align-items:center;padding:12px;">',
                    '<div style="position:absolute;top:0;right:0;background:linear-gradient(135deg,#E60012,#A3000D);',
                    'color:#fff;font-size:.7rem;font-weight:900;padding:4px 10px;border-radius:0 11px 0 8px;">OFERTA</div>',
                    '<div style="width:100%;min-height:100px;display:flex;align-items:center;justify-content:center;padding:8px;">',
                    '[[PROD_IMAGEM]]',
                    '</div>',
                    '<div style="font-size:.9rem;font-weight:800;text-align:center;text-transform:uppercase;margin-bottom:4px;">[[PROD_NOME]]</div>',
                    '<div style="font-size:.8rem;color:#888;text-decoration:line-through;margin-bottom:3px;">de R$ [[PROD_PRECO_DE]]</div>',
                    '<div style="background:linear-gradient(to bottom,#FFD700,#FFC107);border:2px solid #E60012;',
                    'color:#E60012;font-weight:900;font-size:1.5rem;text-align:center;padding:4px 8px;border-radius:8px;width:100%;">R$ [[PROD_PRECO]]</div>',
                    '</div>'
                ].join(''),

                aiHeadline: '<div data-type="ai-area" style="min-height:60px;border:2px dashed #7c3aed;background:rgba(124,58,237,.06);display:flex;align-items:center;justify-content:center;padding:10px;color:#7c3aed;font-weight:bold;font-family:sans-serif;">🤖 ÁREA IA — [[AI_HEADLINE]]</div>',

                aiSubtitle: '<div data-type="ai-area" style="min-height:40px;border:2px dashed #7c3aed;background:rgba(124,58,237,.06);display:flex;align-items:center;justify-content:center;padding:8px;color:#7c3aed;font-family:sans-serif;">🤖 Subtítulo IA — [[AI_SUBTITLE]]</div>',

                icon: '<span style="font-size:2rem;color:#FFD700;"><i class="fas fa-tag"></i></span>',

                divider: '<div style="height:4px;background:linear-gradient(to right,transparent,#FFD700,transparent);margin:16px 0;"></div>',
            };

            // Template inicial completo da página
            const INITIAL_TEMPLATE = [
                '<div style="font-family:\'Roboto Condensed\',Arial,sans-serif;width:1080px;height:1920px;overflow:hidden;',
                'display:flex;flex-direction:column;background:#0057B8;">',
                BLOCKS.header,
                '<div style="background:#f0f4ff;margin:0 20px;flex:1;display:flex;flex-direction:column;border-radius:18px 18px 0 0;overflow:hidden;">',
                BLOCKS.copyArea,
                BLOCKS.productGrid,
                '</div>',
                BLOCKS.footer,
                '<div style="background:#003A7A;color:rgba(255,255,255,.6);font-size:.76rem;padding:10px 28px;',
                'display:flex;justify-content:space-between;text-transform:uppercase;">',
                '<span>Campanha Nº [[CAMPAIGN_ID]]</span><span>Powered by MaxCheckout</span>',
                '</div>',
                '</div>'
            ].join('');

            // ════════════════════════════════════════════════════════
            //  GrapeJS
            // ════════════════════════════════════════════════════════
            const editor = grapesjs.init({
                container: '#gjs',
                height: '100%',
                width: 'auto',
                fromElement: false,
                storageManager: false,
                canvas: {
                    styles: [
                        'https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700;900&display=swap',
                        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css'
                    ]
                },
                blockManager: {
                    blocks: [
                        { id: 'block-header', label: '📋 Header', category: '🏗️ Estrutura', content: BLOCKS.header },
                        { id: 'block-copy', label: '✏️ Área Copy (IA)', category: '🏗️ Estrutura', content: BLOCKS.copyArea },
                        { id: 'block-grid', label: '📦 Grid Produtos', category: '🏗️ Estrutura', content: BLOCKS.productGrid },
                        { id: 'block-footer', label: '🏪 Rodapé', category: '🏗️ Estrutura', content: BLOCKS.footer },
                        { id: 'block-card', label: '🃏 Card de Produto', category: '📦 Componentes', content: BLOCKS.productCard },
                        { id: 'block-ai-headline', label: '🤖 Título IA', category: '📦 Componentes', content: BLOCKS.aiHeadline },
                        { id: 'block-ai-subtitle', label: '🤖 Subtítulo IA', category: '📦 Componentes', content: BLOCKS.aiSubtitle },
                        { id: 'block-icon', label: '⭐ Ícone', category: '📦 Componentes', content: BLOCKS.icon },
                        { id: 'block-divider', label: '〰️ Divisor', category: '📦 Componentes', content: BLOCKS.divider },
                    ]
                },
                deviceManager: {
                    devices: [
                        { name: 'Folheto 1080px', width: '1080px', widthMedia: '' },
                        { name: 'Preview 50%', width: '540px' },
                    ]
                },
                panels: { defaults: [] },
                styleManager: {
                    sectors: [
                        { name: 'Dimensões', open: false, properties: ['width', 'min-height', 'margin', 'padding', 'border-radius'] },
                        { name: 'Tipografia', open: false, properties: ['font-family', 'font-size', 'font-weight', 'color', 'text-align', 'text-transform', 'line-height'] },
                        { name: 'Background', open: false, properties: ['background-color', 'background'] },
                        { name: 'Borda', open: false, properties: ['border', 'border-width', 'border-style', 'border-color'] },
                        { name: 'Flex', open: false, properties: ['display', 'flex-direction', 'align-items', 'justify-content', 'gap', 'flex-wrap'] },
                        { name: 'Efeitos', open: false, properties: ['opacity', 'box-shadow', 'overflow'] },
                    ]
                },
            });

            // Carrega template inicial
            setTimeout(() => editor.setComponents(INITIAL_TEMPLATE), 300);

            // ── Salvar ─────────────────────────────────────────────
            async function saveBuilder() {
                const btn = document.getElementById('btnSave');
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Salvando...';

                const html = editor.getHtml();
                const css = editor.getCss();

                try {
                    const res = await fetch(savePath, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                        body: JSON.stringify({ html, css })
                    });
                    const data = await res.json();
                    const box = document.getElementById('alertBox');
                    const ok = data.success;
                    box.innerHTML = `<div class="alert alert-${ok ? 'success' : 'danger'} py-2 px-3 mb-0 rounded-0 fade show">
                    <i class="fas fa-${ok ? 'check' : 'times'} me-2"></i>${data.message}
                    <button class="btn-close float-end py-1" onclick="this.closest('.alert').remove()"></button>
                </div>`;
                    setTimeout(() => box.innerHTML = '', 5000);
                } catch (e) {
                    alert('Erro ao salvar: ' + e.message);
                }

                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-save me-1"></i> Salvar Tema';
            }
        </script>
    @endverbatim
@endpush