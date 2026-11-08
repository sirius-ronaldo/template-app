<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Middleware\CsrfMiddleware;
use App\Support\CsrfTokenManager;
use App\Support\Request;
use App\Support\Response;
use App\Support\Session;
use PHPUnit\Framework\TestCase;

final class CsrfTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testGeneratesAndValidatesSessionToken(): void
    {
        $manager = new CsrfTokenManager(new Session());
        $token = $manager->token();

        self::assertSame(64, strlen($token));
        self::assertTrue($manager->validate($token));
        self::assertFalse($manager->validate('invalid'));
    }

    public function testMiddlewareRejectsMutationWithoutValidToken(): void
    {
        $manager = new CsrfTokenManager(new Session());
        $middleware = new CsrfMiddleware($manager);
        $request = new Request('POST', '/mutacao', [], [], [], []);

        $response = $middleware->process($request, static fn (): Response => Response::html('ok'));

        self::assertSame(419, $response->status());
    }

    public function testMiddlewareDoesNotProtectGet(): void
    {
        $middleware = new CsrfMiddleware(new CsrfTokenManager(new Session()));
        $request = new Request('GET', '/consulta', [], [], [], []);

        $response = $middleware->process($request, static fn (): Response => Response::html('ok'));

        self::assertSame(200, $response->status());
    }
}
