self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open('v1').then((cache) => {
            return cache.addAll([
                '/',
                '/index.php',
                '/css/app.css',
                '/js/app.js',
                '/images/icons/icon-72x72.png',
                '/images/icons/icon-96x96.png',
                '/images/icons/icon-128x128.png',
                '/images/icons/icon-144x144.png',
                '/images/icons/icon-152x152.png',
                '/images/icons/icon-192x192.png',
                '/images/icons/icon-384x384.png',
                '/images/icons/icon-512x512.png',
            ]);
        })
    );
});

self.addEventListener('fetch', (event) => {
    event.respondWith(
        caches.match(event.request).then((response) => {
            return response || fetch(event.request);
        })
    );
});