import 'bootstrap';
import { bindWebPushUi } from './web-push';
import { initNotificationBadgePolling } from './notification-badge';

document.addEventListener('DOMContentLoaded', () => {
    bindWebPushUi();
    initNotificationBadgePolling();
});
