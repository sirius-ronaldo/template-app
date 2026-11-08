<?php

declare(strict_types=1);

namespace App\Manifest;

final class ApplicationDefinition
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly string $description,
        public readonly string $type,
        public readonly bool $active
    ) {
    }
}
