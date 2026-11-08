<?php

declare(strict_types=1);

namespace App\Auth;

final class AuthenticatedUser
{
    public function __construct(public readonly int $id, public readonly string $login)
    {
    }
}
