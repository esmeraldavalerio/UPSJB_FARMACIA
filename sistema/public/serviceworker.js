// Nombre de la caché. Si se cambian los estilos o los scripts, subir la versión (pwa-v2, pwa-v3...)
// para que los navegadores descarguen los archivos nuevos.
var staticCacheName = "pwa-v1";

// Archivos propios del sistema que se guardan para usarlos sin internet
var filesToCache = [
    '/offline',
    '/css/bootstrap.css',
    '/css/custom.css',
    '/js/app.js',
    '/js/bootstrap.bundle.min.js',
    '/js/chart.umd.min.js',
    '/images/icons/icon-72x72.png',
    '/images/icons/icon-96x96.png',
    '/images/icons/icon-128x128.png',
    '/images/icons/icon-144x144.png',
    '/images/icons/icon-152x152.png',
    '/images/icons/icon-192x192.png',
    '/images/icons/icon-384x384.png',
    '/images/icons/icon-512x512.png',
];

// Al instalar se guardan los archivos en la caché
self.addEventListener("install", event => {
    self.skipWaiting();
    event.waitUntil(
        caches.open(staticCacheName)
            .then(cache => {
                return cache.addAll(filesToCache);
            })
    )
});

// Al activar se borran las cachés de versiones anteriores
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(cacheName => (cacheName.startsWith("pwa-")))
                    .filter(cacheName => (cacheName !== staticCacheName))
                    .map(cacheName => caches.delete(cacheName))
            );
        })
    );
});

// Cada petición se busca primero en la caché y, si no está, se pide al servidor
self.addEventListener("fetch", event => {
    // Solo se manejan las consultas GET; los formularios (POST, PUT, DELETE) van directo al servidor
    if (event.request.method !== 'GET') {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(response => {
                return response || fetch(event.request);
            })
            .catch(() => {
                // Sin internet: si se estaba abriendo una página, se muestra la página "Sin conexión"
                if (event.request.mode === 'navigate') {
                    return caches.match('/offline');
                }
            })
    )
});
