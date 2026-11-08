<?php

declare(strict_types=1);

namespace App\Http;

use App\Support\Request;
use App\Support\Response;

interface MiddlewareInterface
{
    /** @param callable(Request): Response $next */
    public function process(Request $request, callable $next): Response;
}
