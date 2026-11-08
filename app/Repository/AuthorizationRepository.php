<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

class AuthorizationRepository
{
    public function __construct(private readonly PDO $pdo)
    {
    }

    public function userIsActive(int $userId): bool
    {
        $statement = $this->pdo->prepare('SELECT seg_usu_ativo FROM seg_usuarios WHERE seg_usu_id = :user_id');
        $statement->execute(['user_id' => $userId]);
        return (bool) $statement->fetchColumn();
    }

    public function hasActiveAdminGroup(int $userId): bool
    {
        $statement = $this->pdo->prepare(
            'SELECT EXISTS (SELECT 1 FROM seg_usuarios_grupos ug '
            . 'INNER JOIN seg_grupos g ON g.seg_gru_id = ug.seg_usg_gru_id '
            . 'WHERE ug.seg_usg_usu_id = :user_id '
            . 'AND g.seg_gru_codigo = :code AND g.seg_gru_ativo = true)'
        );
        $statement->execute(['user_id' => $userId, 'code' => 'ADMIN']);
        return (bool) $statement->fetchColumn();
    }

    public function hasActiveGrant(int $userId, string $application, string $permission): bool
    {
        $statement = $this->pdo->prepare(<<<'SQL'
            SELECT EXISTS (
                SELECT 1
                FROM seg_usuarios u
                INNER JOIN seg_usuarios_grupos ug ON ug.seg_usg_usu_id = u.seg_usu_id
                INNER JOIN seg_grupos g ON g.seg_gru_id = ug.seg_usg_gru_id
                INNER JOIN seg_grupos_aplicacoes_permissoes gap ON gap.seg_gap_gru_id = g.seg_gru_id
                INNER JOIN seg_aplicacoes a ON a.seg_apl_id = gap.seg_gap_apl_id
                INNER JOIN seg_tipos_aplicacoes t ON t.seg_tap_id = a.seg_apl_tap_id
                INNER JOIN seg_permissoes p ON p.seg_per_id = gap.seg_gap_per_id
                INNER JOIN seg_tipos_aplicacoes_permissoes tp ON tp.seg_tpp_tap_id = t.seg_tap_id
                    AND tp.seg_tpp_per_id = p.seg_per_id
                WHERE u.seg_usu_id = :user_id
                    AND u.seg_usu_ativo = true
                    AND g.seg_gru_ativo = true
                    AND a.seg_apl_codigo = :application
                    AND a.seg_apl_ativo = true
                    AND t.seg_tap_ativo = true
                    AND p.seg_per_codigo = :permission
                    AND p.seg_per_ativo = true
            )
            SQL);
        $statement->execute([
            'user_id' => $userId,
            'application' => $application,
            'permission' => $permission,
        ]);
        return (bool) $statement->fetchColumn();
    }
}
