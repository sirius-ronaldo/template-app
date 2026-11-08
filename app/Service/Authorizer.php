<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\AuthorizationException;
use App\Repository\AuthorizationRepository;

final class Authorizer
{
    public function __construct(
        private readonly AuthorizationRepository $authorization,
        private readonly AdminChecker $admins
    ) {
    }

    public function can(int $userId, string $application, string $permission): bool
    {
        if (!$this->authorization->userIsActive($userId)) {
            return false;
        }
        if ($this->admins->isAdmin($userId)) {
            return true;
        }
        return $this->authorization->hasActiveGrant($userId, $application, $permission);
    }

    public function authorize(int $userId, string $application, string $permission): void
    {
        if (!$this->can($userId, $application, $permission)) {
            throw new AuthorizationException('Acesso negado.');
        }
    }
}
