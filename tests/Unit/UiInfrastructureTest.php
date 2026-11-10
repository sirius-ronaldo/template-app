<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Repository\AuthorizationRepository;
use App\Service\AdminChecker;
use App\Support\Csrf;
use App\Support\FlashBag;
use App\Support\Pagination;
use App\Support\Session;
use App\Support\View;
use App\Support\ViewRenderer;
use PHPUnit\Framework\TestCase;

final class UiInfrastructureTest extends TestCase
{
    protected function setUp(): void
    {
        $_SESSION = [];
    }

    public function testRendererAppliesLayoutAndConsumesFlash(): void
    {
        $flash = new FlashBag(new Session());
        $flash->add('success', 'Operacao concluida.');
        $renderer = new ViewRenderer(dirname(__DIR__, 2) . '/resources/views', $flash);
        $pagination = new Pagination(1, 25, 1);

        $html = $renderer->render('admin/security/index', [
            'items' => ['Usuarios'],
            'filter' => '',
            'pagination' => $pagination,
            'query' => [],
        ], 'layouts/app');

        self::assertStringContainsString('Template App', $html);
        self::assertStringContainsString('Operacao concluida.', $html);
        self::assertStringContainsString('Usuarios', $html);
        self::assertStringContainsString('data-theme-toggle', $html);
        self::assertStringContainsString('data-sidebar-open', $html);
        self::assertStringContainsString('role="alert"', $html);
        self::assertNull($flash->pull('success'));
    }

    public function testPaginationUsesDefaultAndMaximumPageSize(): void
    {
        $default = Pagination::fromQuery([], 101);
        $maximum = Pagination::fromQuery(['page' => 2, 'per_page' => 500, 'filter' => 'ativo'], 300);

        self::assertSame(25, $default->perPage);
        self::assertSame(100, $maximum->perPage);
        self::assertSame(100, $maximum->offset());
        self::assertSame(
            ['filter' => 'ativo', 'page' => '3', 'per_page' => '100'],
            $maximum->queryForPage(3, ['filter' => 'ativo'])
        );
    }

    public function testAdminMenuIsVisibleOnlyForAdmin(): void
    {
        $repository = $this->createMock(AuthorizationRepository::class);
        $repository->method('userIsActive')->willReturn(true);
        $repository->method('hasActiveAdminGroup')->willReturn(true);
        $admins = new AdminChecker($repository);
        $session = new Session();
        $session->put('auth', ['user_id' => 1]);
        $view = new View(dirname(__DIR__, 2) . '/app/View', $session, new Csrf($session), $admins);

        $html = $view->render('layouts/app', ['content' => 'conteudo']);

        self::assertStringContainsString('/configuracoes/seguranca', $html);
        self::assertStringContainsString('aria-controls="admin-navigation"', $html);
        self::assertStringContainsString('method="post" action="/logout"', $html);
    }
}
