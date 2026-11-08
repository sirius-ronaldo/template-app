<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ApplicationTypeRepository implements ApplicationTypeRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function paginate(string $filter, int $offset, int $limit): array
    {
        $where = $filter === ''
            ? ''
            : ' WHERE seg_tap_codigo ILIKE :filter OR seg_tap_nome ILIKE :filter';
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM seg_tipos_aplicacoes' . $where);
        if ($filter !== '') {
            $count->bindValue('filter', '%' . $filter . '%');
        }
        $count->execute();
        $total = (int) $count->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT seg_tap_id AS id, seg_tap_codigo AS code, seg_tap_nome AS name, '
            . 'seg_tap_descricao AS description, seg_tap_ativo AS active '
            . 'FROM seg_tipos_aplicacoes' . $where . ' ORDER BY seg_tap_codigo LIMIT :limit OFFSET :offset'
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
            'SELECT seg_tap_id AS id, seg_tap_codigo AS code, seg_tap_nome AS name, '
            . 'seg_tap_descricao AS description, seg_tap_ativo AS active '
            . 'FROM seg_tipos_aplicacoes WHERE seg_tap_id = :id'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();
        return is_array($result) ? $result : null;
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM seg_tipos_aplicacoes WHERE seg_tap_codigo = :code';
        if ($exceptId !== null) {
            $sql .= ' AND seg_tap_id <> :except_id';
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
            'INSERT INTO seg_tipos_aplicacoes (seg_tap_codigo, seg_tap_nome, seg_tap_descricao, seg_tap_ativo) '
            . 'VALUES (:code, :name, :description, :active) RETURNING seg_tap_id'
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
            'UPDATE seg_tipos_aplicacoes SET seg_tap_codigo = :code, seg_tap_nome = :name, '
            . 'seg_tap_descricao = :description, seg_tap_ativo = :active '
            . 'WHERE seg_tap_id = :id'
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
        $statement = $this->pdo->prepare(
            'UPDATE seg_tipos_aplicacoes SET seg_tap_ativo = :active WHERE seg_tap_id = :id'
        );
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM seg_tipos_aplicacoes WHERE seg_tap_id = :id');
        $statement->execute(['id' => $id]);
    }

    public function listWithPermissions(): array
    {
        $statement = $this->pdo->query(
            'SELECT seg_tap_id AS id, seg_tap_codigo AS code, seg_tap_nome AS name, seg_tap_ativo AS active '
            . 'FROM seg_tipos_aplicacoes ORDER BY seg_tap_codigo'
        );
        return $statement->fetchAll();
    }
}
