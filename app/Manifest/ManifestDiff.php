<?php

declare(strict_types=1);

namespace App\Manifest;

final class ManifestDiff
{
    /**
     * @param array<string,int> $counts
     * @param array<string,list<string>> $missing
     */
    public function __construct(public readonly array $counts, public readonly array $missing)
    {
    }
}
