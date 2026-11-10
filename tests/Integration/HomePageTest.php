<?php

declare(strict_types=1);

namespace Tests\Integration;

final class HomePageTest extends HttpTestCase
{
    public function testHomePageLoadsWithDatabaseDiagnostics(): void
    {
        $response = $this->request('GET', '/');

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Template App', $response['body']);
        self::assertStringContainsString('Diagnóstico do banco', $response['body']);
        self::assertStringContainsString('data-default-theme="dark"', $response['body']);
        self::assertStringContainsString('class="ui-sidebar"', $response['body']);
    }

    public function testStatusRouteReturnsFragment(): void
    {
        $response = $this->request('GET', '/status', headers: ['HX-Request' => 'true']);

        self::assertSame(200, $response['status']);
        self::assertStringContainsString('Fragmento HTMX atualizado', $response['body']);
    }
}
