<?php

return [
    'adminEmail' => env('ADMIN_EMAIL', 'admin@example.com'),
    'senderEmail' => env('SENDER_EMAIL', 'noreply@example.com'),
    'senderName' => env('SENDER_NAME', 'Svyat Landing'),
    'openaiApiKey' => env('OPENAI_API_KEY', ''),
    'openaiAssistantId' => env('OPENAI_ASSISTANT_ID', ''),
    'turnstileSiteKey' => env('TURNSTILE_SITE_KEY', ''),
    'turnstileSecretKey' => env('TURNSTILE_SECRET_KEY', ''),
    'chatRateLimitPerMinute' => (int) env('CHAT_RATE_LIMIT_PER_MINUTE', 5),
    'chatRateLimitPerHour' => (int) env('CHAT_RATE_LIMIT_PER_HOUR', 30),
    'chatMinDelaySeconds' => (int) env('CHAT_MIN_DELAY_SECONDS', 2),
    'chatMaxMessageLength' => (int) env('CHAT_MAX_MESSAGE_LENGTH', 2000),
];
