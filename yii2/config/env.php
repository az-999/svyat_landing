<?php

declare(strict_types=1);

use Dotenv\Dotenv;

$root = dirname(__DIR__);

if (is_readable($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

if (!function_exists('env')) {
    /**
     * @param mixed $default
     */
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? getenv($key);

        if ($value === false || $value === '') {
            return $default;
        }

        return $value;
    }
}
