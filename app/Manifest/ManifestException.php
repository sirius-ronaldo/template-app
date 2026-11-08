<?php

declare(strict_types=1);

namespace App\Manifest;

use RuntimeException;

final class ManifestException extends RuntimeException
{
    /** @param list<string> $diagnostics */
    public function __construct(public readonly array $diagnostics)
    {
        parent::__construct(implode(' ', $diagnostics));
    }
}
