# svyat_landing

Лендинг-резюме на Yii2 с интеграцией OpenAI.

## Запуск

1. Скопируйте переменные окружения:

```bash
cp yii2/.env.example yii2/.env
```

2. Укажите в `yii2/.env` ключ `OPENAI_API_KEY` и при необходимости `COOKIE_VALIDATION_KEY`.

3. Установите зависимости PHP (если папки `yii2/vendor` ещё нет):

```bash
docker run --rm -v "$(pwd)/yii2:/app" -w /app composer:latest install --no-interaction
```

4. Запустите Docker:

```bash
docker compose up -d
```

Приложение будет доступно на [http://localhost:6001](http://localhost:6001).

> **Почему не 6000?** На Windows порт 6000 часто занят **MobaXterm** (X11-сервер `XWin_MobaX`).  
> Docker при этом работает, но браузер подключается не к Apache. Проверка в PowerShell:  
> `Get-NetTCPConnection -LocalPort 6000 | Select OwningProcess`  
> Если нужен именно 6000 — отключите X11 в MobaXterm или смените его display port.

### Если страница не открывается

- Откройте **http://127.0.0.1:6001** (не 6000).
- В Docker Desktop у сервиса `web` должен быть статус **Running**.
- Перезапустите: `docker compose down && docker compose up -d`
- Проверьте логи: `docker compose logs web`
- После `git clone` обязательно выполните `composer install` в `yii2/` (см. шаг 3).

## Структура

- `yii2/` — приложение Yii2 Basic
- `index.html` — исходник резюме (контент подгружается в `yii2/data/resume-body.html`)
- `docker-compose.yml` — PHP 8.2 + Apache на порту **6000**

## OpenAI

Клиент доступен через компонент приложения:

```php
Yii::$app->openai->getClient()->chat()->create([...]);
```

Ключ API читается из `OPENAI_API_KEY` в `.env` (библиотека `vlucas/phpdotenv`).

## Чат с AI-ассистентом

- Вход: [http://localhost:6001/index.php?r=chat](http://localhost:6001/index.php?r=chat)
- Защита: Turnstile (или капча Yii2), honeypot, rate limit, серверная сессия с `thread_id`
- В `.env` укажите `OPENAI_ASSISTANT_ID=asst_7eRl9fdPMS6maIF0kkI1Tsgp`
- Опционально: `TURNSTILE_SITE_KEY` и `TURNSTILE_SECRET_KEY` с [Cloudflare Turnstile](https://developers.cloudflare.com/turnstile/)
