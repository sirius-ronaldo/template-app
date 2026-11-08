<?php

declare(strict_types=1);

namespace App\Manifest;

final class ManifestSnapshot
{
    /**
     * @param array<string,array<string,mixed>> $permissions
     * @param array<string,array<string,mixed>> $types
     * @param array<string,array<string,mixed>> $applications
     * @param array<string,list<string>> $relations
     */
    public function __construct(
        public readonly array $permissions,
        public readonly array $types,
        public readonly array $applications,
        public readonly array $relations
    ) {
    }
}
