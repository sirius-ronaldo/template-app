<?php

declare(strict_types=1);

namespace App\Support;

final class FlashBag
{
    private const SESSION_KEY = '_flash';

    public function __construct(private readonly Session $session)
    {
    }

    public function add(string $key, mixed $value): void
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        if (!is_array($flash)) {
            $flash = [];
        }
        $flash[$key] = $value;
        $this->session->put(self::SESSION_KEY, $flash);
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $flash = $this->session->get(self::SESSION_KEY, []);
        if (!is_array($flash)) {
            return $default;
        }
        $value = $flash[$key] ?? $default;
        unset($flash[$key]);
        $this->session->put(self::SESSION_KEY, $flash);
        return $value;
    }
}
