<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\AuthorizationRepository;

final class AdminChecker
{
    public function __construct(private readonly AuthorizationRepository $authorization)
    {
    }

    public function isAdmin(int $userId): bool
    {
        return $this->authorization->userIsActive($userId)
            && $this->authorization->hasActiveAdminGroup($userId);
    }
}
