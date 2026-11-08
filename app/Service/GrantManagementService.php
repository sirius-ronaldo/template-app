<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\GrantRepositoryInterface;

final class GrantManagementService
{
    public function __construct(private readonly GrantRepositoryInterface $grants)
    {
    }

    /** @return list<array<string,mixed>> */
    public function matrix(int $groupId, string $filter): array
    {
        return $this->grants->matrix($groupId, trim($filter));
    }

    /**
     * @param array<int,list<int>> $permissionsByApplication
     * @return array<string,string>
     */
    public function sync(int $groupId, array $permissionsByApplication): array
    {
        try {
            $this->grants->sync($groupId, $permissionsByApplication);
        } catch (\Throwable $exception) {
            return ['global' => $exception->getMessage()];
        }
        return [];
    }
}
