// Service Worker for Web Push Notifications
self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    let data = {};
    if (event.data) {
        try {
            data = event.data.json();
        } catch (e) {
            data = {
                title: 'Pengingat Absensi',
                body: event.data.text(),
            };
        }
    }

    const title = data.title || 'Pengingat Absensi';
    const options = {
        body: data.body || 'Jangan lupa untuk melakukan absensi.',
        icon: data.icon || '/favicon.ico',
        badge: data.badge || '/favicon.ico',
        vibrate: [100, 50, 100],
        data: {
            url: data.action_url || data.url || '/employee/dashboard',
        },
        actions: data.actions || [
            {
                action: 'open',
                title: 'Absen Sekarang'
            }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(title, options)
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();

    const targetUrl = (event.notification.data && event.notification.data.url)
        ? event.notification.data.url
        : '/employee/dashboard';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function (clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if (client.url.includes(targetUrl) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});