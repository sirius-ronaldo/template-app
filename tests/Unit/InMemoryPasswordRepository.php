<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\UserCredentialRepositoryInterface;

final class InMemoryPasswordRepository implements UserCredentialRepositoryInterface
{
    public function __construct(public ?string $hash)
    {
    }

    public function findByLogin(string $login): ?array
    {
        return null;
    }

    public function isActive(int $userId): bool
    {
        return true;
    }

    public function updatePasswordHash(int $userId, string $passwordHash): void
    {
        $this->hash = $passwordHash;
    }

    public function findPasswordHash(int $userId): ?string
    {
        return $this->hash;
    }
}
