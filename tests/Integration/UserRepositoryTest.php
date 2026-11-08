<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Repository\UserRepository;
use PDO;
use PHPUnit\Framework\TestCase;

final class UserRepositoryTest extends TestCase
{
    private PDO $pdo;
    private UserRepository $repository;

    protected function setUp(): void
    {
        IntegrationDatabase::reset();
        $this->pdo = IntegrationDatabase::connection();
        $this->repository = new UserRepository($this->pdo);
    }

    public function testCrudAndCaseSensitiveLogin(): void
    {
        $id = $this->repository->create('Admin', 'Nome', 'nome@example.com', true, 'hash');
        self::assertTrue($this->repository->loginExists('Admin'));
        self::assertFalse($this->repository->loginExists('admin'));
        self::assertSame('Nome', $this->repository->find($id)['name']);

        $this->repository->update($id, 'Admin', 'Nome Atualizado', 'outro@example.com', false);
        self::assertFalse((bool) $this->repository->find($id)['active']);
        $this->repository->setActive($id, true);
        self::assertTrue((bool) $this->repository->find($id)['active']);
        $this->repository->delete($id);
        self::assertNull($this->repository->find($id));
    }

    public function testPaginationFilterAndPasswordUpdate(): void
    {
        $this->repository->create('um', 'Primeiro', 'um@example.com', true, 'hash');
        $id = $this->repository->create('dois', 'Segundo', 'dois@example.com', true, 'hash');

        $page = $this->repository->paginate('Segundo', 0, 25);
        $this->repository->updatePassword($id, 'novo-hash');
        $hash = $this->pdo->prepare('SELECT seg_usu_senha_hash FROM seg_usuarios WHERE seg_usu_id = :id');
        $hash->execute(['id' => $id]);

        self::assertSame(1, $page['total']);
        self::assertSame('novo-hash', $hash->fetchColumn());
    }

    public function testDeleteRespectsUserGroupIntegrity(): void
    {
        $userId = $this->repository->create('user', 'Usuario', 'user@example.com', true, 'hash');
        $group = $this->pdo->query(
            "INSERT INTO seg_grupos (seg_gru_codigo, seg_gru_nome) "
            . "VALUES ('G', 'Grupo') RETURNING seg_gru_id"
        );
        $groupId = (int) $group->fetchColumn();
        $membership = $this->pdo->prepare(
            'INSERT INTO seg_usuarios_grupos (seg_usg_usu_id, seg_usg_gru_id) '
            . 'VALUES (:user_id, :group_id)'
        );
        $membership->execute(['user_id' => $userId, 'group_id' => $groupId]);

        $this->expectException(\PDOException::class);
        $this->repository->delete($userId);
    }
}
