<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\MiddlewareInterface;
use App\Support\CsrfTokenManager;
use App\Support\Request;
use App\Support\Response;

final class CsrfMiddleware implements MiddlewareInterface
{
    public function __construct(private readonly CsrfTokenManager $tokens)
    {
    }

    public function process(Request $request, callable $next): Response
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return $next($request);
        }

        $token = $request->isHtmx()
            ? ($request->header('X-CSRF-Token') ?? (string) $request->input('_csrf', ''))
            : (string) $request->input('_csrf', '');

        if (!$this->tokens->validate($token)) {
            return Response::html('Requisicao invalida.', 419);
        }

        return $next($request);
    }
}
