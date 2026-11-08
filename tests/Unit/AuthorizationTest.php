<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exception\AuthorizationException;
use App\Repository\AuthorizationRepository;
use App\Service\AdminChecker;
use App\Service\Authorizer;
use PHPUnit\Framework\TestCase;

final class AuthorizationTest extends TestCase
{
    public function testDeniesInactiveUser(): void
    {
        $repository = $this->createMock(AuthorizationRepository::class);
        $repository->expects(self::once())->method('userIsActive')->with(1)->willReturn(false);

        self::assertFalse($this->authorizer($repository)->can(1, 'APP', 'VIEW'));
    }

    public function testDeniesUserWithoutGroupOrGrant(): void
    {
        $repository = $this->repository(userActive: true, admin: false, grant: false);

        self::assertFalse($this->authorizer($repository)->can(1, 'APP', 'VIEW'));
    }

    public function testAllowsCumulativeGrantFromAnyActiveGroup(): void
    {
        $repository = $this->repository(userActive: true, admin: false, grant: true);

        self::assertTrue($this->authorizer($repository)->can(1, 'APP', 'VIEW'));
    }

    public function testAllowsActiveAdmin(): void
    {
        $repository = $this->repository(userActive: true, admin: true, grant: false);

        self::assertTrue($this->authorizer($repository)->can(1, 'APP', 'VIEW'));
    }

    public function testAuthorizeThrowsWhenPermissionIsDenied(): void
    {
        $repository = $this->repository(userActive: true, admin: false, grant: false);

        $this->expectException(AuthorizationException::class);
        $this->authorizer($repository)->authorize(1, 'APP', 'VIEW');
    }

    private function authorizer(AuthorizationRepository $repository): Authorizer
    {
        return new Authorizer($repository, new AdminChecker($repository));
    }

    private function repository(bool $userActive, bool $admin, bool $grant): AuthorizationRepository
    {
        $repository = $this->createMock(AuthorizationRepository::class);
        $repository->method('userIsActive')->willReturn($userActive);
        $repository->method('hasActiveAdminGroup')->willReturn($admin);
        $repository->method('hasActiveGrant')->willReturn($grant);
        return $repository;
    }
}
