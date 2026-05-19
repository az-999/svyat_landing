<?php

declare(strict_types=1);

namespace app\components;

use Yii;
use yii\base\Component;

class ChatRateLimiter extends Component
{
    public int $perMinute = 5;

    public int $perHour = 30;

    /**
     * @return string|null Сообщение об ошибке или null, если лимит не превышен.
     */
    public function acquire(string $scope): ?string
    {
        if (!$this->hit($scope . ':m', $this->perMinute, 60)) {
            return 'Слишком много сообщений. Подождите минуту.';
        }

        if (!$this->hit($scope . ':h', $this->perHour, 3600)) {
            return 'Достигнут часовой лимит сообщений. Попробуйте позже.';
        }

        return null;
    }

    private function hit(string $key, int $limit, int $ttl): bool
    {
        $cache = Yii::$app->cache;
        $count = (int) $cache->get($key);

        if ($count >= $limit) {
            return false;
        }

        if ($count === 0) {
            $cache->set($key, 1, $ttl);
        } else {
            $cache->set($key, $count + 1, $ttl);
        }

        return true;
    }
}
