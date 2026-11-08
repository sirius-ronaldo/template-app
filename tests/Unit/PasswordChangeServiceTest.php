<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Service\PasswordChangeService;
use App\Support\PasswordHasher;
use PHPUnit\Framework\TestCase;

final class PasswordChangeServiceTest extends TestCase
{
    public function testRejectsIncorrectCurrentPassword(): void
    {
        $repository = new InMemoryPasswordRepository((new PasswordHasher())->hash('senha-atual'));
        $error = $this->service($repository)->change(1, 'incorreta', 'nova-senha', 'nova-senha');

        self::assertSame('A senha atual esta incorreta.', $error);
    }

    public function testRejectsShortPassword(): void
    {
        $repository = new InMemoryPasswordRepository((new PasswordHasher())->hash('senha-atual'));
        $error = $this->service($repository)->change(1, 'senha-atual', 'curta', 'curta');

        self::assertSame('A nova senha deve ter pelo menos 8 caracteres.', $error);
    }

    public function testRejectsMismatchedConfirmation(): void
    {
        $repository = new InMemoryPasswordRepository((new PasswordHasher())->hash('senha-atual'));
        $error = $this->service($repository)->change(1, 'senha-atual', 'nova-senha', 'outra-senha');

        self::assertSame('A confirmacao da nova senha nao confere.', $error);
    }

    public function testChangesPasswordWhenDataIsValid(): void
    {
        $repository = new InMemoryPasswordRepository((new PasswordHasher())->hash('senha-atual'));
        $error = $this->service($repository)->change(1, 'senha-atual', 'nova-senha', 'nova-senha');

        self::assertNull($error);
        self::assertNotNull($repository->hash);
        self::assertTrue((new PasswordHasher())->verify('nova-senha', $repository->hash));
    }

    private function service(InMemoryPasswordRepository $repository): PasswordChangeService
    {
        return new PasswordChangeService($repository, new PasswordHasher());
    }
}
