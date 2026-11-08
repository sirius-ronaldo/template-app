<?php

declare(strict_types=1);

namespace App\Repository;

interface UserCredentialRepositoryInterface
{
    /** @return array{id:int,login:string,password_hash:string,active:bool}|null */
    public function findByLogin(string $login): ?array;

    public function isActive(int $userId): bool;

    public function updatePasswordHash(int $userId, string $passwordHash): void;
}
