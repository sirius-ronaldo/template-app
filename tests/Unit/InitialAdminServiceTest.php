<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exception\InitialAdminException;
use App\Repository\SecurityUserRepositoryInterface;
use App\Service\InitialAdminService;
use PDO;
use PHPUnit\Framework\TestCase;

final class InitialAdminServiceTest extends TestCase
{
    public function testCreatesAdminWithArgon2idAndAttachesItToAdminGroup(): void
    {
        $repository = new InMemorySecurityUserRepository();
        $pdo = $this->createMock(PDO::class);
        $pdo->expects(self::once())->method('beginTransaction');
        $pdo->expects(self::once())->method('commit');

        (new InitialAdminService($pdo, $repository))->create(
            'admin',
            'Administrador',
            'admin@example.com',
            'senha-segura'
        );

        self::assertNotNull($repository->passwordHash);
        self::assertStringStartsWith('$argon2id$', $repository->passwordHash);
        self::assertTrue(password_verify('senha-segura', $repository->passwordHash));
        self::assertSame([1, 10], $repository->attachment);
    }

    public function testRejectsExistingAdmin(): void
    {
        $repository = new InMemorySecurityUserRepository(adminExists: true);
        $pdo = $this->createMock(PDO::class);

        $this->expectException(InitialAdminException::class);
        (new InitialAdminService($pdo, $repository))->create('admin', 'Nome', 'a@b.com', 'senha');
    }

    public function testRejectsExistingLogin(): void
    {
        $repository = new InMemorySecurityUserRepository(loginExists: true);
        $pdo = $this->createMock(PDO::class);

        $this->expectException(InitialAdminException::class);
        (new InitialAdminService($pdo, $repository))->create('admin', 'Nome', 'a@b.com', 'senha');
    }

    public function testRejectsMissingAdminGroup(): void
    {
        $repository = new InMemorySecurityUserRepository(groupId: null);
        $pdo = $this->createMock(PDO::class);

        $this->expectException(InitialAdminException::class);
        (new InitialAdminService($pdo, $repository))->create('admin', 'Nome', 'a@b.com', 'senha');
    }
}
