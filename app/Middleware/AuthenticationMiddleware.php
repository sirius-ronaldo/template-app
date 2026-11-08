<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\MiddlewareInterface;
use App\Repository\UserCredentialRepositoryInterface;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;

final class AuthenticationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionManager $sessions,
        private readonly UserCredentialRepositoryInterface $users
    ) {
    }

    public function handle(Request $request): ?Response
    {
        $credentials = $this->sessions->credentials();
        if ($credentials === null || $this->sessions->isExpired() || !$this->users->isActive($credentials['user_id'])) {
            if ($credentials !== null) {
                $this->sessions->logout();
            }
            return $request->isHtmx()
                ? Response::html('', 401, ['HX-Redirect' => '/login'])
                : Response::redirect('/login');
        }
        $this->sessions->touch();
        return null;
    }

    public function process(Request $request, callable $next): Response
    {
        $response = $this->handle($request);
        return $response ?? $next($request);
    }
}
