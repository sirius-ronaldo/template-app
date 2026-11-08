<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\ApplicationTypeRepository;
use App\Repository\AuthorizationRepository;
use App\Repository\PermissionRepository;
use PDO;
use PDOException;
use PHPUnit\Framework\TestCase;

final class PermissionAndApplicationTypeRepositoryTest extends TestCase
{
    private PDO $pdo;
    private PermissionRepository $permissions;
    private ApplicationTypeRepository $types;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $this->pdo = IntegrationDatabase::connection();
        $this->permissions = new PermissionRepository($this->pdo);
        $this->types = new ApplicationTypeRepository($this->pdo);
    }

    public function testPermissionCrudDuplicateAndStatus(): void
    {
        $id = $this->permissions->create('READ', 'Leitura', 'Descricao', true);

        self::assertTrue($this->permissions->codeExists('READ'));
        self::assertSame($id, (int) $this->permissions->find($id)['id']);
        $this->permissions->setActive($id, false);
        self::assertFalse((bool) $this->permissions->find($id)['active']);

        $this->expectException(PDOException::class);
        $this->permissions->create('READ', 'Outra leitura', '', true);
    }

    public function testApplicationTypeCrudDuplicateAndStatus(): void
    {
        $id = $this->types->create('WEB', 'Web', 'Descricao', true);

        self::assertTrue($this->types->codeExists('WEB'));
        $this->types->setActive($id, false);
        self::assertFalse((bool) $this->types->find($id)['active']);

        $this->expectException(PDOException::class);
        $this->types->create('WEB', 'Outro web', '', true);
    }

    public function testTypePermissionRelationIsAtomicAndCanBeRemovedWithoutRemovingGrant(): void
    {
        $typeId = $this->types->create('WEB', 'Web', '', true);
        $activeId = $this->permissions->create('READ', 'Leitura', '', true);
        $inactiveId = $this->permissions->create('WRITE', 'Escrita', '', false);

        $this->permissions->syncTypePermissions($typeId, [$activeId]);
        self::assertSame([$activeId], array_map('intval', array_column(
            $this->permissions->activeForType($typeId),
            'id'
        )));

        try {
            $this->permissions->syncTypePermissions($typeId, [$activeId, $inactiveId]);
            self::fail('Era esperada rejeicao da permissao inativa.');
        } catch (\RuntimeException $exception) {
            self::assertSame('Permissao inexistente ou inativa.', $exception->getMessage());
        }
        self::assertSame([$activeId], array_map('intval', array_column(
            $this->permissions->activeForType($typeId),
            'id'
        )));

        [$userId, $groupId] = $this->createUserAndGroup();
        $applicationId = $this->createApplication($typeId);
        $this->grant($groupId, $applicationId, $activeId);
        $authorization = new AuthorizationRepository($this->pdo);
        self::assertTrue($authorization->hasActiveGrant($userId, 'PORTAL', 'READ'));

        $this->permissions->syncTypePermissions($typeId, []);
        self::assertFalse($authorization->hasActiveGrant($userId, 'PORTAL', 'READ'));
        $grant = $this->pdo->query('SELECT COUNT(*) FROM seg_grupos_aplicacoes_permissoes')->fetchColumn();
        self::assertSame(1, (int) $grant);
    }

    /** @return array{int,int} */
    private function createUserAndGroup(): array
    {
        $user = $this->pdo->query(
            "INSERT INTO seg_usuarios (seg_usu_nome, seg_usu_login) "
            . "VALUES ('Teste', 'user-" . uniqid() . "') RETURNING seg_usu_id"
        );
        $userId = (int) $user->fetchColumn();
        $group = $this->pdo->query(
            "INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome) "
            . "VALUES ('OPERADOR', 'Operador-" . uniqid() . "') RETURNING seg_gru_id"
        );
        $groupId = (int) $group->fetchColumn();
        $membership = $this->pdo->prepare(
            'INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) VALUES (:user_id, :group_id)'
        );
        $membership->execute(['user_id' => $userId, 'group_id' => $groupId]);
        return [$userId, $groupId];
    }

    private function createApplication(int $typeId): int
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_aplicacoes (seg_apl_tap_id, seg_apl_codigo, seg_apl_nome) '
            . 'VALUES (:type_id, :code, :name) RETURNING seg_apl_id'
        );
        $statement->execute(['type_id' => $typeId, 'code' => 'PORTAL', 'name' => 'Portal']);
        return (int) $statement->fetchColumn();
    }

    private function grant(int $groupId, int $applicationId, int $permissionId): void
    {
        $statement = $this->pdo->prepare(
            'INSERT INTO seg_grupos_aplicacoes_permissoes '
            . '(seg_gap_gru_id, seg_gap_apl_id, seg_gap_per_id) '
            . 'VALUES (:group_id, :application_id, :permission_id)'
        );
        $statement->execute([
            'group_id' => $groupId,
            'application_id' => $applicationId,
            'permission_id' => $permissionId,
        ]);
    }
}
