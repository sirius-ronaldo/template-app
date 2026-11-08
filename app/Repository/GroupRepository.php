<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class GroupRepository implements GroupRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function paginate(string $filter, int $offset, int $limit): array
    {
        $where = $filter === '' ? '' : ' WHERE seg_gru_codigo ILIKE :filter OR seg_gru_nome ILIKE :filter';
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM seg_grupos' . $where);
        if ($filter !== '') {
            $count->bindValue('filter', '%' . $filter . '%');
        }
        $count->execute();
        $total = (int) $count->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT seg_gru_id AS id, seg_gru_codigo AS code, seg_gru_nome AS name, '
            . 'seg_gru_descricao AS description, seg_gru_ativo AS active '
            . 'FROM seg_grupos' . $where . ' ORDER BY seg_gru_codigo LIMIT :limit OFFSET :offset'
        );
        if ($filter !== '') {
            $statement->bindValue('filter', '%' . $filter . '%');
        }
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return ['items' => $statement->fetchAll(), 'total' => $total];
    }

    public function find(int $groupId): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT seg_gru_id AS id, seg_gru_codigo AS code, seg_gru_nome AS name, '
            . 'seg_gru_descricao AS description, seg_gru_ativo AS active '
            . 'FROM seg_grupos WHERE seg_gru_id = :id'
        );
        $statement->execute(['id' => $groupId]);
        $group = $statement->fetch();
        return is_array($group) ? $group : null;
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM seg_grupos WHERE seg_gru_codigo = :code';
        if ($exceptId !== null) {
            $sql .= ' AND seg_gru_id <> :except_id';
        }
        $statement = $this->pdo->prepare($sql . ')');
        $statement->bindValue('code', $code);
        if ($exceptId !== null) {
            $statement->bindValue('except_id', $exceptId, PDO::PARAM_INT);
        }
        $statement->execute();
        return (bool) $statement->fetchColumn();
    }

    public function create(string $code, string $name, string $description, bool $active): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome, seg_gru_descricao, seg_gru_ativo) '
            . 'VALUES (:code, :name, :description, :active) RETURNING seg_gru_id'
        );
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    public function update(int $groupId, string $code, string $name, string $description, bool $active): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE seg_grupos SET seg_gru_codigo = :code, seg_gru_nome = :name, '
            . 'seg_gru_descricao = :description, seg_gru_ativo = :active WHERE seg_gru_id = :id'
        );
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $groupId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function setActive(int $groupId, bool $active): void
    {
        $statement = $this->pdo->prepare('UPDATE seg_grupos SET seg_gru_ativo = :active WHERE seg_gru_id = :id');
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $groupId, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $groupId): void
    {
        $statement = $this->pdo->prepare('DELETE FROM seg_grupos WHERE seg_gru_id = :id');
        $statement->execute(['id' => $groupId]);
    }

    public function userGroups(int $userId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT g.seg_gru_id AS id, g.seg_gru_codigo AS code, g.seg_gru_nome AS name, '
            . 'g.seg_gru_ativo AS active FROM seg_usuarios_grupos ug '
            . 'INNER JOIN seg_grupos g ON g.seg_gru_id = ug.seg_usg_gru_id '
            . 'WHERE ug.seg_usg_usu_id = :user_id ORDER BY g.seg_gru_codigo'
        );
        $statement->execute(['user_id' => $userId]);
        return $statement->fetchAll();
    }

    public function syncUserGroups(int $userId, array $groupIds): void
    {
        $groupIds = array_values(array_unique(array_map('intval', $groupIds)));
        $this->pdo->beginTransaction();
        try {
            if ($groupIds !== []) {
                $placeholders = implode(', ', array_fill(0, count($groupIds), '?'));
                $check = $this->pdo->prepare(
                    'SELECT COUNT(*) FROM seg_grupos WHERE seg_gru_ativo = true '
                    . 'AND seg_gru_id IN (' . $placeholders . ')'
                );
                $check->execute($groupIds);
                if ((int) $check->fetchColumn() !== count($groupIds)) {
                    throw new \RuntimeException('Grupo inexistente ou inativo.');
                }
            }

            $deleteSql = 'DELETE FROM seg_usuarios_grupos WHERE seg_usg_usu_id = ?';
            if ($groupIds !== []) {
                $placeholders = implode(', ', array_fill(0, count($groupIds), '?'));
                $deleteSql .= ' AND seg_usg_gru_id NOT IN (' . $placeholders . ')';
                $delete = $this->pdo->prepare($deleteSql);
                $delete->execute(array_merge([$userId], $groupIds));
            } else {
                $delete = $this->pdo->prepare($deleteSql);
                $delete->execute([$userId]);
            }

            foreach ($groupIds as $groupId) {
                $insert = $this->pdo->prepare(
                    'INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) '
                    . 'SELECT :user_id, :group_id WHERE NOT EXISTS '
                    . '(SELECT 1 FROM seg_usuarios_grupos '
                    . 'WHERE seg_usg_usu_id = :check_user AND seg_usg_gru_id = :check_group)'
                );
                $insert->execute([
                    'user_id' => $userId,
                    'group_id' => $groupId,
                    'check_user' => $userId,
                    'check_group' => $groupId,
                ]);
            }
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }
}
