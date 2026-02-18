
const CACHE_NAME = 'maxvision-v3';
// Tente colocar apenas arquivos que tem certeza que existem.
const urlsToCache = [
  '/assets/manifest.json',
  // '/app' removido da lista obrigatória de install para evitar falha se o usuário não estiver logado ou redirecionar
];

self.addEventListener('install', event => {
  self.skipWaiting();
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        // Usa map para tentar cachear cada um individualmente, evitando falha total
        return Promise.allSettled(
            urlsToCache.map(url => cache.add(url).catch(e => console.warn('Falha cache:', url)))
        );
      })
  );
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

self.addEventListener('fetch', event => {
  if (event.request.method !== 'GET') return;

  const url = new URL(event.request.url);

  // Estratégia Network First para a aplicação (/app) e dados (/produtos)
  if (url.pathname.startsWith('/app') || url.pathname.startsWith('/produtos')) {
     event.respondWith(
       fetch(event.request)
         .then(response => {
             // Opcional: Cachear a página app dinamicamente se sucesso
             return response;
         })
         .catch(() => {
             // Fallback offline se necessário
             return caches.match(event.request);
         })
     );
     return;
  }

  // Cache First para imagens e assets estáticos
  event.respondWith(
    caches.match(event.request)
      .then(response => {
        if (response) return response;
        return fetch(event.request).then(response => {
            // Cacheia dinamicamente novos assets acessados
            if(!response || response.status !== 200 || response.type !== 'basic') {
                return response;
            }
            const responseToCache = response.clone();
            caches.open(CACHE_NAME).then(cache => {
                cache.put(event.request, responseToCache);
            });
            return response;
        });
      })
  );
});
