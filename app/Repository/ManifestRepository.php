<?php

declare(strict_types=1);

namespace App\Repository;

use App\Manifest\ApplicationDefinition;
use App\Manifest\ApplicationTypeDefinition;
use App\Manifest\ManifestDefinition;
use App\Manifest\ManifestSnapshot;
use App\Manifest\PermissionDefinition;
use PDO;

final class ManifestRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function snapshot(): ManifestSnapshot
    {
        $permissions = $this->catalog(
            'seg_permissoes',
            'seg_per_codigo',
            'seg_per_nome',
            'seg_per_descricao',
            'seg_per_ativo'
        );
        $types = $this->catalog(
            'seg_tipos_aplicacoes',
            'seg_tap_codigo',
            'seg_tap_nome',
            'seg_tap_descricao',
            'seg_tap_ativo'
        );
        $applications = [];
        foreach (
            $this->pdo->query(
                'SELECT a.seg_apl_codigo AS code, a.seg_apl_nome AS name, a.seg_apl_descricao AS description, '
                . 'a.seg_apl_ativo AS active, t.seg_tap_codigo AS type FROM seg_aplicacoes a '
                . 'INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = a.seg_apl_tap_id ORDER BY a.seg_apl_codigo'
            )->fetchAll() as $row
        ) {
            $applications[(string) $row['code']] = $row;
        }
        $relations = [];
        foreach (
            $this->pdo->query(
                'SELECT t.seg_tap_codigo AS type, p.seg_per_codigo AS permission '
                . 'FROM seg_tipos_aplicacoes_permissoes tp '
                . 'INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = tp.seg_tpp_tap_id '
                . 'INNER JOIN seg_permissoes p ON p.seg_per_id = tp.seg_tpp_per_id '
                . 'ORDER BY t.seg_tap_codigo, p.seg_per_codigo'
            )->fetchAll() as $row
        ) {
            $relations[(string) $row['type']][] = (string) $row['permission'];
        }
        return new ManifestSnapshot($permissions, $types, $applications, $relations);
    }

    /** @return array<string,int> */
    public function synchronize(ManifestDefinition $manifest): array
    {
        $this->pdo->beginTransaction();
        try {
            $permissionIds = [];
            foreach ($manifest->permissions as $definition) {
                $permissionIds[$definition->code] = $this->upsertPermission($definition);
            }
            $typeIds = [];
            foreach ($manifest->types as $definition) {
                $typeIds[$definition->code] = $this->upsertType($definition);
            }
            foreach ($manifest->applications as $definition) {
                $this->upsertApplication($definition, $typeIds[$definition->type]);
            }
            foreach ($manifest->types as $definition) {
                $this->syncRelations($typeIds[$definition->code], $definition->permissions, $permissionIds);
            }
            $this->pdo->commit();
            return [
                'permissions' => count($manifest->permissions),
                'types' => count($manifest->types),
                'applications' => count($manifest->applications),
                'relations' => array_sum(array_map(
                    static fn (ApplicationTypeDefinition $type): int => count($type->permissions),
                    $manifest->types
                )),
            ];
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    /** @return array<string,array<string,mixed>> */
    private function catalog(string $table, string $code, string $name, string $description, string $active): array
    {
        $result = [];
        $sql = "SELECT {$code} AS code, {$name} AS name, {$description} AS description, "
            . "{$active} AS active FROM {$table} ORDER BY {$code}";
        foreach ($this->pdo->query($sql)->fetchAll() as $row) {
            $result[(string) $row['code']] = $row;
        }
        return $result;
    }

    private function upsertPermission(PermissionDefinition $definition): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome, seg_per_descricao, seg_per_ativo) '
            . 'VALUES (:code, :name, :description, :active) '
            . 'ON CONFLICT (seg_per_codigo) DO UPDATE SET seg_per_nome = EXCLUDED.seg_per_nome, '
            . 'seg_per_descricao = EXCLUDED.seg_per_descricao, seg_per_ativo = EXCLUDED.seg_per_ativo '
            . 'RETURNING seg_per_id'
        );
        $this->bindCatalog(
            $statement,
            $definition->code,
            $definition->name,
            $definition->description,
            $definition->active
        );
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    private function upsertType(ApplicationTypeDefinition $definition): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_tipos_aplicacoes (seg_tap_codigo, seg_tap_nome, seg_tap_descricao, seg_tap_ativo) '
            . 'VALUES (:code, :name, :description, :active) '
            . 'ON CONFLICT (seg_tap_codigo) DO UPDATE SET seg_tap_nome = EXCLUDED.seg_tap_nome, '
            . 'seg_tap_descricao = EXCLUDED.seg_tap_descricao, seg_tap_ativo = EXCLUDED.seg_tap_ativo '
            . 'RETURNING seg_tap_id'
        );
        $this->bindCatalog(
            $statement,
            $definition->code,
            $definition->name,
            $definition->description,
            $definition->active
        );
        $statement->execute();
        return (int) $statement->fetchColumn();
    }

    private function upsertApplication(ApplicationDefinition $definition, int $typeId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_aplicacoes '
            . '(seg_apl_tap_id, seg_apl_codigo, seg_apl_nome, seg_apl_descricao, seg_apl_ativo) '
            . 'VALUES (:type_id, :code, :name, :description, :active) '
            . 'ON CONFLICT (seg_apl_codigo) DO UPDATE SET seg_apl_tap_id = EXCLUDED.seg_apl_tap_id, '
            . 'seg_apl_nome = EXCLUDED.seg_apl_nome, seg_apl_descricao = EXCLUDED.seg_apl_descricao, '
            . 'seg_apl_ativo = EXCLUDED.seg_apl_ativo'
        );
        $statement->bindValue('type_id', $typeId, PDO::PARAM_INT);
        $statement->bindValue('code', $definition->code);
        $statement->bindValue('name', $definition->name);
        $statement->bindValue('description', $definition->description);
        $statement->bindValue('active', $definition->active, PDO::PARAM_BOOL);
        $statement->execute();
    }

    /**
     * @param list<string> $permissionCodes
     * @param array<string,int> $permissionIds
     */
    private function syncRelations(int $typeId, array $permissionCodes, array $permissionIds): void
    {
        $delete = $this->pdo->prepare('DELETE FROM seg_tipos_aplicacoes_permissoes WHERE seg_tpp_tap_id = :type_id');
        $delete->execute(['type_id' => $typeId]);
        $insert = $this->pdo->prepare(
            'INSERT INTO seg_tipos_aplicacoes_permissoes '
            . '(seg_tpp_tap_id, seg_tpp_per_id) VALUES (:type_id, :permission_id)'
        );
        foreach ($permissionCodes as $code) {
            if (!array_key_exists($code, $permissionIds)) {
                throw new \RuntimeException('Permissao referenciada nao encontrada no manifesto.');
            }
            $insert->execute(['type_id' => $typeId, 'permission_id' => $permissionIds[$code]]);
        }
    }

    private function bindCatalog(
        \PDOStatement $statement,
        string $code,
        string $name,
        string $description,
        bool $active
    ): void {
        $statement->bindValue('code', $code);
        $statement->bindValue('name', $name);
        $statement->bindValue('description', $description);
        $statement->bindValue('active', $active, PDO::PARAM_BOOL);
    }
}
