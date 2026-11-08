<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\AuthorizationRepository;
use App\Repository\GrantRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class GrantRepositoryTest extends TestCase
{
    private PDO $pdo;
    private GrantRepository $grants;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $this->pdo = IntegrationDatabase::connection();
        $this->grants = new GrantRepository($this->pdo);
    }

    public function testAddsAndRemovesGrantsAndKeepsGroupsCumulative(): void
    {
        [$typeId, $applicationId, $readId] = $this->createCatalog();
        $writeId = $this->createPermission('WRITE', $typeId);
        $firstGroup = $this->createGroup('OPERADOR');
        $secondGroup = $this->createGroup('SUPERVISOR');
        $userId = $this->createUser([$firstGroup, $secondGroup]);

        $this->grants->sync($firstGroup, [$applicationId => [$readId]]);
        $this->grants->sync($secondGroup, [$applicationId => [$writeId]]);
        $authorization = new AuthorizationRepository($this->pdo);
        self::assertTrue($authorization->hasActiveGrant($userId, 'PORTAL', 'READ'));
        self::assertTrue($authorization->hasActiveGrant($userId, 'PORTAL', 'WRITE'));

        $this->grants->sync($firstGroup, [$applicationId => []]);
        self::assertFalse($authorization->hasActiveGrant($userId, 'PORTAL', 'READ'));
        self::assertTrue($authorization->hasActiveGrant($userId, 'PORTAL', 'WRITE'));
    }

    public function testRejectsInvalidRelationAndRollsBack(): void
    {
        [$typeId, $applicationId, $readId] = $this->createCatalog();
        $inactivePermission = $this->createPermission('DISABLED', $typeId, false);
        $groupId = $this->createGroup('OPERADOR');
        $this->grants->sync($groupId, [$applicationId => [$readId]]);

        $this->expectExceptionMessage('Permissao inexistente, inativa ou invalida para a aplicacao.');
        try {
            $this->grants->sync($groupId, [$applicationId => [$readId, $inactivePermission]]);
        } finally {
            $rows = $this->grants->matrix($groupId, '');
            self::assertTrue((bool) $rows[0]['granted']);
            self::assertCount(1, $rows);
        }
    }

    public function testAdminCannotBeEdited(): void
    {
        $admin = (int) $this->pdo->query(
            "SELECT seg_gru_id FROM seg_grupos WHERE seg_gru_codigo = 'ADMIN'"
        )->fetchColumn();
        $this->expectExceptionMessage('O grupo ADMIN possui acesso irrestrito.');
        $this->grants->sync($admin, []);
    }

    /** @return array{int,int,int} */
    private function createCatalog(): array
    {
        $type = $this->pdo->query(
            "INSERT INTO seg_tipos_aplicacoes (seg_tap_codigo, seg_tap_nome) "
            . "VALUES ('WEB', 'Web') RETURNING seg_tap_id"
        );
        $typeId = (int) $type->fetchColumn();
        $application = $this->pdo->query(
            "INSERT INTO seg_aplicacoes (seg_apl_tap_id, seg_apl_codigo, seg_apl_nome) "
            . "VALUES ($typeId, 'PORTAL', 'Portal') RETURNING seg_apl_id"
        );
        $applicationId = (int) $application->fetchColumn();
        $readId = $this->createPermission('READ', $typeId);
        return [$typeId, $applicationId, $readId];
    }

    private function createPermission(string $code, int $typeId, bool $active = true): int
    {
        $permission = $this->pdo->prepare(
            'INSERT INTO seg_permissoes (seg_per_codigo, seg_per_nome, seg_per_ativo) '
            . 'VALUES (:code, :name, :active) RETURNING seg_per_id'
        );
        $permission->bindValue('code', $code);
        $permission->bindValue('name', $code);
        $permission->bindValue('active', $active, PDO::PARAM_BOOL);
        $permission->execute();
        $permissionId = (int) $permission->fetchColumn();
        if ($active) {
            $relation = $this->pdo->prepare(
                'INSERT INTO seg_tipos_aplicacoes_permissoes (seg_tpp_tap_id, seg_tpp_per_id) VALUES (?, ?)'
            );
            $relation->execute([$typeId, $permissionId]);
        }
        return $permissionId;
    }

    private function createGroup(string $code): int
    {
        $group = $this->pdo->prepare(
            'INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome) VALUES (:code, :name) RETURNING seg_gru_id'
        );
        $group->execute(['code' => $code, 'name' => $code . '-' . uniqid()]);
        return (int) $group->fetchColumn();
    }

    /** @param list<int> $groupIds */
    private function createUser(array $groupIds): int
    {
        $user = $this->pdo->prepare(
            'INSERT INTO seg_usuarios (seg_usu_nome, seg_usu_login) VALUES (:name, :login) RETURNING seg_usu_id'
        );
        $user->execute(['name' => 'Teste', 'login' => 'grant-' . uniqid()]);
        $userId = (int) $user->fetchColumn();
        foreach ($groupIds as $groupId) {
            $membership = $this->pdo->prepare(
                'INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) VALUES (?, ?)'
            );
            $membership->execute([$userId, $groupId]);
        }
        return $userId;
    }
}
