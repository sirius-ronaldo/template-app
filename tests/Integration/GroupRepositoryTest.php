<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class GroupRepositoryTest extends TestCase
{
    private PDO $pdo;
    private GroupRepository $groups;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $this->pdo = IntegrationDatabase::connection();
        $this->groups = new GroupRepository($this->pdo);
    }

    public function testCrudAndAdminProtectionData(): void
    {
        $id = $this->groups->create('OPERADOR', 'Operador', 'Descricao', true);
        self::assertSame('OPERADOR', $this->groups->find($id)['code']);
        $this->groups->update($id, 'OPERADOR', 'Operador atualizado', 'Nova', false);
        self::assertFalse((bool) $this->groups->find($id)['active']);
        $this->groups->setActive($id, true);
        $this->groups->delete($id);
        self::assertNull($this->groups->find($id));
    }

    public function testAssociationsAreAddedRemovedAndRolledBackForInactiveGroup(): void
    {
        $users = new UserRepository($this->pdo);
        $userId = $users->create('user', 'Usuario', 'u@example.com', true, 'hash');
        $first = $this->groups->create('UM', 'Um', '', true);
        $second = $this->groups->create('DOIS', 'Dois', '', true);
        $inactive = $this->groups->create('INATIVO', 'Inativo', '', false);

        $this->groups->syncUserGroups($userId, [$first, $second]);
        self::assertCount(2, $this->groups->userGroups($userId));
        $this->groups->syncUserGroups($userId, [$second]);
        self::assertSame([$second], array_map('intval', array_column($this->groups->userGroups($userId), 'id')));

        try {
            $this->groups->syncUserGroups($userId, [$first, $inactive]);
            self::fail('Era esperada rejeicao de grupo inativo.');
        } catch (\RuntimeException $exception) {
            self::assertSame('Grupo inexistente ou inativo.', $exception->getMessage());
        }
        self::assertSame([$second], array_map('intval', array_column($this->groups->userGroups($userId), 'id')));
    }
}
