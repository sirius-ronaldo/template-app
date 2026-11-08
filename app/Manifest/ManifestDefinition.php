<?php

declare(strict_types=1);

namespace App\Manifest;

final class ManifestDefinition
{
    /**
     * @param list<PermissionDefinition> $permissions
     * @param list<ApplicationTypeDefinition> $types
     * @param list<ApplicationDefinition> $applications
     */
    public function __construct(
        public readonly int $version,
        public readonly array $permissions,
        public readonly array $types,
        public readonly array $applications
    ) {
    }
}
