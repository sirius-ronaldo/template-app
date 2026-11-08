<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\MiddlewareInterface;
use App\Service\AdminChecker;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;

final class AdminMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionManager $sessions,
        private readonly AdminChecker $admins
    ) {
    }

    public function process(Request $request, callable $next): Response
    {
        $credentials = $this->sessions->credentials();
        if ($credentials === null || !$this->admins->isAdmin($credentials['user_id'])) {
            return Response::html('Acesso negado.', 403);
        }
        return $next($request);
    }
}
