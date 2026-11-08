<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\GroupRepositoryInterface;

final class InMemoryGroupRepository implements GroupRepositoryInterface
{
    /** @var array<int,array<string,mixed>> */
    public array $records = [];
    /** @var array<int,list<int>> */
    public array $memberships = [];

    public function paginate(string $filter, int $offset, int $limit): array
    {
        return ['items' => array_values($this->records), 'total' => count($this->records)];
    }

    public function find(int $groupId): ?array
    {
        return $this->records[$groupId] ?? null;
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        foreach ($this->records as $id => $record) {
            if ($id !== $exceptId && $record['code'] === $code) {
                return true;
            }
        }
        return false;
    }

    public function create(string $code, string $name, string $description, bool $active): int
    {
        $id = count($this->records) + 1;
        $this->records[$id] = compact('code', 'name', 'description', 'active') + ['id' => $id];
        return $id;
    }

    public function update(int $groupId, string $code, string $name, string $description, bool $active): void
    {
        $this->records[$groupId] = compact('code', 'name', 'description', 'active') + ['id' => $groupId];
    }

    public function setActive(int $groupId, bool $active): void
    {
        $this->records[$groupId]['active'] = $active;
    }

    public function delete(int $groupId): void
    {
        unset($this->records[$groupId]);
    }

    public function userGroups(int $userId): array
    {
        return array_values(array_filter(
            $this->records,
            fn (array $group): bool => in_array((int) $group['id'], $this->memberships[$userId] ?? [], true)
        ));
    }

    public function syncUserGroups(int $userId, array $groupIds): void
    {
        $this->memberships[$userId] = $groupIds;
    }
}
