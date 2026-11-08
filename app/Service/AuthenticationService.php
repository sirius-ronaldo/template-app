<?php

declare(strict_types=1);

namespace App\Service;

use App\Auth\AuthenticatedUser;
use App\Repository\UserCredentialRepositoryInterface;
use App\Support\PasswordHasher;

final class AuthenticationService
{
    public function __construct(
        private readonly UserCredentialRepositoryInterface $users,
        private readonly PasswordHasher $hasher
    ) {
    }

    public function authenticate(string $login, string $password): ?AuthenticatedUser
    {
        $credentials = $this->users->findByLogin($login);
        if (
            $credentials === null
            || !$credentials['active']
            || $credentials['password_hash'] === ''
            || !$this->hasher->verify($password, $credentials['password_hash'])
        ) {
            return null;
        }
        if ($this->hasher->needsRehash($credentials['password_hash'])) {
            $this->users->updatePasswordHash($credentials['id'], $this->hasher->hash($password));
        }
        return new AuthenticatedUser($credentials['id'], $credentials['login']);
    }
}
