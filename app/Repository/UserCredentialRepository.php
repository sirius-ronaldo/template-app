<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserCredentialRepository implements UserCredentialRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function findByLogin(string $login): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT seg_usu_id AS id, seg_usu_login AS login, '
            . 'seg_usu_senha_hash AS password_hash, seg_usu_ativo AS active '
            . 'FROM seg_usuarios WHERE seg_usu_login = :login'
        );
        $statement->execute(['login' => $login]);
        $row = $statement->fetch();
        if ($row === false || !is_array($row)) {
            return null;
        }
        return [
            'id' => (int) $row['id'],
            'login' => (string) $row['login'],
            'password_hash' => (string) ($row['password_hash'] ?? ''),
            'active' => (bool) $row['active'],
        ];
    }

    public function isActive(int $userId): bool
    {
        $statement = $this->pdo->prepare('SELECT seg_usu_ativo FROM seg_usuarios WHERE seg_usu_id = :id');
        $statement->execute(['id' => $userId]);
        return (bool) $statement->fetchColumn();
    }

    public function updatePasswordHash(int $userId, string $passwordHash): void
    {
        $statement = $this->pdo->prepare('UPDATE seg_usuarios SET seg_usu_senha_hash = :hash WHERE seg_usu_id = :id');
        $statement->execute(['hash' => $passwordHash, 'id' => $userId]);
    }
}
