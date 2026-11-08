<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class PermissionRepository implements PermissionRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function paginate(string $filter, int $offset, int $limit): array
    {
        $where = $filter === ''
            ? ''
            : ' WHERE seg_per_codigo ILIKE :filter OR seg_per_nome ILIKE :filter';
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM seg_permissoes' . $where);
        if ($filter !== '') {
            $count->bindValue('filter', '%' . $filter . '%');
        }
        $count->execute();
        $total = (int) $count->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT seg_per_id AS id, seg_per_codigo AS code, seg_per_nome AS name, '
            . 'seg_per_descricao AS description, seg_per_ativo AS active '
            . 'FROM seg_permissoes' . $where . ' ORDER BY seg_per_codigo LIMIT :limit OFFSET :offset'
        );
        if ($filter !== '') {
            $statement->bindValue('filter', '%' . $filter . '%');
        }
        $statement->bindValue('limit', $limit, PDO::PARAM_INT);
        $statement->bindValue('offset', $offset, PDO::PARAM_INT);
        $statement->execute();
        return ['items' => $statement->fetchAll(), 'total' => $total];
    }

    public function find(int $id): ?array
    {
        $statement = $this->pdo->prepare(
            'SELECT seg_per_id AS id, seg_per_codigo AS code, seg_per_nome AS name, '
            . 'seg_per_descricao AS description, seg_per_ativo AS active '
            . 'FROM seg_permissoes WHERE seg_per_id = :id'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();
        return is_array($result) ? $result : null;
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM seg_permissoes WHERE seg_per_codigo = :code';
        if ($exceptId !== null) {
            $sql .= ' AND seg_per_id <> :except_id';
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
            'INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome, seg_per_descricao, seg_per_ativo) '
            . 'VALUES (:code, :name, :description, :active) RETURNING seg_per_id'
        );
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    public function update(int $id, string $code, string $name, string $description, bool $active): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE seg_permissoes SET seg_per_codigo = :code, seg_per_nome = :name, '
            . 'seg_per_descricao = :description, seg_per_ativo = :active '
            . 'WHERE seg_per_id = :id'
        );
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function setActive(int $id, bool $active): void
    {
        $statement = $this->pdo->prepare('UPDATE seg_permissoes SET seg_per_ativo = :active WHERE seg_per_id = :id');
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM seg_permissoes WHERE seg_per_id = :id');
        $statement->execute(['id' => $id]);
    }

    public function activeForType(int $typeId): array
    {
        $statement = $this->pdo->prepare(
            'SELECT p.seg_per_id AS id, p.seg_per_codigo AS code, p.seg_per_nome AS name, p.seg_per_ativo AS active '
            . 'FROM seg_permissoes p INNER JOIN seg_tipos_aplicacoes_permissoes tp '
            . 'ON tp.seg_tpp_per_id = p.seg_per_id WHERE tp.seg_tpp_tap_id = :type_id ORDER BY p.seg_per_codigo'
        );
        $statement->execute(['type_id' => $typeId]);
        return $statement->fetchAll();
    }

    public function syncTypePermissions(int $typeId, array $permissionIds): void
    {
        $permissionIds = array_values(array_unique(array_map('intval', $permissionIds)));
        $this->pdo->beginTransaction();
        try {
            if ($permissionIds !== []) {
                $placeholders = implode(', ', array_fill(0, count($permissionIds), '?'));
                $check = $this->pdo->prepare(
                    'SELECT COUNT(*) FROM seg_permissoes WHERE seg_per_ativo = true '
                    . 'AND seg_per_id IN (' . $placeholders . ')'
                );
                $check->execute($permissionIds);
                if ((int) $check->fetchColumn() !== count($permissionIds)) {
                    throw new \RuntimeException('Permissao inexistente ou inativa.');
                }
            }
            $deleteSql = 'DELETE FROM seg_tipos_aplicacoes_permissoes WHERE seg_tpp_tap_id = ?';
            if ($permissionIds !== []) {
                $placeholders = implode(', ', array_fill(0, count($permissionIds), '?'));
                $deleteSql .= ' AND seg_tpp_per_id NOT IN (' . $placeholders . ')';
                $delete = $this->pdo->prepare($deleteSql);
                $delete->execute(array_merge([$typeId], $permissionIds));
            } else {
                $delete = $this->pdo->prepare($deleteSql);
                $delete->execute([$typeId]);
            }
            foreach ($permissionIds as $permissionId) {
                $insert = $this->pdo->prepare(
                    'INSERT INTO seg_tipos_aplicacoes_permissoes (seg_tpp_tap_id, seg_tpp_per_id) '
                    . 'SELECT :type_id, :permission_id WHERE NOT EXISTS '
                    . '(SELECT 1 FROM seg_tipos_aplicacoes_permissoes '
                    . 'WHERE seg_tpp_tap_id = :check_type '
                    . 'AND seg_tpp_per_id = :check_permission)'
                );
                $insert->execute([
                    'type_id' => $typeId,
                    'permission_id' => $permissionId,
                    'check_type' => $typeId,
                    'check_permission' => $permissionId,
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
