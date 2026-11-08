<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\MiddlewareInterface;
use App\Service\Authorizer;
use App\Support\Request;
use App\Support\Response;
use App\Support\SessionManager;

final class AuthorizationMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly SessionManager $sessions,
        private readonly Authorizer $authorizer
    ) {
    }

    public function process(Request $request, callable $next): Response
    {
        $credentials = $this->sessions->credentials();
        $metadata = $request->attribute('route_metadata', []);
        if ($credentials === null || !is_array($metadata)) {
            return Response::redirect('/login');
        }
        $this->authorizer->authorize(
            $credentials['user_id'],
            (string) $metadata['application'],
            (string) $metadata['permission']
        );
        return $next($request);
    }
}
