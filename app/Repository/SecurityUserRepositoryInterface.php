<?php

declare(strict_types=1);

namespace App\Repository;

interface SecurityUserRepositoryInterface
{
    public function hasAdminUser(): bool;

    public function loginExists(string $login): bool;

    public function findAdminGroupId(): ?int;

    public function createUser(string $login, string $name, string $email, string $passwordHash): int;

    public function attachToGroup(int $userId, int $groupId): void;
}
