<?php

declare(strict_types=1);

namespace App\Support;

use App\Auth\AuthenticatedUser;

final class SessionManager
{
    public const AUTH_KEY = 'auth';
    public const IDLE_TIMEOUT = 1800;
    public const ABSOLUTE_TIMEOUT = 28800;

    public function __construct(private readonly Session $session, private readonly ?int $now = null)
    {
    }

    public function authenticate(AuthenticatedUser $user): void
    {
        $this->session->regenerate();
        $now = $this->timestamp();
        $this->session->put(self::AUTH_KEY, [
            'user_id' => $user->id,
            'login' => $user->login,
            'authenticated_at' => $now,
            'last_activity_at' => $now,
        ]);
    }

    /** @return array{user_id:int,login:string,authenticated_at:int,last_activity_at:int}|null */
    public function credentials(): ?array
    {
        $auth = $this->session->get(self::AUTH_KEY);
        if (
            !is_array($auth)
            || !isset($auth['user_id'], $auth['login'], $auth['authenticated_at'], $auth['last_activity_at'])
        ) {
            return null;
        }

        $auth['user_id'] = (int) $auth['user_id'];
        $auth['authenticated_at'] = (int) $auth['authenticated_at'];
        $auth['last_activity_at'] = (int) $auth['last_activity_at'];
        return is_string($auth['login']) ? $auth : null;
    }

    public function isExpired(): bool
    {
        $auth = $this->credentials();
        if ($auth === null) {
            return true;
        }
        $now = $this->timestamp();
        return $now - $auth['last_activity_at'] >= self::IDLE_TIMEOUT
            || $now - $auth['authenticated_at'] >= self::ABSOLUTE_TIMEOUT;
    }

    public function touch(): void
    {
        $auth = $this->credentials();
        if ($auth !== null) {
            $auth['last_activity_at'] = $this->timestamp();
            $this->session->put(self::AUTH_KEY, $auth);
        }
    }

    public function logout(): void
    {
        $this->session->invalidate();
    }

    private function timestamp(): int
    {
        return $this->now ?? time();
    }
}
