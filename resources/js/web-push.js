function urlBase64ToUint8Array(base64String) {
    const padding = '='.repeat((4 - (base64String.length % 4)) % 4);
    const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
    const rawData = window.atob(base64);

    return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

function isValidVapidPublicKey(key) {
    if (!key || typeof key !== 'string') {
        return false;
    }

    try {
        return urlBase64ToUint8Array(key).length === 65;
    } catch (error) {
        return false;
    }
}

function getConfig() {
    const meta = document.querySelector('meta[name="webpush-config"]');

    if (!meta) {
        return null;
    }

    return {
        vapidPublicKey: meta.dataset.vapidPublicKey?.trim(),
        subscribeUrl: meta.dataset.subscribeUrl,
        unsubscribeUrl: meta.dataset.unsubscribeUrl,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
    };
}

function mapPushError(error) {
    const message = error?.message || '';

    if (!window.isSecureContext) {
        return 'Powiadomienia push działają tylko na HTTPS lub localhost (nie na adresie IP w sieci lokalnej).';
    }

    if (message.includes('push service error')) {
        return 'Błąd usługi push przeglądarki. Spróbuj Chrome/Edge, wyłącz blokadę FCM (Brave: Ustawienia → Prywatność → Google push), odśwież stronę i kliknij ponownie.';
    }

    if (error?.name === 'NotAllowedError') {
        return 'Przeglądarka zablokowała powiadomienia. Odblokuj je w ustawieniach witryny.';
    }

    return message || 'Nie udało się włączyć powiadomień push.';
}

async function getServiceWorkerRegistration() {
    if (!('serviceWorker' in navigator)) {
        return null;
    }

    await navigator.serviceWorker.register('/sw.js', { scope: '/' });

    return navigator.serviceWorker.ready;
}

async function getActiveSubscription() {
    const registration = await getServiceWorkerRegistration();

    if (!registration) {
        return null;
    }

    return registration.pushManager.getSubscription();
}

async function sendSubscription(url, subscription, csrfToken) {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify(subscription),
    });

    if (!response.ok) {
        throw new Error('Nie udało się zapisać subskrypcji push.');
    }
}

function setPushUiState({ enabled, message, isError = false }) {
    const enableButton = document.getElementById('enable-web-push');
    const disableButton = document.getElementById('disable-web-push');
    const statusEl = document.getElementById('web-push-status');

    if (enableButton && disableButton) {
        if (enabled) {
            enableButton.classList.add('d-none');
            disableButton.classList.remove('d-none');
        } else {
            enableButton.classList.remove('d-none');
            disableButton.classList.add('d-none');
        }
    }

    if (statusEl && message) {
        statusEl.textContent = message;
        statusEl.classList.toggle('text-danger', isError);
        statusEl.classList.toggle('text-muted', !isError);
    }
}

export async function syncPushSubscriptionWithServer() {
    const config = getConfig();

    if (!config || Notification.permission !== 'granted') {
        return;
    }

    const subscription = await getActiveSubscription();

    if (!subscription) {
        return;
    }

    try {
        await sendSubscription(config.subscribeUrl, subscription.toJSON(), config.csrfToken);
    } catch (error) {
        console.warn('Web Push sync failed:', error);
    }
}

export async function syncPushUiState() {
    const enableButton = document.getElementById('enable-web-push');
    const disableButton = document.getElementById('disable-web-push');

    if (!enableButton && !disableButton) {
        return;
    }

    if (!('Notification' in window)) {
        setPushUiState({
            enabled: false,
            message: 'Twoja przeglądarka nie obsługuje powiadomień push.',
            isError: true,
        });
        enableButton?.classList.add('d-none');
        disableButton?.classList.add('d-none');

        return;
    }

    if (Notification.permission === 'denied') {
        setPushUiState({
            enabled: false,
            message: 'Powiadomienia są zablokowane w przeglądarce. Odblokuj je w ustawieniach witryny.',
            isError: true,
        });
        enableButton?.classList.add('d-none');
        disableButton?.classList.add('d-none');

        return;
    }

    const subscription = await getActiveSubscription();
    const isEnabled = Notification.permission === 'granted' && subscription !== null;

    if (isEnabled) {
        setPushUiState({
            enabled: true,
            message: 'Powiadomienia push są włączone.',
        });
        await syncPushSubscriptionWithServer();

        return;
    }

    setPushUiState({
        enabled: false,
        message: Notification.permission === 'granted'
            ? 'Możesz włączyć powiadomienia push na tym urządzeniu.'
            : 'Otrzymuj alerty o rezerwacjach i wiadomościach na pulpicie, nawet gdy karta jest w tle.',
    });
}

export async function enableWebPush() {
    const config = getConfig();

    if (!config?.vapidPublicKey) {
        throw new Error('Web Push nie jest skonfigurowany na serwerze.');
    }

    if (!isValidVapidPublicKey(config.vapidPublicKey)) {
        throw new Error('Nieprawidłowy klucz VAPID na serwerze. Uruchom ponownie: php artisan webpush:vapid --force');
    }

    if (!('serviceWorker' in navigator) || !('PushManager' in window)) {
        throw new Error('Twoja przeglądarka nie obsługuje powiadomień push.');
    }

    if (!window.isSecureContext) {
        throw new Error(mapPushError({ message: 'insecure context' }));
    }

    const permission = await Notification.requestPermission();

    if (permission !== 'granted') {
        throw new Error('Brak zgody na powiadomienia w przeglądarce.');
    }

    const registration = await getServiceWorkerRegistration();
    let subscription = await registration.pushManager.getSubscription();

    if (!subscription) {
        try {
            subscription = await registration.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(config.vapidPublicKey),
            });
        } catch (error) {
            throw new Error(mapPushError(error));
        }
    }

    await sendSubscription(config.subscribeUrl, subscription.toJSON(), config.csrfToken);

    return subscription;
}

export async function disableWebPush() {
    const config = getConfig();

    if (!config) {
        return;
    }

    const registration = await navigator.serviceWorker.getRegistration('/sw.js');
    const subscription = await registration?.pushManager.getSubscription();

    if (!subscription) {
        return;
    }

    await fetch(config.unsubscribeUrl, {
        method: 'DELETE',
        headers: {
            Accept: 'application/json',
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': config.csrfToken,
            'X-Requested-With': 'XMLHttpRequest',
        },
        credentials: 'same-origin',
        body: JSON.stringify({ endpoint: subscription.endpoint }),
    });

    await subscription.unsubscribe();
}

export function bindWebPushUi() {
    const enableButton = document.getElementById('enable-web-push');
    const disableButton = document.getElementById('disable-web-push');

    enableButton?.addEventListener('click', async () => {
        try {
            await enableWebPush();
            setPushUiState({
                enabled: true,
                message: 'Powiadomienia push są włączone.',
            });
        } catch (error) {
            setPushUiState({
                enabled: false,
                message: mapPushError(error),
                isError: true,
            });
        }
    });

    disableButton?.addEventListener('click', async () => {
        try {
            await disableWebPush();
            setPushUiState({
                enabled: false,
                message: 'Powiadomienia push są wyłączone.',
            });
        } catch (error) {
            setPushUiState({
                enabled: true,
                message: error.message || 'Nie udało się wyłączyć powiadomień push.',
                isError: true,
            });
        }
    });

    syncPushUiState();
}
