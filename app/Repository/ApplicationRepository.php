<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class ApplicationRepository implements ApplicationRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function paginate(string $filter, int $offset, int $limit): array
    {
        $where = $filter === '' ? '' : ' WHERE a.seg_apl_codigo ILIKE :filter OR a.seg_apl_nome ILIKE :filter';
        $count = $this->pdo->prepare('SELECT COUNT(*) FROM seg_aplicacoes a' . $where);
        if ($filter !== '') {
            $count->bindValue('filter', '%' . $filter . '%');
        }
        $count->execute();
        $total = (int) $count->fetchColumn();
        $statement = $this->pdo->prepare(
            'SELECT a.seg_apl_id AS id, a.seg_apl_codigo AS code, a.seg_apl_nome AS name, '
            . 'a.seg_apl_descricao AS description, a.seg_apl_ativo AS active, '
            . 't.seg_tap_id AS type_id, t.seg_tap_codigo AS type_code, t.seg_tap_nome AS type_name, '
            . '(a.seg_apl_ativo AND t.seg_tap_ativo) AS effective_active '
            . 'FROM seg_aplicacoes a INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = a.seg_apl_tap_id'
            . $where . ' ORDER BY a.seg_apl_codigo LIMIT :limit OFFSET :offset'
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
            'SELECT a.seg_apl_id AS id, a.seg_apl_codigo AS code, a.seg_apl_nome AS name, '
            . 'a.seg_apl_descricao AS description, a.seg_apl_ativo AS active, '
            . 't.seg_tap_id AS type_id, t.seg_tap_codigo AS type_code, t.seg_tap_nome AS type_name, '
            . 't.seg_tap_ativo AS type_active, (a.seg_apl_ativo AND t.seg_tap_ativo) AS effective_active '
            . 'FROM seg_aplicacoes a INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = a.seg_apl_tap_id '
            . 'WHERE a.seg_apl_id = :id'
        );
        $statement->execute(['id' => $id]);
        $result = $statement->fetch();
        return is_array($result) ? $result : null;
    }

    public function codeExists(string $code, ?int $exceptId = null): bool
    {
        $sql = 'SELECT EXISTS (SELECT 1 FROM seg_aplicacoes WHERE seg_apl_codigo = :code';
        if ($exceptId !== null) {
            $sql .= ' AND seg_apl_id <> :except_id';
        }
        $statement = $this->pdo->prepare($sql . ')');
        $statement->bindValue('code', $code);
        if ($exceptId !== null) {
            $statement->bindValue('except_id', $exceptId, PDO::PARAM_INT);
        }
        $statement->execute();
        return (bool) $statement->fetchColumn();
    }

    public function create(int $typeId, string $code, string $name, string $description, bool $active): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_aplicacoes '
            . '(seg_apl_tap_id, seg_apl_codigo, seg_apl_nome, seg_apl_descricao, seg_apl_ativo) '
            . 'VALUES (:type_id, :code, :name, :description, :active) RETURNING seg_apl_id'
        );
        $statement->bindValue('type_id', $typeId, PDO::PARAM_INT);
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    public function update(int $id, int $typeId, string $code, string $name, string $description, bool $active): void
    {
        $statement = $this->pdo->prepare(
            'UPDATE seg_aplicacoes SET seg_apl_tap_id = :type_id, seg_apl_codigo = :code, '
            . 'seg_apl_nome = :name, seg_apl_descricao = :description, seg_apl_ativo = :active '
            . 'WHERE seg_apl_id = :id'
        );
        $statement->bindValue('type_id', $typeId, PDO::PARAM_INT);
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function setActive(int $id, bool $active): void
    {
        $statement = $this->pdo->prepare('UPDATE seg_aplicacoes SET seg_apl_ativo = :active WHERE seg_apl_id = :id');
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
        $statement->bindValue('id', $id, PDO::PARAM_INT);
        $statement->execute();
    }

    public function delete(int $id): void
    {
        $statement = $this->pdo->prepare('DELETE FROM seg_aplicacoes WHERE seg_apl_id = :id');
        $statement->execute(['id' => $id]);
    }
}
