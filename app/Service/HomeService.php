<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\DatabaseDiagnosticsRepository;

final class HomeService
{
    public function __construct(private readonly DatabaseDiagnosticsRepository $repository)
    {
    }

    /**
     * @return array{
     *     database:string,
     *     user:string,
     *     server_version:string,
     *     now:string
     * }
     */
    public function diagnostics(): array
    {
        return $this->repository->fetch();
    }
}
