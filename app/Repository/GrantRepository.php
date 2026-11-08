<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class GrantRepository implements GrantRepositoryInterface
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function matrix(int $groupId, string $filter): array
    {
        $where = $filter === '' ? '' : ' AND (a.seg_apl_codigo ILIKE :filter OR a.seg_apl_nome ILIKE :filter)';
        $statement = $this->pdo->prepare(<<<SQL
            SELECT t.seg_tap_id AS type_id, t.seg_tap_codigo AS type_code, t.seg_tap_nome AS type_name,
                a.seg_apl_id AS application_id, a.seg_apl_codigo AS application_code,
                a.seg_apl_nome AS application_name, p.seg_per_id AS permission_id,
                p.seg_per_codigo AS permission_code, p.seg_per_nome AS permission_name,
                (gap.seg_gap_id IS NOT NULL) AS granted
            FROM seg_tipos_aplicacoes t
            INNER JOIN seg_aplicacoes a ON a.seg_apl_tap_id = t.seg_tap_id
                AND a.seg_apl_ativo = true
            INNER JOIN seg_tipos_aplicacoes_permissoes tp ON tp.seg_tpp_tap_id = t.seg_tap_id
            INNER JOIN seg_permissoes p ON p.seg_per_id = tp.seg_tpp_per_id
                AND p.seg_per_ativo = true
            LEFT JOIN seg_grupos_aplicacoes_permissoes gap
                ON gap.seg_gap_gru_id = :group_id
                AND gap.seg_gap_apl_id = a.seg_apl_id
                AND gap.seg_gap_per_id = p.seg_per_id
            WHERE t.seg_tap_ativo = true {$where}
            ORDER BY t.seg_tap_codigo, a.seg_apl_codigo, p.seg_per_codigo
            SQL);
        $statement->bindValue('group_id', $groupId, PDO::PARAM_INT);
        if ($filter !== '') {
            $statement->bindValue('filter', '%' . $filter . '%');
        }
        $statement->execute();
        return $statement->fetchAll();
    }

    public function sync(int $groupId, array $permissionsByApplication): void
    {
        $permissionsByApplication = $this->normalize($permissionsByApplication);
        $this->pdo->beginTransaction();
        try {
            $this->validateGroup($groupId);
            $this->validateRelations($permissionsByApplication);

            $activeApplications = $this->pdo->prepare(
                'SELECT a.seg_apl_id FROM seg_aplicacoes a '
                . 'INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = a.seg_apl_tap_id '
                . 'WHERE a.seg_apl_ativo = true '
                . 'AND t.seg_tap_ativo = true'
            );
            $activeApplications->execute();
            $activeApplicationIds = array_map('intval', $activeApplications->fetchAll(PDO::FETCH_COLUMN));
            $managedApplicationIds = array_values(array_intersect(
                $activeApplicationIds,
                array_keys($permissionsByApplication)
            ));

            foreach ($managedApplicationIds as $applicationId) {
                $desired = $permissionsByApplication[$applicationId] ?? [];
                $existingStatement = $this->pdo->prepare(
                    'SELECT seg_gap_per_id FROM seg_grupos_aplicacoes_permissoes '
                    . 'WHERE seg_gap_gru_id = :group_id AND seg_gap_apl_id = :application_id'
                );
                $existingStatement->execute(['group_id' => $groupId, 'application_id' => $applicationId]);
                $existing = array_map('intval', $existingStatement->fetchAll(PDO::FETCH_COLUMN));
                $toDelete = array_diff($existing, $desired);
                $toInsert = array_diff($desired, $existing);
                foreach ($toDelete as $permissionId) {
                    $delete = $this->pdo->prepare(
                        'DELETE FROM seg_grupos_aplicacoes_permissoes WHERE seg_gap_gru_id = :group_id '
                        . 'AND seg_gap_apl_id = :application_id AND seg_gap_per_id = :permission_id'
                    );
                    $delete->execute([
                        'group_id' => $groupId,
                        'application_id' => $applicationId,
                        'permission_id' => $permissionId,
                    ]);
                }
                foreach ($toInsert as $permissionId) {
                    $insert = $this->pdo->prepare(
                        'INSERT INTO seg_grupos_aplicacoes_permissoes '
                        . '(seg_gap_gru_id, seg_gap_apl_id, seg_gap_per_id) '
                        . 'VALUES (:group_id, :application_id, :permission_id)'
                    );
                    $insert->execute([
                        'group_id' => $groupId,
                        'application_id' => $applicationId,
                        'permission_id' => $permissionId,
                    ]);
                }
            }
            $this->pdo->commit();
        } catch (\Throwable $exception) {
            if ($this->pdo->inTransaction()) {
                $this->pdo->rollBack();
            }
            throw $exception;
        }
    }

    private function validateGroup(int $groupId): void
    {
        $statement = $this->pdo->prepare('SELECT seg_gru_codigo, seg_gru_ativo FROM seg_grupos WHERE seg_gru_id = :id');
        $statement->execute(['id' => $groupId]);
        $group = $statement->fetch();
        if (!is_array($group)) {
            throw new \RuntimeException('Grupo nao encontrado.');
        }
        if ($group['seg_gru_codigo'] === 'ADMIN') {
            throw new \RuntimeException('O grupo ADMIN possui acesso irrestrito.');
        }
        if (!(bool) $group['seg_gru_ativo']) {
            throw new \RuntimeException('Grupo inexistente ou inativo.');
        }
    }

    /** @param array<int,list<int>> $permissionsByApplication */
    private function validateRelations(array $permissionsByApplication): void
    {
        foreach ($permissionsByApplication as $applicationId => $permissionIds) {
            $application = $this->pdo->prepare(
                'SELECT a.seg_apl_tap_id FROM seg_aplicacoes a '
                . 'INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = a.seg_apl_tap_id '
                . 'WHERE a.seg_apl_id = :id AND a.seg_apl_ativo = true AND t.seg_tap_ativo = true'
            );
            $application->execute(['id' => $applicationId]);
            $typeId = $application->fetchColumn();
            if ($typeId === false) {
                throw new \RuntimeException('Aplicacao inexistente ou inativa.');
            }
            foreach ($permissionIds as $permissionId) {
                $permission = $this->pdo->prepare(
                    'SELECT 1 FROM seg_permissoes p INNER JOIN seg_tipos_aplicacoes_permissoes tp '
                    . 'ON tp.seg_tpp_per_id = p.seg_per_id AND tp.seg_tpp_tap_id = :type_id '
                    . 'WHERE p.seg_per_id = :permission_id AND p.seg_per_ativo = true'
                );
                $permission->execute(['type_id' => $typeId, 'permission_id' => $permissionId]);
                if ($permission->fetchColumn() === false) {
                    throw new \RuntimeException('Permissao inexistente, inativa ou invalida para a aplicacao.');
                }
            }
        }
    }

    /**
     * @param array<int,list<int>> $permissionsByApplication
     * @return array<int,list<int>>
     */
    private function normalize(array $permissionsByApplication): array
    {
        $normalized = [];
        foreach ($permissionsByApplication as $applicationId => $permissionIds) {
            $normalized[(int) $applicationId] = array_values(array_unique(array_map('intval', $permissionIds)));
        }
        return $normalized;
    }
}
