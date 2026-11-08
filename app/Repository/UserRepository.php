<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class UserRepository implements UserRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function paginate(string $filter, int $offset, int $limit): array
    {
        $where = $filter === ''
            ? ''
            : ' WHERE seg_usu_login ILIKE :filter OR seg_usu_nome ILIKE :filter '
                . 'OR seg_usu_email ILIKE :filter';
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM seg_usuarios' . $where);
        if ($filter !== '') {
            $count->bindValue('filter', '%' . $filter . '%');
        }
        $count->execute();
        $total = (int) $count->fetchColumn();

        $statement = $this->pdo->prepare(
            'SELECT seg_usu_id AS id, seg_usu_login AS login, seg_usu_nome AS name, '
            . 'seg_usu_email AS email, seg_usu_ativo AS active '
            . 'FROM seg_usuarios' . $where . ' ORDER BY seg_usu_nome, seg_usu_id LIMIT :limit OFFSET :offset'
        );
        if ($filter !== '') {
            $statement->bindValue('filter', '%' . $filter . '%');
        }
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        $items = $statement->fetchAll();
        return ['items' => $items, 'total' => $total];
    }

    public function find(int $userId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT seg_usu_id AS id, seg_usu_login AS login, seg_usu_nome AS name, '
            . 'seg_usu_email AS email, seg_usu_ativo AS active FROM seg_usuarios WHERE seg_usu_id = :id'
        );
        $statement->execute(['id' => $userId]);
        $user = $statement->fetch();
        return is_array($user) ? $user : null;
    }

    public function loginExists(string $login, ?int $exceptId = null): bool
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM seg_usuarios WHERE seg_usu_login = :login';
        if ($exceptId !== null) {
            $sql .= ' AND seg_usu_id <> :except_id';
        }
        $sql .= ')';
        $statement = $this->pdo->prepare($sql);
        $statement->bindValue('login', $login);
        if ($exceptId !== null) {
            $statement->bindValue('except_id', $exceptId, PDO::PARAM_INT);
        }
        $statement->execute();
        return (bool) $statement->fetchColumn();
    }

    public function create(string $login, string $name, string $email, bool $active, string $passwordHash): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_usuarios '
            . '(seg_usu_login, seg_usu_nome, seg_usu_email, seg_usu_ativo, seg_usu_senha_hash) '
            . 'VALUES (:login, :name, :email, :active, :hash) RETURNING seg_usu_id'
        );
        $statement->bindValue('login', $login);
        $statement->bindValue('name', $name);
        $statement->bindValue('email', $email);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('hash', $passwordHash);
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    public function update(int $userId, string $login, string $name, string $email, bool $active): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE seg_usuarios SET seg_usu_login = :login, seg_usu_nome = :name, '
            . 'seg_usu_email = :email, seg_usu_ativo = :active WHERE seg_usu_id = :id'
        );
        $statement->bindValue('login', $login);
        $statement->bindValue('name', $name);
        $statement->bindValue('email', $email);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function updatePassword(int $userId, string $passwordHash): void
    {
        $statement = $this->pdo->prepare('UPDATE seg_usuarios SET seg_usu_senha_hash = :hash WHERE seg_usu_id = :id');
        $statement->execute(['hash' => $passwordHash, 'id' => $userId]);
    }

    public function setActive(int $userId, bool $active): void
    {
        $statement = $this->pdo->prepare('UPDATE seg_usuarios SET seg_usu_ativo = :active WHERE seg_usu_id = :id');
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $userId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $userId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM seg_usuarios WHERE seg_usu_id = :id');
        $statement->execute(['id' => $userId]);
    }
}
