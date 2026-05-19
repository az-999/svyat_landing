<?php

declare(strict_types=1);

namespace app\models;

use Yii;

class ChatSession
{
    private const SESSION_KEY = 'chat';

    public static function get(): ?array
    {
        $data = Yii::$app->session->get(self::SESSION_KEY);

        return is_array($data) ? $data : null;
    }

    public static function isAllowed(): bool
    {
        $data = self::get();

        return $data !== null && !empty($data['allowed']) && !empty($data['thread_id']);
    }

    public static function getThreadId(): ?string
    {
        $data = self::get();

        return $data['thread_id'] ?? null;
    }

    /**
     * @param array{allowed: bool, thread_id: string, started_at: int, message_count?: int, last_message_at?: int} $data
     */
    public static function save(array $data): void
    {
        Yii::$app->session->set(self::SESSION_KEY, $data);
    }

    public static function incrementMessageCount(): void
    {
        $data = self::get();
        if ($data === null) {
            return;
        }

        $data['message_count'] = (int) ($data['message_count'] ?? 0) + 1;
        $data['last_message_at'] = time();
        self::save($data);
    }

    public static function clear(): void
    {
        Yii::$app->session->remove(self::SESSION_KEY);
    }
}
