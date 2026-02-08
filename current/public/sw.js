// Service Worker — EMC Abastos Push Notifications
self.addEventListener('push', function(event) {
    if (!event.data) return;

    let data;
    try {
        data = event.data.json();
    } catch (e) {
        data = { title: 'EMC Abastos', body: event.data.text() };
    }

    const title = data.title || 'EMC Abastos';
    const options = {
        body: data.body || '',
        icon: '/images/logoiados.png',
        badge: '/images/logoiados.png',
        tag: data.tags ? `order-${data.tags.order_id}` : 'emc-notification',
        renotify: true,
        data: {
            url: data.url || '/ops/movil'
        }
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();

    const url = event.notification.data?.url || '/ops/movil';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            // Si ya hay una ventana abierta, enfocarla y navegar
            for (const client of clientList) {
                if (client.url.includes('/ops/') && 'focus' in client) {
                    client.focus();
                    client.navigate(url);
                    return;
                }
            }
            // Si no, abrir nueva ventana
            if (clients.openWindow) {
                return clients.openWindow(url);
            }
        })
    );
});

// Cache minimo para offline (solo el SW)
self.addEventListener('install', function(event) {
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    event.waitUntil(self.clients.claim());
});
