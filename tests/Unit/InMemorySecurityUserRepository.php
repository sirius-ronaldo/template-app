<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\SecurityUserRepositoryInterface;

final class InMemorySecurityUserRepository implements SecurityUserRepositoryInterface
{
    public ?string $passwordHash = null;

    /** @var array{int, int}|null */
    public ?array $attachment = null;

    public function __construct(
        private readonly bool $adminExists = false,
        private readonly bool $loginExists = false,
        private readonly ?int $groupId = 10
    ) {
    }

    public function hasAdminUser(): bool
    {
        return $this->adminExists;
    }

    public function loginExists(string $login): bool
    {
        return $this->loginExists;
    }

    public function findAdminGroupId(): ?int
    {
        return $this->groupId;
    }

    public function createUser(string $login, string $name, string $email, string $passwordHash): int
    {
        $this->passwordHash = $passwordHash;

        return 1;
    }

    public function attachToGroup(int $userId, int $groupId): void
    {
        $this->attachment = [$userId, $groupId];
    }
}
