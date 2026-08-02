<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\StatusController;
use App\Support\Container;
use App\Support\Request;
use App\Support\Response;
use App\Support\Router;

return static function (Container $container): Router {
    $router = new Router();

    $router->add('GET', '/', static fn (Request $request): Response => $container->get(HomeController::class)->index());
    $router->add('GET', '/status', static fn (Request $request): Response => $container->get(StatusController::class)->show());

    return $router;
};
