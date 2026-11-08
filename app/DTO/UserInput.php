<?php

declare(strict_types=1);

namespace App\DTO;

final class UserInput
{
    public function __construct(
        public readonly string $login,
        public readonly string $name,
        public readonly string $email,
        public readonly bool $active,
        public readonly ?string $password = null,
        public readonly ?string $passwordConfirmation = null
    ) {
    }
}
