<?php

declare(strict_types=1);

namespace App\Manifest;

final class ApplicationTypeDefinition
{
    /** @param list<string> $permissions */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $description,
        public readonly bool $active,
        public readonly array $permissions
    ) {
    }
}
