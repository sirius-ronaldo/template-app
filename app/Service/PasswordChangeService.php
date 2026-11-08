<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserCredentialRepositoryInterface;
use App\Support\PasswordHasher;

final class PasswordChangeService
{
    public function __construct(
        private readonly UserCredentialRepositoryInterface $users,
        private readonly PasswordHasher $hasher
    ) {
    }

    public function change(int $userId, string $current, string $new, string $confirmation): ?string
    {
        $hash = $this->users->findPasswordHash($userId);
        if ($hash === null || !$this->hasher->verify($current, $hash)) {
            return 'A senha atual esta incorreta.';
        }
        if (strlen($new) < 8) {
            return 'A nova senha deve ter pelo menos 8 caracteres.';
        }
        if ($new !== $confirmation) {
            return 'A confirmacao da nova senha nao confere.';
        }

        $this->users->updatePasswordHash($userId, $this->hasher->hash($new));
        return null;
    }
}
