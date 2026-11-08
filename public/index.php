<?php

declare(strict_types=1);

use App\Application;
use App\Config\AppConfig;
use App\Support\Container;
use App\Support\Request;
use App\Support\Response;
use App\Http\ExceptionHandler;

require dirname(__DIR__) . '/bootstrap/app.php';

/** @var Container $container */
/** @var Request $request */
/** @var AppConfig $config */
$routerFactory = require dirname(__DIR__) . '/routes/web.php';
/** @var App\Support\Router $router */
$router = $routerFactory($container);
$exceptionHandler = new ExceptionHandler(
    $container->get(\App\Support\View::class),
    $container->get(\Psr\Log\LoggerInterface::class),
    $config->debug
);
(new Application($container, $router, $exceptionHandler))->handle($request)->send();
