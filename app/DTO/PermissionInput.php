<?php

declare(strict_types=1);

namespace App\DTO;

final class PermissionInput
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $active
    ) {
    }
}
