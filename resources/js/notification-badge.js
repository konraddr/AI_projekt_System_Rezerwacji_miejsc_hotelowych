export function initNotificationBadgePolling() {
    const badgeEl = document.getElementById('notifications-badge');
    const configEl = document.querySelector('meta[name="notification-badge-config"]');

    if (!badgeEl || !configEl) {
        return;
    }

    const pollUrl = configEl.dataset.pollUrl;
    const pollInterval = parseInt(configEl.dataset.pollInterval || '10000', 10);

    async function refreshBadge() {
        try {
            const response = await fetch(pollUrl, {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                return;
            }

            const data = await response.json();
            const count = Number(data.count) || 0;

            if (count > 0) {
                badgeEl.textContent = String(count);
                badgeEl.classList.remove('d-none');
            } else {
                badgeEl.classList.add('d-none');
            }
        } catch (error) {
            console.warn('Notification badge poll failed:', error);
        }
    }

    refreshBadge();
    setInterval(refreshBadge, pollInterval);
}
