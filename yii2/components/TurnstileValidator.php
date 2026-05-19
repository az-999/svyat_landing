<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use yii\base\Component;

class TurnstileValidator extends Component
{
    public string $secretKey = '';

    public function isEnabled(): bool
    {
        return $this->secretKey !== '';
    }

    public function validate(?string $token, ?string $remoteIp = null): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }

        if ($token === null || $token === '') {
            return false;
        }

        $post = http_build_query([
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $remoteIp ?? '',
        ]);

        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n"
                    . 'Content-Length: ' . strlen($post) . "\r\n",
                'content' => $post,
                'timeout' => 10,
            ],
        ]);

        $body = @file_get_contents(
            'https://challenges.cloudflare.com/turnstile/v0/siteverify',
            false,
            $context,
        );

        if ($body === false) {
            Yii::warning('Turnstile verify request failed', __METHOD__);

            return false;
        }

        $data = json_decode($body, true);

        return is_array($data) && !empty($data['success']);
    }
}
