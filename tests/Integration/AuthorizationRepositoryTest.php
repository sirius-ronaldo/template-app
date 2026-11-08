<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\AuthorizationRepository;
use PDO;

final class AuthorizationRepositoryTest extends \PHPUnit\Framework\TestCase
{
    private PDO $pdo;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $this->pdo = IntegrationDatabase::connection();
    }

    public function testGrantFromActiveGroupAllowsPermission(): void
    {
        [$userId, $groupId] = $this->createUserAndGroup();
        $this->createApplicationPermissionGrant($groupId);

        self::assertTrue((new AuthorizationRepository($this->pdo))->hasActiveGrant($userId, 'PORTAL', 'READ'));
    }

    public function testNoGroupOrGrantDeniesPermission(): void
    {
        [$userId] = $this->createUserAndGroup();

        self::assertFalse((new AuthorizationRepository($this->pdo))->hasActiveGrant($userId, 'PORTAL', 'READ'));
    }

    public function testInactiveApplicationAndIncompatiblePermissionDenyPermission(): void
    {
        [$userId, $groupId] = $this->createUserAndGroup();
        $this->createApplicationPermissionGrant($groupId, applicationActive: false);

        $repository = new AuthorizationRepository($this->pdo);
        self::assertFalse($repository->hasActiveGrant($userId, 'PORTAL', 'READ'));
        self::assertFalse($repository->hasActiveGrant($userId, 'PORTAL', 'WRITE'));
    }

    public function testActiveAdminGroupIsRecognized(): void
    {
        [$userId, $groupId] = $this->createUserAndGroup('ADMIN');

        $repository = new AuthorizationRepository($this->pdo);
        self::assertTrue($repository->userIsActive($userId));
        self::assertTrue($repository->hasActiveAdminGroup($userId));
        self::assertGreaterThan(0, $groupId);
    }

    /** @return array{int, int} */
    private function createUserAndGroup(string $groupCode = 'OPERADOR'): array
    {
        $user = $this->pdo->prepare(
            "INSERT INTO seg_usuarios (seg_usu_nome, seg_usu_login, "
            . "seg_usu_senha_hash) VALUES ('Teste', :login, 'hash') RETURNING seg_usu_id"
        );
        $user->execute(['login' => 'user-' . uniqid()]);
        $userId = (int) $user->fetchColumn();

        if ($groupCode === 'ADMIN') {
            $group = $this->pdo->query("SELECT seg_gru_id FROM seg_grupos WHERE seg_gru_codigo = 'ADMIN'");
            $groupId = (int) $group->fetchColumn();
        } else {
            $group = $this->pdo->prepare(
                'INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome) VALUES (:code, :name) RETURNING seg_gru_id'
            );
            $group->execute(['code' => $groupCode, 'name' => 'Grupo ' . $groupCode . '-' . uniqid()]);
            $groupId = (int) $group->fetchColumn();
        }

        $membership = $this->pdo->prepare(
            'INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) VALUES (:user_id, :group_id)'
        );
        $membership->execute(['user_id' => $userId, 'group_id' => $groupId]);

        return [$userId, $groupId];
    }

    private function createApplicationPermissionGrant(int $groupId, bool $applicationActive = true): void
    {
        $type = $this->pdo->query(
            "INSERT INTO seg_tipos_aplicacoes (seg_tap_codigo, seg_tap_nome) VALUES ('WEB', 'Web') RETURNING seg_tap_id"
        );
        $typeId = (int) $type->fetchColumn();
        $application = $this->pdo->prepare(
            'INSERT INTO seg_aplicacoes (seg_apl_tap_id, seg_apl_codigo, '
            . 'seg_apl_nome, seg_apl_ativo) VALUES (:type_id, :code, :name, :active) '
            . 'RETURNING seg_apl_id'
        );
        $application->bindValue(':type_id', $typeId, PDO::PARAM_INT);
        $application->bindValue(':code', 'PORTAL');
        $application->bindValue(':name', 'Portal');
        $application->bindValue(':active', $applicationActive, PDO::PARAM_BOOL);
        $application->execute();
        $applicationId = (int) $application->fetchColumn();

        $permission = $this->pdo->query(
            "INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome) VALUES ('READ', 'Ler') RETURNING seg_per_id"
        );
        $permissionId = (int) $permission->fetchColumn();
        $valid = $this->pdo->prepare(
            'INSERT INTO seg_tipos_aplicacoes_permissoes '
            . '(seg_tpp_tap_id, seg_tpp_per_id) VALUES (:type_id, :permission_id)'
        );
        $valid->execute(['type_id' => $typeId, 'permission_id' => $permissionId]);

        $grant = $this->pdo->prepare(
            'INSERT INTO seg_grupos_aplicacoes_permissoes '
            . '(seg_gap_gru_id, seg_gap_apl_id, seg_gap_per_id) '
            . 'VALUES (:group_id, :application_id, :permission_id)'
        );
        $grant->execute([
            'group_id' => $groupId,
            'application_id' => $applicationId,
            'permission_id' => $permissionId,
        ]);
    }
}
