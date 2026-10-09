// Service worker de El Billetazo.
// - Las páginas siempre se piden a la red (los datos del jugador deben estar al día y no quedan guardados en el teléfono).
// - Sin internet se muestra /offline.html.
// - Imágenes, íconos y archivos de /build se guardan en caché para que la app abra rápido.
const VERSION = 'billetazo-v1';
const CACHE_ESTATICO = `${VERSION}-estatico`;
const PRECARGA = ['/offline.html', '/icons/icon-192.png', '/images/billetazo.png'];

self.addEventListener('install', (event) => {
    event.waitUntil(caches.open(CACHE_ESTATICO).then((cache) => cache.addAll(PRECARGA)));
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((claves) => Promise.all(claves.filter((c) => !c.startsWith(VERSION)).map((c) => caches.delete(c))))
            .then(() => self.clients.claim())
    );
});

function esEstatico(url) {
    return url.pathname.startsWith('/build/')
        || url.pathname.startsWith('/icons/')
        || url.pathname.startsWith('/images/')
        || url.pathname === '/favicon.ico'
        || url.hostname === 'fonts.bunny.net';
}

self.addEventListener('fetch', (event) => {
    const { request } = event;
    if (request.method !== 'GET') return;

    const url = new URL(request.url);

    if (request.mode === 'navigate') {
        event.respondWith(fetch(request).catch(() => caches.match('/offline.html')));
        return;
    }

    if (esEstatico(url)) {
        event.respondWith(
            caches.match(request).then((enCache) => enCache || fetch(request).then((respuesta) => {
                if (respuesta.ok || respuesta.type === 'opaque') {
                    const copia = respuesta.clone();
                    caches.open(CACHE_ESTATICO).then((cache) => cache.put(request, copia));
                }
                return respuesta;
            }))
        );
    }
    // Todo lo demás (estado del sorteo en vivo, comprobantes, etc.) va directo a la red, sin caché.
});
