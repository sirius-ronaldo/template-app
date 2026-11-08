<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class SecurityUserRepository implements SecurityUserRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function hasAdminUser(): bool
    {
        $statement = $this->pdo->query(
            "SELECT EXISTS (
                SELECT 1
                FROM seg_usuarios_grupos ug
                INNER JOIN seg_grupos g ON g.seg_gru_id = ug.seg_usg_gru_id
                WHERE g.seg_gru_codigo = 'ADMIN'
            )"
        );

        return (bool) $statement->fetchColumn();
    }

    public function loginExists(string $login): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT EXISTS (SELECT 1 FROM seg_usuarios WHERE seg_usu_login = :login)'
        );
        $statement->execute(['login' => $login]);

        return (bool) $statement->fetchColumn();
    }

    public function findAdminGroupId(): ?int
    {
        $statement = $this->pdo->query(
            "SELECT seg_gru_id FROM seg_grupos WHERE seg_gru_codigo = 'ADMIN'"
        );
        $groupId = $statement->fetchColumn();

        return $groupId === false ? null : (int) $groupId;
    }

    public function createUser(
        string $login,
        string $name,
        string $email,
        string $passwordHash
    ): int {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_usuarios ' .
            '(seg_usu_nome, seg_usu_login, seg_usu_email, seg_usu_senha_hash, seg_usu_ativo) ' .
            'VALUES (:name, :login, :email, :password_hash, true) ' .
            'RETURNING seg_usu_id'
        );
        $statement->execute([
            'name' => $name,
            'login' => $login,
            'email' => $email,
            'password_hash' => $passwordHash,
        ]);

        return (int) $statement->fetchColumn();
    }

    public function attachToGroup(int $userId, int $groupId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) ' .
            'VALUES (:user_id, :group_id)'
        );
        $statement->execute(['user_id' => $userId, 'group_id' => $groupId]);
    }
}
