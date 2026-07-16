self.addEventListener('install', () => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    let data = { title: 'Thông báo', body: '', url: '/' };
    try {
        data = event.data ? event.data.json() : data;
    } catch (e) {
        data.body = event.data ? event.data.text() : '';
    }

    event.waitUntil(
        self.registration.showNotification(data.title || 'Thông báo', {
            body: data.body || '',
            icon: '/images/pwa-icon-192.png',
            badge: '/images/pwa-icon-192.png',
            data: { url: data.url || '/' },
        })
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data && event.notification.data.url ? event.notification.data.url : '/';

    event.waitUntil(
        self.clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url.includes(url) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (self.clients.openWindow) {
                return self.clients.openWindow(url);
            }
        })
    );
});
