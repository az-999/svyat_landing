<?php

declare(strict_types=1);

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'Тест окружения';
$this->params['breadcrumbs'][] = $this->title;

$this->registerJs(<<<'JS'
(function () {
    function isActiveTelegramWebApp(webApp) {
        if (!webApp) {
            return false;
        }
        var initData = webApp.initData || '';
        var platform = webApp.platform || '';
        // Вне Telegram SDK может существовать с пустым initData и platform === 'unknown'.
        return initData.length > 0 || (platform !== '' && platform !== 'unknown');
    }

    function detectEnvironment() {
        var ua = navigator.userAgent || '';
        var telegramUa = /Telegram/i.test(ua);
        var webApp = window.Telegram && window.Telegram.WebApp;
        var activeWebApp = isActiveTelegramWebApp(webApp);
        var webviewProxy = typeof window.TelegramWebviewProxy !== 'undefined';
        var isTelegram = telegramUa || activeWebApp || webviewProxy;

        return {
            isTelegram: isTelegram,
            telegramUa: telegramUa,
            activeWebApp: activeWebApp,
            webAppPlatform: webApp ? (webApp.platform || '—') : '—',
            webviewProxy: webviewProxy,
            userAgent: ua,
        };
    }

    function render() {
        var info = detectEnvironment();
        var statusEl = document.getElementById('browser-status');
        var detailsEl = document.getElementById('browser-details');

        if (!statusEl) {
            return;
        }

        if (info.isTelegram) {
            statusEl.textContent = 'Открыта во встроенном браузере Telegram';
            statusEl.className = 'browser-status browser-status--telegram';
        } else {
            statusEl.textContent = 'Открыта в обычном браузере';
            statusEl.className = 'browser-status browser-status--regular';
        }

        if (detailsEl) {
            detailsEl.innerHTML =
                '<dl>' +
                '<dt>User-Agent</dt><dd>' + escapeHtml(info.userAgent) + '</dd>' +
                '<dt>Telegram в UA</dt><dd>' + (info.telegramUa ? 'да' : 'нет') + '</dd>' +
                '<dt>Активный Telegram.WebApp</dt><dd>' + (info.activeWebApp ? 'да' : 'нет') + '</dd>' +
                '<dt>WebApp platform</dt><dd>' + escapeHtml(info.webAppPlatform) + '</dd>' +
                '<dt>TelegramWebviewProxy</dt><dd>' + (info.webviewProxy ? 'да' : 'нет') + '</dd>' +
                '</dl>';
        }
    }

    function escapeHtml(text) {
        var div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', render);
    } else {
        render();
    }

    window.addEventListener('load', render);
})();
JS
);
?>
<style>
    .test-browser-page {
        max-width: 42rem;
        margin: 2rem auto;
    }

    .browser-status {
        font-size: 1.25rem;
        font-weight: 600;
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1.5rem;
    }

    .browser-status--regular {
        background: #e8f4fd;
        color: #0b3d6e;
        border: 1px solid #b6d9f5;
    }

    .browser-status--telegram {
        background: #e8f5e9;
        color: #1b5e20;
        border: 1px solid #a5d6a7;
    }

    #browser-details dl {
        margin: 0;
    }

    #browser-details dt {
        font-weight: 600;
        margin-top: 0.75rem;
    }

    #browser-details dd {
        margin: 0.25rem 0 0;
        word-break: break-word;
        font-family: ui-monospace, monospace;
        font-size: 0.875rem;
        color: #555;
    }
</style>

<div class="test-browser-page">
    <h1 class="h3 mb-3"><?= Html::encode($this->title) ?></h1>
    <p id="browser-status" class="browser-status browser-status--regular">Определяем окружение…</p>
    <div id="browser-details" class="text-muted small" aria-live="polite"></div>
</div>
