/**
 * Sistem Absensi - Service Worker
 * Strategy:
 * - Static assets (icons, build): Cache First
 * - HTML pages: Network First with offline fallback
 * - Attendance POST endpoints: Network Only + IndexedDB queue
 * - Offline fallback: /offline page
 */

const CACHE_NAME = 'absensi-v2';
const OFFLINE_URL = '/offline';

// Hanya pre-cache static assets yang pasti ada (tidak perlu login)
const PRECACHE_ASSETS = [
    '/offline',
    '/manifest.json',
    '/icons/icon-72x72.png',
    '/icons/icon-96x96.png',
    '/icons/icon-128x128.png',
    '/icons/icon-144x144.png',
    '/icons/icon-152x152.png',
    '/icons/icon-192x192.png',
    '/icons/icon-384x384.png',
    '/icons/icon-512x512.png',
];

// ============================================
// IndexedDB untuk offline attendance queue
// ============================================

const DB_NAME = 'AbsensiDB';
const DB_VERSION = 1;
const STORE_NAME = 'attendance_queue';

function initDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains(STORE_NAME)) {
                db.createObjectStore(STORE_NAME, { keyPath: 'id', autoIncrement: true });
            }
        };
    });
}

async function saveToQueue(data) {
    try {
        const db = await initDB();
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        return new Promise((resolve, reject) => {
            const request = store.add({ timestamp: new Date().toISOString(), data, retries: 0 });
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    } catch (error) {
        console.error('[SW] Failed to save to queue:', error);
    }
}

async function getQueue() {
    try {
        const db = await initDB();
        const tx = db.transaction(STORE_NAME, 'readonly');
        const store = tx.objectStore(STORE_NAME);
        return new Promise((resolve, reject) => {
            const request = store.getAll();
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    } catch (error) {
        return [];
    }
}

async function clearQueue(ids) {
    try {
        const db = await initDB();
        const tx = db.transaction(STORE_NAME, 'readwrite');
        const store = tx.objectStore(STORE_NAME);
        ids.forEach(id => store.delete(id));
    } catch (error) {
        console.error('[SW] Failed to clear queue:', error);
    }
}

async function syncQueue() {
    const queue = await getQueue();
    if (queue.length === 0) return;
    console.log(`[SW] Syncing ${queue.length} attendance records...`);

    const successIds = [];
    for (const item of queue) {
        try {
            const response = await fetch('/employee/attendance/check-in', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(item.data),
            });
            if (response.ok) {
                successIds.push(item.id);
            }
        } catch (error) {
            console.error('[SW] Sync failed for item:', item.id);
        }
    }

    if (successIds.length > 0) {
        await clearQueue(successIds);
        self.clients.matchAll().then(clients => {
            clients.forEach(client => {
                client.postMessage({ type: 'SYNC_COMPLETE', count: successIds.length });
            });
        });
    }
}

// ============================================
// INSTALL — pre-cache only safe static assets
// ============================================

self.addEventListener('install', (event) => {
    console.log('[SW] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then((cache) => {
            // addAll akan gagal jika salah satu URL error.
            // Gunakan individual add untuk lebih robust.
            return Promise.allSettled(
                PRECACHE_ASSETS.map(url => cache.add(url).catch(err => {
                    console.warn('[SW] Failed to cache:', url, err.message);
                }))
            );
        }).then(() => {
            console.log('[SW] Install complete, skipping waiting...');
            return self.skipWaiting();
        })
    );
});

// ============================================
// ACTIVATE — cleanup old caches
// ============================================

self.addEventListener('activate', (event) => {
    console.log('[SW] Activating...');
    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== CACHE_NAME)
                    .map(name => {
                        console.log('[SW] Deleting old cache:', name);
                        return caches.delete(name);
                    })
            );
        }).then(() => {
            console.log('[SW] Activation complete');
            return self.clients.claim();
        })
    );
});

// ============================================
// FETCH — request routing
// ============================================

self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests (forms, API calls etc) — biarkan browser handle langsung
    if (request.method !== 'GET') return;

    // Skip cross-origin requests (CDN, fonts Google, dll)
    if (url.origin !== location.origin) return;

    // Skip Laravel admin routes — selalu network
    if (url.pathname.startsWith('/admin')) return;

    // Skip auth routes
    if (url.pathname.startsWith('/login') || url.pathname.startsWith('/logout')) return;

    // ── Static assets: Cache First ──────────────────
    if (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/icons/') ||
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/fonts/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.match(/\.(css|js|woff|woff2|ttf|eot|svg|ico)$/)
    ) {
        event.respondWith(
            caches.match(request).then((cached) => {
                if (cached) return cached;
                return fetch(request).then((response) => {
                    if (response && response.status === 200) {
                        const clone = response.clone();
                        caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                    }
                    return response;
                }).catch(() => {
                    // Aset statis tidak ada: biarkan saja
                    return new Response('', { status: 404 });
                });
            })
        );
        return;
    }

    // ── Offline page: Cache First ────────────────────
    if (url.pathname === '/offline') {
        event.respondWith(
            caches.match(request).then(cached => cached || fetch(request))
        );
        return;
    }

    // ── HTML Pages (employee): Network First ─────────
    if (request.headers.get('accept')?.includes('text/html')) {
        event.respondWith(
            fetch(request).then((response) => {
                if (response && response.status === 200) {
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => cache.put(request, clone));
                }
                return response;
            }).catch(() => {
                // Network failed: coba cache, fallback ke offline
                return caches.match(request).then(cached => {
                    return cached || caches.match(OFFLINE_URL);
                });
            })
        );
        return;
    }

    // ── Default: Network First ───────────────────────
    event.respondWith(
        fetch(request).catch(() => {
            return caches.match(request).then(cached => {
                return cached || caches.match(OFFLINE_URL);
            });
        })
    );
});

// ============================================
// BACKGROUND SYNC
// ============================================

self.addEventListener('sync', (event) => {
    if (event.tag === 'sync-attendance') {
        console.log('[SW] Background sync triggered');
        event.waitUntil(syncQueue());
    }
});

// ============================================
// PUSH NOTIFICATIONS
// ============================================

self.addEventListener('push', (event) => {
    if (!event.data) return;

    let data;
    try {
        data = event.data.json();
    } catch {
        data = { title: 'Sistem Absensi', body: event.data.text() };
    }

    const options = {
        body: data.body || '',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-72x72.png',
        tag: data.tag || 'default',
        requireInteraction: data.requireInteraction || false,
        data: { url: data.url || '/employee/attendance' },
        actions: data.actions || [],
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Sistem Absensi', options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/employee/attendance';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});

// ============================================
// MESSAGE HANDLING
// ============================================

self.addEventListener('message', (event) => {
    const { type } = event.data || {};

    if (type === 'SYNC_ATTENDANCE') {
        syncQueue()
            .then(() => event.ports[0]?.postMessage({ success: true }))
            .catch(err => event.ports[0]?.postMessage({ success: false, error: err.message }));
    }

    if (type === 'CLEAR_CACHE') {
        caches.delete(CACHE_NAME).then(() => {
            event.ports[0]?.postMessage({ success: true });
        });
    }

    if (type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});

console.log('[SW] Service Worker v2 loaded successfully');