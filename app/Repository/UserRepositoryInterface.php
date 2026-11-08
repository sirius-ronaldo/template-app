<?php

declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface
{
    /** @return array{items:list<array<string,mixed>>,total:int} */
    public function paginate(string $filter, int $offset, int $limit): array;

    /** @return array<string,mixed>|null */
    public function find(int $userId): ?array;

    public function loginExists(string $login, ?int $exceptId = null): bool;

    public function create(string $login, string $name, string $email, bool $active, string $passwordHash): int;

    public function update(int $userId, string $login, string $name, string $email, bool $active): void;

    public function updatePassword(int $userId, string $passwordHash): void;

    public function setActive(int $userId, bool $active): void;

    public function delete(int $userId): void;
}
