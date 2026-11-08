<?php

declare(strict_types=1);

namespace App\Repository;

interface GroupRepositoryInterface
{
    /** @return array{items:list<array<string,mixed>>,total:int} */
    public function paginate(string $filter, int $offset, int $limit): array;

    /** @return array<string,mixed>|null */
    public function find(int $groupId): ?array;

    public function codeExists(string $code, ?int $exceptId = null): bool;

    public function create(string $code, string $name, string $description, bool $active): int;

    public function update(int $groupId, string $code, string $name, string $description, bool $active): void;

    public function setActive(int $groupId, bool $active): void;

    public function delete(int $groupId): void;

    /** @return list<array<string,mixed>> */
    public function userGroups(int $userId): array;

    /** @param list<int> $groupIds */
    public function syncUserGroups(int $userId, array $groupIds): void;
}
