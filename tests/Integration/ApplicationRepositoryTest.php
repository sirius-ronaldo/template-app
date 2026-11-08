<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\ApplicationRepository;
use App\Repository\ApplicationTypeRepository;
use App\Repository\AuthorizationRepository;
use PDOException;
use PHPUnit\Framework\TestCase;

final class ApplicationRepositoryTest extends TestCase
{
    private ApplicationRepository $applications;
    private ApplicationTypeRepository $types;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $pdo = IntegrationDatabase::connection();
        $this->applications = new ApplicationRepository($pdo);
        $this->types = new ApplicationTypeRepository($pdo);
    }

    public function testCrudEffectiveStateAndTypeChange(): void
    {
        $activeType = $this->types->create('WEB', 'Web', '', true);
        $inactiveType = $this->types->create('LEGACY', 'Legacy', '', false);
        $id = $this->applications->create($activeType, 'PORTAL', 'Portal', '', true);

        $item = $this->applications->find($id);
        self::assertTrue((bool) $item['active']);
        self::assertTrue((bool) $item['effective_active']);

        $this->applications->update($id, $inactiveType, 'PORTAL', 'Portal alterado', '', true);
        self::assertFalse((bool) $this->applications->find($id)['effective_active']);

        $this->types->setActive($inactiveType, true);
        self::assertTrue((bool) $this->applications->find($id)['effective_active']);
        $this->applications->setActive($id, false);
        self::assertFalse((bool) $this->applications->find($id)['effective_active']);
    }

    public function testAuthorizationStopsGrantWhenApplicationIsInactive(): void
    {
        $typeId = $this->types->create('WEB', 'Web', '', true);
        $applicationId = $this->applications->create($typeId, 'PORTAL', 'Portal', '', true);
        $permission = IntegrationDatabase::connection()->query(
            "INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome) VALUES ('READ', 'Leitura') RETURNING seg_per_id"
        );
        $permissionId = (int) $permission->fetchColumn();
        $pdo = IntegrationDatabase::connection();
        $pdo->exec(
            "INSERT INTO seg_tipos_aplicacoes_permissoes "
            . "(seg_tpp_tap_id, seg_tpp_per_id) VALUES ($typeId, $permissionId)"
        );
        $user = $pdo->query(
            "INSERT INTO seg_usuarios (seg_usu_nome, seg_usu_login) "
            . "VALUES ('Teste', 'app-user-" . uniqid() . "') RETURNING seg_usu_id"
        );
        $userId = (int) $user->fetchColumn();
        $group = $pdo->query(
            "INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome) "
            . "VALUES ('OP', 'Operador') RETURNING seg_gru_id"
        );
        $groupId = (int) $group->fetchColumn();
        $pdo->exec("INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) VALUES ($userId, $groupId)");
        $pdo->exec(
            "INSERT INTO seg_grupos_aplicacoes_permissoes "
            . "(seg_gap_gru_id, seg_gap_apl_id, seg_gap_per_id) "
            . "VALUES ($groupId, $applicationId, $permissionId)"
        );
        $authorization = new AuthorizationRepository($pdo);
        self::assertTrue($authorization->hasActiveGrant($userId, 'PORTAL', 'READ'));

        $this->applications->setActive($applicationId, false);
        self::assertFalse($authorization->hasActiveGrant($userId, 'PORTAL', 'READ'));
    }

    public function testDeleteIsRejectedWhenApplicationHasGrant(): void
    {
        $typeId = $this->types->create('WEB', 'Web', '', true);
        $applicationId = $this->applications->create($typeId, 'PORTAL', 'Portal', '', true);
        $pdo = IntegrationDatabase::connection();
        $group = $pdo->query(
            "INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome) "
            . "VALUES ('OP', 'Operador') RETURNING seg_gru_id"
        );
        $groupId = (int) $group->fetchColumn();
        $permission = $pdo->query(
            "INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome) "
            . "VALUES ('READ', 'Leitura') RETURNING seg_per_id"
        );
        $permissionId = (int) $permission->fetchColumn();
        $statement = $pdo->prepare(
            'INSERT INTO seg_grupos_aplicacoes_permissoes '
            . '(seg_gap_gru_id, seg_gap_apl_id, seg_gap_per_id) VALUES (?, ?, ?)'
        );
        $statement->execute([$groupId, $applicationId, $permissionId]);

        $this->expectException(PDOException::class);
        $this->applications->delete($applicationId);
    }
}
