<?php

declare(strict_types=1);

namespace App\Manifest;

final class PermissionDefinition
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $active
    ) {
    }
}
