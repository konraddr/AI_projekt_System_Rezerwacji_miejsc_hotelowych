(function () {
    const chatRoot = document.getElementById('hotel-chat');
    if (!chatRoot) {
        return;
    }

    const messagesEl = document.getElementById('chat-messages');
    const loadingEl = document.getElementById('chat-loading');
    const formEl = document.getElementById('chat-form');
    const errorEl = document.getElementById('chat-error');
    const submitBtn = document.getElementById('chat-submit');
    const pollUrl = chatRoot.dataset.pollUrl;
    const storeUrl = chatRoot.dataset.storeUrl;
    const pollInterval = parseInt(chatRoot.dataset.pollInterval || '60000', 10);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

    let lastMessageId = 0;
    let isPolling = false;

    function showError(message) {
        if (!errorEl) {
            return;
        }
        errorEl.textContent = message;
        errorEl.classList.remove('d-none');
    }

    function clearError() {
        if (!errorEl) {
            return;
        }
        errorEl.classList.add('d-none');
        errorEl.textContent = '';
    }

    function renderMessages(messages, replace) {
        if (!messagesEl) {
            return;
        }

        if (replace) {
            messagesEl.innerHTML = '';
        }

        if (loadingEl) {
            loadingEl.remove();
        }

        if (messages.length === 0 && messagesEl.children.length === 0) {
            messagesEl.innerHTML = '<p class="text-muted mb-0">Brak wiadomości. Napisz pierwszą wiadomość poniżej.</p>';
            return;
        }

        messages.forEach(function (message) {
            if (message.id <= lastMessageId && !replace) {
                return;
            }

            const existing = document.getElementById('chat-message-' + message.id);
            if (existing) {
                return;
            }

            const wrapper = document.createElement('div');
            wrapper.id = 'chat-message-' + message.id;
            wrapper.className = 'mb-3 ' + (message.is_mine ? 'text-end' : 'text-start');

            const bubble = document.createElement('div');
            bubble.className = 'd-inline-block p-2 rounded ' + (message.is_mine ? 'bg-primary text-white' : 'bg-light border');
            bubble.style.maxWidth = '85%';

            const meta = document.createElement('div');
            meta.className = 'small ' + (message.is_mine ? 'text-white-50' : 'text-muted');
            meta.textContent = message.sender_name + ' → ' + message.receiver_name + ' · ' + message.created_at;

            const body = document.createElement('div');
            body.className = 'mt-1';
            body.textContent = message.content;

            bubble.appendChild(meta);
            bubble.appendChild(body);
            wrapper.appendChild(bubble);
            messagesEl.appendChild(wrapper);

            if (message.id > lastMessageId) {
                lastMessageId = message.id;
            }
        });

        messagesEl.scrollTop = messagesEl.scrollHeight;
    }

    async function fetchMessages(replace) {
        if (isPolling) {
            return;
        }
        isPolling = true;

        try {
            const url = new URL(pollUrl, window.location.origin);
            if (!replace && lastMessageId > 0) {
                url.searchParams.set('after_id', String(lastMessageId));
            }

            const response = await fetch(url.toString(), {
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
            });

            if (!response.ok) {
                throw new Error('Nie udało się pobrać wiadomości.');
            }

            const data = await response.json();
            renderMessages(data.messages || [], replace);
            clearError();
        } catch (error) {
            showError(error.message || 'Błąd pobierania wiadomości.');
        } finally {
            isPolling = false;
        }
    }

    async function sendMessage(event) {
        event.preventDefault();
        clearError();

        const receiverId = formEl.receiver_id.value;
        const content = formEl.content.value.trim();

        if (!content) {
            showError('Wpisz treść wiadomości.');
            return;
        }

        submitBtn.disabled = true;

        try {
            const response = await fetch(storeUrl, {
                method: 'POST',
                headers: {
                    Accept: 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                credentials: 'same-origin',
                body: JSON.stringify({
                    receiver_id: parseInt(receiverId, 10),
                    content: content,
                }),
            });

            if (!response.ok) {
                const payload = await response.json().catch(function () {
                    return {};
                });
                const firstError = payload.errors
                    ? Object.values(payload.errors).flat()[0]
                    : null;
                throw new Error(firstError || 'Nie udało się wysłać wiadomości.');
            }

            const data = await response.json();
            if (data.message) {
                const emptyNote = messagesEl.querySelector('.text-muted');
                if (emptyNote && emptyNote.textContent.includes('Brak wiadomości')) {
                    messagesEl.innerHTML = '';
                }
                renderMessages([data.message], false);
            }

            formEl.content.value = '';
            clearError();
        } catch (error) {
            showError(error.message || 'Błąd wysyłania wiadomości.');
        } finally {
            submitBtn.disabled = false;
        }
    }

    formEl.addEventListener('submit', sendMessage);
    fetchMessages(true);
    setInterval(function () {
        fetchMessages(false);
    }, pollInterval);
})();
