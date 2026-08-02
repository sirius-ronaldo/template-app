<?php

declare(strict_types=1);

use App\Exception\AuthorizationException;
use App\Exception\NotFoundException;
use App\Support\Container;
use App\Support\Request;
use App\Support\Response;
use App\Support\Router;
use App\Support\View;
use FastRoute\Dispatcher;

require dirname(__DIR__) . '/bootstrap/app.php';

/** @var Container $container */
/** @var Request $request */
$routerFactory = require dirname(__DIR__) . '/routes/web.php';
/** @var Router $router */
$router = $routerFactory($container);

set_exception_handler(static function (Throwable $exception) use ($container): void {
    $debug = filter_var($_ENV['APP_DEBUG'] ?? false, FILTER_VALIDATE_BOOL);
    error_log($exception->__toString());

    $view = $container->get(View::class);
    $status = 500;
    $template = 'errors/500';

    if ($exception instanceof NotFoundException) {
        $status = 404;
        $template = 'errors/404';
    }

    if ($exception instanceof AuthorizationException) {
        $status = 403;
        $template = 'errors/403';
    }

    $message = $debug ? $exception->getMessage() : null;
    Response::html($view->render($template, ['message' => $message]), $status)->send();
});

$route = $router->dispatch($request);

if (($route['status'] ?? null) === Dispatcher::NOT_FOUND) {
    throw new NotFoundException('Pagina nao encontrada.');
}

if (($route['status'] ?? null) === Dispatcher::METHOD_NOT_ALLOWED) {
    Response::html('Metodo nao permitido.', 405)->send();

    return;
}

foreach ($route['middleware'] as $middlewareClass) {
    $response = $container->get($middlewareClass)->handle($request);

    if ($response instanceof Response) {
        $response->send();

        return;
    }
}

$handler = $route['handler'];
$vars = $route['vars'];
$normalizedVars = array_map(
    static fn (mixed $value): mixed => is_string($value) && ctype_digit($value) ? (int) $value : $value,
    array_values($vars)
);
$response = $handler($request, ...$normalizedVars);

if (!$response instanceof Response) {
    throw new RuntimeException('Handler deve retornar uma Response.');
}

$response->send();
