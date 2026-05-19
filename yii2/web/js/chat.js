(function () {
    'use strict';

    const form = document.getElementById('chat-form');
    const input = document.getElementById('chat-input');
    const messages = document.getElementById('chat-messages');
    const sendBtn = document.getElementById('chat-send');
    const errorEl = document.getElementById('chat-error');

    if (!form || !window.chatConfig) {
        return;
    }

    const cfg = window.chatConfig;

    function showError(text) {
        if (!errorEl) {
            return;
        }
        errorEl.textContent = text;
        errorEl.hidden = !text;
    }

    function appendMessage(text, role) {
        const el = document.createElement('div');
        el.className = 'chat-msg chat-msg--' + role;
        const p = document.createElement('p');
        p.textContent = text;
        el.appendChild(p);
        messages.appendChild(el);
        messages.scrollTop = messages.scrollHeight;
        return el;
    }

    function setLoading(loading) {
        sendBtn.disabled = loading;
        input.disabled = loading;
    }

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        showError('');

        const text = input.value.trim();
        if (!text) {
            return;
        }

        appendMessage(text, 'user');
        input.value = '';
        setLoading(true);

        const pending = appendMessage('Думаю…', 'assistant');
        pending.classList.add('chat-msg--pending');

        const body = new URLSearchParams();
        body.append('message', text);
        body.append(cfg.csrfParam, cfg.csrfToken);

        try {
            const response = await fetch(cfg.messageUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: body.toString(),
                credentials: 'same-origin',
            });

            const data = await response.json().catch(function () {
                return { success: false, error: 'Некорректный ответ сервера.' };
            });

            pending.remove();

            if (!response.ok) {
                const msg = data.message || data.error || 'Ошибка ' + response.status;
                showError(msg);
                appendMessage('Не удалось отправить сообщение.', 'assistant');
                return;
            }

            if (data.success && data.reply) {
                appendMessage(data.reply, 'assistant');
            } else {
                showError(data.error || 'Неизвестная ошибка.');
                appendMessage(data.error || 'Попробуйте переформулировать вопрос.', 'assistant');
            }
        } catch (err) {
            pending.remove();
            showError('Нет связи с сервером.');
            appendMessage('Проверьте подключение и попробуйте снова.', 'assistant');
        } finally {
            setLoading(false);
            input.focus();
        }
    });
})();
