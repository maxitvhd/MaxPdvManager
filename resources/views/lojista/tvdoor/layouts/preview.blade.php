<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prévia do Layout – TvDoor</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { background: #111; overflow: hidden; width: 100vw; height: 100vh; display: flex; align-items: center; justify-content: center; font-family: 'Segoe UI', sans-serif; }
        #preview-wrap { position: relative; background: #000; }
        #main-canvas { display: block; }
        #toolbar {
            position: fixed; top: 0; left: 0; right: 0;
            background: rgba(0,0,0,.75); backdrop-filter: blur(10px);
            display: flex; align-items: center; gap: 10px; padding: 8px 16px;
            font-size: .82rem; color: #ccc; z-index: 999;
        }
        #toolbar strong { color: #43e97b; }
        #close-btn {
            margin-left: auto; background: #ff416c; color: #fff; border: none;
            border-radius: 6px; padding: 4px 12px; cursor: pointer; font-size: .8rem;
        }
        #no-data {
            color: #fff; text-align: center; padding: 40px;
            font-size: 1.2rem; opacity: .6;
        }
    </style>
</head>
<body>
    <div id="toolbar">
        <strong>📺 Prévia do Layout</strong>
        <span style="opacity:.5">|</span>
        <span id="res-info">Carregando...</span>
        <button id="close-btn" onclick="window.close()">✕ Fechar</button>
    </div>

    <div id="preview-wrap">
        <canvas id="main-canvas"></canvas>
        <div id="overlay-container" style="position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;"></div>
    </div>

    <div id="no-data" style="display:none">
        <div style="font-size:3rem">📺</div>
        <p>Abra o Editor, clique em <strong>"Prévia"</strong> primeiro.</p>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
    <script>
    const raw = sessionStorage.getItem('tvdoor_preview');
    if (!raw) {
        document.getElementById('no-data').style.display = '';
        document.getElementById('preview-wrap').style.display = 'none';
    } else {
        const data = JSON.parse(raw);
        const W = data.width  || 1920;
        const H = data.height || 1080;

        document.getElementById('res-info').innerText = `${W} × ${H}`;

        const scale = Math.min(window.innerWidth / W, (window.innerHeight - 50) / H);
        const vw = Math.round(W * scale);
        const vh = Math.round(H * scale);

        const wrap = document.getElementById('preview-wrap');
        wrap.style.width  = vw + 'px';
        wrap.style.height = vh + 'px';
        wrap.style.marginTop = '42px';

        const fc = new fabric.Canvas('main-canvas', {
            width: W, height: H,
            preserveObjectStacking: true,
            selection: false,
            interactive: false,
        });

        fc.wrapperEl.style.transform = `scale(${scale})`;
        fc.wrapperEl.style.transformOrigin = 'top left';

        const toLoad = data.fabric || data;
        fc.loadFromJSON(toLoad, () => {
            // Lock all objects (read-only)
            fc.getObjects().forEach(o => {
                o.selectable = false;
                o.evented   = false;

                const d = o.data || {};

                // GIF overlay
                if (d.type === 'gif' && d.src) {
                    o.set('opacity', 0);
                    const img = document.createElement('img');
                    img.src    = d.src;
                    const bounds = o.getBoundingRect();
                    img.style.cssText = `
                        position:absolute;
                        left:${bounds.left * scale}px;
                        top:${bounds.top * scale}px;
                        width:${bounds.width * scale}px;
                        height:${bounds.height * scale}px;
                        object-fit:cover;
                        z-index:10;
                    `;
                    document.getElementById('overlay-container').appendChild(img);
                }

                // Video overlay
                if (d.type === 'video' && d.src) {
                    o.set('opacity', 0);
                    const vid = document.createElement('video');
                    vid.src = d.src;
                    vid.autoplay = true;
                    vid.muted    = true;
                    vid.loop     = true;
                    vid.setAttribute('playsinline', '');
                    const bounds = o.getBoundingRect();
                    vid.style.cssText = `
                        position:absolute;
                        left:${bounds.left * scale}px;
                        top:${bounds.top * scale}px;
                        width:${bounds.width * scale}px;
                        height:${bounds.height * scale}px;
                        object-fit:cover;
                        z-index:10;
                    `;
                    document.getElementById('overlay-container').appendChild(vid);
                    vid.play().catch(() => {});
                }
            });

            fc.renderAll();

            // Relógio
            setInterval(() => {
                fc.getObjects().forEach(o => {
                    if (o.data && o.data.type === 'clock') {
                        o.set('text', new Date().toLocaleTimeString('pt-BR'));
                    }
                });
                fc.renderAll();
            }, 1000);
        });
    }
    </script>
</body>
</html>
