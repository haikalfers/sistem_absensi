/**
 * WebPush Client Helper
 * PT. Indobismar - Employee Portal
 */

(function () {
    'use strict';

    const VAPID_PUBLIC_KEY = window.VAPID_PUBLIC_KEY || '';

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    async function initWebPush() {
        if (!('serviceWorker' in navigator) || !('PushManager' in window) || !('Notification' in window)) {
            console.warn('[WebPush] Push Notifications not supported in this browser.');
            return;
        }

        if (!VAPID_PUBLIC_KEY) {
            console.warn('[WebPush] VAPID Public Key missing.');
            return;
        }

        try {
            const swReg = await navigator.serviceWorker.ready;

            if (Notification.permission === 'granted') {
                // Ensure subscription is active and synced to backend
                await subscribeUser(swReg);
            } else if (Notification.permission === 'default') {
                // Prompt user with a custom modal/banner if not dismissed recently
                showNotificationPromptModal(swReg);
            }
        } catch (err) {
            console.error('[WebPush] Initialization error:', err);
        }
    }

    async function subscribeUser(swReg) {
        try {
            let subscription = await swReg.pushManager.getSubscription();

            if (!subscription) {
                const applicationServerKey = urlBase64ToUint8Array(VAPID_PUBLIC_KEY);
                subscription = await swReg.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: applicationServerKey
                });
            }

            // Send subscription to Laravel backend
            const subJson = subscription.toJSON();
            const response = await fetch('/push-subscriptions', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
                },
                body: JSON.stringify(subJson)
            });

            if (response.ok) {
                console.log('[WebPush] Subscription synced successfully.');
            }
        } catch (err) {
            console.error('[WebPush] Failed to subscribe user:', err);
        }
    }

    function showNotificationPromptModal(swReg) {
        if (localStorage.getItem('webpush_prompt_dismissed_v1')) {
            return; // Don't annoy user repeatedly if dismissed
        }

        const modalHtml = `
        <div id="webpush-modal" style="position: fixed; inset: 0; z-index: 9999; display: flex; align-items: center; justify-content: center; padding: 1rem; background-color: rgba(0, 0, 0, 0.55); backdrop-filter: blur(4px);">
            <div style="background-color: #ffffff; border-radius: 1rem; padding: 1.5rem; max-width: 24rem; width: 100%; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); border: 1px solid #fee2e2; text-align: center;">
                <div style="width: 3.5rem; height: 3.5rem; background-color: #fee2e2; color: #dc2626; border-radius: 9999px; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem auto; box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05);">
                    <svg style="width: 1.75rem; height: 1.75rem;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1rem; font-weight: 800; color: #111827; margin-bottom: 0.25rem; font-family: sans-serif;">Aktifkan Pengingat Absen</h3>
                <p style="font-size: 0.75rem; color: #4b5563; margin-bottom: 1.25rem; line-height: 1.5; font-family: sans-serif;">
                    Dapatkan pengingat otomatis sebelum jam masuk (07.30 & 07.50) dan sebelum jam pulang (15.45) agar absensi Anda selalu tercatat tepat waktu.
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                    <button id="webpush-enable-btn" style="width: 100%; padding: 0.75rem 1rem; background-color: #991b1b; color: #ffffff !important; font-weight: 800; font-size: 0.8125rem; border-radius: 0.75rem; border: none; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(153, 27, 27, 0.4); transition: background-color 0.2s; font-family: sans-serif;" onmouseover="this.style.backgroundColor='#7f1d1d'" onmouseout="this.style.backgroundColor='#991b1b'">
                        Aktifkan Notifikasi
                    </button>
                    <button id="webpush-dismiss-btn" style="width: 100%; padding: 0.5rem 1rem; background-color: transparent; color: #6b7280; font-weight: 600; font-size: 0.75rem; border: none; cursor: pointer; transition: color 0.2s; font-family: sans-serif;" onmouseover="this.style.color='#111827'" onmouseout="this.style.color='#6b7280'">
                        Nanti Saja
                    </button>
                </div>
            </div>
        </div>
        `;

        document.body.insertAdjacentHTML('beforeend', modalHtml);

        document.getElementById('webpush-enable-btn')?.addEventListener('click', async () => {
            document.getElementById('webpush-modal')?.remove();
            const permission = await Notification.requestPermission();
            if (permission === 'granted') {
                await subscribeUser(swReg);
            }
        });

        document.getElementById('webpush-dismiss-btn')?.addEventListener('click', () => {
            document.getElementById('webpush-modal')?.remove();
            localStorage.setItem('webpush_prompt_dismissed_v1', '1');
        });
    }

    window.addEventListener('load', () => {
        initWebPush();
    });
})();
