<?php

declare(strict_types=1);

namespace App\Http;

use App\Support\Request;
use App\Support\Response;

final class MiddlewarePipeline
{
    /** @param list<MiddlewareInterface> $middleware */
    public function __construct(private readonly array $middleware)
    {
    }

    /** @param callable(Request): Response $handler */
    public function handle(Request $request, callable $handler): Response
    {
        $next = $handler;
        foreach (array_reverse($this->middleware) as $middleware) {
            $current = $next;
            $next = static fn (Request $request): Response => $middleware->process($request, $current);
        }

        return $next($request);
    }
}
