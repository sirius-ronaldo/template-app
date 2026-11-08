<?php

declare(strict_types=1);

namespace App\Repository;

interface ApplicationRepositoryInterface
{
    /** @return array{items:list<array<string,mixed>>,total:int} */
    public function paginate(string $filter, int $offset, int $limit): array;
    /** @return array<string,mixed>|null */
    public function find(int $id): ?array;
    public function codeExists(string $code, ?int $exceptId = null): bool;
    public function create(int $typeId, string $code, string $name, string $description, bool $active): int;
    public function update(int $id, int $typeId, string $code, string $name, string $description, bool $active): void;
    public function setActive(int $id, bool $active): void;
    public function delete(int $id): void;
}
