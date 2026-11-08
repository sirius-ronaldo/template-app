<?php

declare(strict_types=1);

namespace App\Support;

class CsrfTokenManager
{
    private const SESSION_KEY = '_csrf_token';

    public function __construct(private readonly Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::SESSION_KEY);
        if (is_string($token) && $token !== '') {
            return $token;
        }

        $token = bin2hex(random_bytes(32));
        $this->session->put(self::SESSION_KEY, $token);
        return $token;
    }

    public function validate(?string $token): bool
    {
        $current = $this->session->get(self::SESSION_KEY);
        return is_string($current) && is_string($token) && hash_equals($current, $token);
    }
}
