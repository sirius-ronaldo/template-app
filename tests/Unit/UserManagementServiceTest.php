<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\UserInput;
use App\Repository\AuthorizationRepository;
use App\Service\AdminChecker;
use App\Service\UserManagementService;
use App\Support\Pagination;
use App\Support\PasswordHasher;
use App\Validator\UserValidator;
use PHPUnit\Framework\TestCase;

final class UserManagementServiceTest extends TestCase
{
    public function testCreatesUserWithArgon2id(): void
    {
        $repository = new InMemoryUserRepository();
        $errors = $this->service($repository)->create(new UserInput(
            'novo',
            'Novo Usuario',
            'novo@example.com',
            true,
            'senha-segura',
            'senha-segura'
        ));

        self::assertSame([], $errors);
        self::assertNotNull($repository->passwordHash);
        self::assertStringStartsWith('$argon2id$', $repository->passwordHash);
        self::assertTrue((new PasswordHasher())->verify('senha-segura', $repository->passwordHash));
    }

    public function testRejectsDuplicateLoginAndInvalidEmail(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->records[1] = ['id' => 1, 'login' => 'admin', 'active' => true];
        $service = $this->service($repository);

        $duplicate = $service->create(new UserInput(
            'admin',
            'Nome',
            'nome@example.com',
            true,
            'senha-segura',
            'senha-segura'
        ));
        $invalidEmail = $service->create(new UserInput(
            'novo',
            'Nome',
            'invalido',
            true,
            'senha-segura',
            'senha-segura'
        ));

        self::assertSame('O login informado ja existe.', $duplicate['login']);
        self::assertSame('Informe um e-mail valido.', $invalidEmail['email']);
    }

    public function testRejectsSelfDeactivateAndSelfDeleteForAdmin(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->records[1] = ['id' => 1, 'login' => 'admin', 'active' => true];
        $service = $this->service($repository, true);

        self::assertArrayHasKey('global', $service->deactivate(1, 1));
        self::assertArrayHasKey('global', $service->delete(1, 1));
    }

    public function testResetsPasswordSeparatelyFromProfileUpdate(): void
    {
        $repository = new InMemoryUserRepository();
        $repository->records[1] = ['id' => 1, 'login' => 'user', 'active' => true];
        $errors = $this->service($repository)->resetPassword(1, 'nova-senha', 'nova-senha');

        self::assertSame([], $errors);
        self::assertNotNull($repository->passwordHash);
        self::assertTrue((new PasswordHasher())->verify('nova-senha', $repository->passwordHash));
    }

    private function service(InMemoryUserRepository $repository, bool $admin = false): UserManagementService
    {
        $authorization = $this->createMock(AuthorizationRepository::class);
        $authorization->method('userIsActive')->willReturn(true);
        $authorization->method('hasActiveAdminGroup')->willReturn($admin);
        return new UserManagementService(
            $repository,
            new PasswordHasher(),
            new UserValidator(),
            new AdminChecker($authorization)
        );
    }
}
