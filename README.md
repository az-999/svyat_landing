# svyat_landing

Лендинг-резюме на Yii2 с интеграцией OpenAI.

## Запуск

1. Скопируйте переменные окружения:

```bash
cp yii2/.env.example yii2/.env
```

2. Укажите в `yii2/.env` ключ `OPENAI_API_KEY` и при необходимости `COOKIE_VALIDATION_KEY`.

3. Запустите Docker:

```bash
docker compose up -d
```

Приложение будет доступно на [http://localhost:6000](http://localhost:6000).

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
