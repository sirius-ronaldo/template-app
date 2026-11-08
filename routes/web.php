<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\PasswordController;
use App\Controller\StatusController;
use App\Support\Container;
use App\Support\Request;
use App\Support\Response;
use App\Support\Router;

return static function (Container $container): Router {
    $router = new Router();

    $router->add('GET', '/', static fn (Request $request): Response => $container->get(HomeController::class)->index());
    $router->add('GET', '/Home', static fn (Request $request): Response => $container->get(HomeController::class)->index(), metadata: ['auth' => true]);
    $router->add('GET', '/login', static fn (Request $request): Response => $container->get(LoginController::class)->form());
    $router->add('POST', '/login', static fn (Request $request): Response => $container->get(LoginController::class)->login($request), [CsrfMiddleware::class]);
    $router->add('POST', '/logout', static fn (Request $request): Response => $container->get(LogoutController::class)->logout(), metadata: ['auth' => true]);
    $router->add('GET', '/minha-senha', static fn (Request $request): Response => $container->get(PasswordController::class)->form(), metadata: ['auth' => true]);
    $router->add('POST', '/minha-senha', static fn (Request $request): Response => $container->get(PasswordController::class)->change($request), metadata: ['auth' => true]);
    $router->add('GET', '/status', static fn (Request $request): Response => $container->get(StatusController::class)->show());

    return $router;
};
