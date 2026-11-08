<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\GroupInput;
use App\Service\GroupManagementService;
use App\Validator\GroupValidator;
use PHPUnit\Framework\TestCase;

final class GroupManagementServiceTest extends TestCase
{
    public function testNormalizesCodeAndValidatesRegex(): void
    {
        $repository = new InMemoryGroupRepository();
        $service = new GroupManagementService($repository, new GroupValidator());

        self::assertSame([], $service->create(new GroupInput(' operadores ', 'Operadores', '', true)));
        self::assertSame('OPERADORES', $repository->records[1]['code']);
        self::assertArrayHasKey('code', $service->create(new GroupInput('1-invalido', 'Nome', '', true)));
    }

    public function testProtectsAdminFromDeactivateAndDelete(): void
    {
        $repository = new InMemoryGroupRepository();
        $repository->records[1] = [
            'id' => 1,
            'code' => 'ADMIN',
            'name' => 'Administradores',
            'description' => '',
            'active' => true,
        ];
        $service = new GroupManagementService($repository, new GroupValidator());

        self::assertArrayHasKey('global', $service->deactivate(1));
        self::assertArrayHasKey('global', $service->delete(1));
        self::assertSame([], $service->update(1, new GroupInput('outro', 'Admins', 'Descricao', false)));
        self::assertSame('ADMIN', $repository->records[1]['code']);
    }

    public function testSynchronizesMultipleGroupsAndRemovesDeselectedGroups(): void
    {
        $repository = new InMemoryGroupRepository();
        $repository->records = [
            1 => ['id' => 1, 'code' => 'UM', 'name' => 'Um', 'description' => '', 'active' => true],
            2 => ['id' => 2, 'code' => 'DOIS', 'name' => 'Dois', 'description' => '', 'active' => true],
        ];
        $service = new GroupManagementService($repository, new GroupValidator());

        self::assertSame([], $service->syncUserGroups(10, [1, 2]));
        self::assertSame([], $service->syncUserGroups(10, [2]));
        self::assertSame([2], $repository->memberships[10]);
    }
}
