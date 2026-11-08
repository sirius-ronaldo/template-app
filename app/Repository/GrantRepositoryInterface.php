<?php

declare(strict_types=1);

namespace App\Repository;

interface GrantRepositoryInterface
{
    /** @return list<array<string,mixed>> */
    public function matrix(int $groupId, string $filter): array;

    /** @param array<int,list<int>> $permissionsByApplication */
    public function sync(int $groupId, array $permissionsByApplication): void;
}
