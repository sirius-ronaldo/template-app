<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\InitialAdminException;
use App\Repository\SecurityUserRepositoryInterface;
use PDO;

final class InitialAdminService
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly SecurityUserRepositoryInterface $users
    ) {
    }

    public function create(string $login, string $name, string $email, string $password): void
    {
        if (!defined('PASSWORD_ARGON2ID')) {
            throw new InitialAdminException('Argon2id nao esta disponivel neste ambiente.');
        }

        if ($this->users->hasAdminUser()) {
            throw new InitialAdminException('Ja existe um usuario ADMIN.');
        }

        if ($this->users->loginExists($login)) {
            throw new InitialAdminException('O login informado ja existe.');
        }

        $groupId = $this->users->findAdminGroupId();
        if ($groupId === null) {
            throw new InitialAdminException('Grupo ADMIN nao encontrado. Execute as migrations.');
        }

        $passwordHash = $this->hashPassword($password);

        $this->pdo->beginTransaction();
        try {
            $userId = $this->users->createUser($login, $name, $email, $passwordHash);
            $this->users->attachToGroup($userId, $groupId);
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }

            throw new InitialAdminException('Nao foi possivel criar o usuario ADMIN.', 0, $exception);
        }
    }

    private function hashPassword(string $password): string
    {
        return password_hash($password, PASSWORD_ARGON2ID);
    }
}
