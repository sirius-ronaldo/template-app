<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\UserRepositoryInterface;

final class InMemoryUserRepository implements UserRepositoryInterface
{
    /** @var array<int,array<string,mixed>> */
    public array $records = [];
    public ?string $passwordHash = null;

    public function paginate(string $filter, int $offset, int $limit): array
    {
        return ['items' => array_values($this->records), 'total' => count($this->records)];
    }

    public function find(int $userId): ?array
    {
        return $this->records[$userId] ?? null;
    }

    public function loginExists(string $login, ?int $exceptId = null): bool
    {
        foreach ($this->records as $id => $record) {
            if ($id !== $exceptId && $record['login'] === $login) {
                return true;
            }
        }
        return false;
    }

    public function create(string $login, string $name, string $email, bool $active, string $passwordHash): int
    {
        $this->passwordHash = $passwordHash;
        $id = count($this->records) + 1;
        $this->records[$id] = compact('login', 'name', 'email', 'active') + ['id' => $id];
        return $id;
    }

    public function update(int $userId, string $login, string $name, string $email, bool $active): void
    {
        $this->records[$userId] = compact('login', 'name', 'email', 'active') + ['id' => $userId];
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }

    public function setActive(int $userId, bool $active): void
    {
        $this->records[$userId]['active'] = $active;
    }

    public function delete(int $userId): void
    {
        unset($this->records[$userId]);
    }
}
