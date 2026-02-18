
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- PWA Settings -->
    <meta name="theme-color" content="#000000">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="MaxVision">
    
    <!-- Caminhos atualizados para assets -->
    <link rel="manifest" href="/assets/manifest.json">
    <link rel="apple-touch-icon" href="/assets/icon-192x192.png">

    <title>MaxVision</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    
    <script>
      window.__INITIAL_DATA__ = {
        products: @json($produtos),
        loja: @json($loja),
        user: @json(auth()->user())
      };
    </script>
    
    <style>
        body { 
          font-family: 'Inter', sans-serif; 
          overflow: hidden; 
          overscroll-behavior: none; 
          -webkit-tap-highlight-color: transparent;
          background-color: #000;
        }
        .font-tech { font-family: 'Orbitron', sans-serif; }
        .scroll-container {
          overflow-y: auto;
          -webkit-overflow-scrolling: touch;
        }
        .scroll-container::-webkit-scrollbar {
          display: none;
        }
        input[type="number"]::-webkit-inner-spin-button, 
        input[type="number"]::-webkit-outer-spin-button { 
          -webkit-appearance: none; 
          margin: 0; 
        }
        @keyframes scan {
          0% { transform: translateY(0); opacity: 0; }
          50% { opacity: 1; }
          100% { transform: translateY(180px); opacity: 0; }
        }
        .animate-scan { animation: scan 2s cubic-bezier(0.4, 0, 0.2, 1) infinite; }
        
        #reader video {
            object-fit: contain !important; 
            width: 100% !important;
            height: 100% !important;
            border-radius: 20px;
        }
    </style>

<script type="importmap">
{
  "imports": {
    "react": "https://esm.sh/react@^19.2.3",
    "react-dom/": "https://esm.sh/react-dom@^19.2.3/",
    "react/": "https://esm.sh/react@^19.2.3/",
    "lucide-react": "https://esm.sh/lucide-react@^0.562.0",
    "html5-qrcode": "https://esm.sh/html5-qrcode@^2.3.8"
  }
}
</script>
  <script type="module" crossorigin src="/assets/index-V3H83UQ2.js"></script>
  <link rel="stylesheet" crossorigin href="/assets/index-CxKo13H9.css">
</head>
<body class="bg-black text-white antialiased">
    <div id="root"></div>
    <script>
      if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
          // O SW DEVE estar na raiz (/sw.js) para controlar o escopo '/'.
          // Se estiver em /assets/sw.js, ele não poderá controlar /app.
          navigator.serviceWorker.register('/sw.js')
            .then(registration => {
              console.log('SW registrado com sucesso:', registration.scope);
            })
            .catch(registrationError => {
              console.log('Falha ao registrar SW:', registrationError);
            });
        });
      }
    </script>
</body>
</html>
