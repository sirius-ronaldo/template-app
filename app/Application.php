<?php

declare(strict_types=1);

namespace App;

use App\Http\ExceptionHandler;
use App\Support\Container;
use App\Support\Request;
use App\Support\Response;
use App\Support\Router;
use FastRoute\Dispatcher;
use Throwable;

final class Application
{
    public function __construct(
        private readonly Container $container,
        private readonly Router $router,
        private readonly ExceptionHandler $exceptions
    ) {
    }

    public function handle(Request $request): Response
    {
        try {
            $route = $this->router->dispatch($request);
            if ($route['status'] === Dispatcher::NOT_FOUND) {
                throw new \App\Exception\NotFoundException('Pagina nao encontrada.');
            }
            if ($route['status'] === Dispatcher::METHOD_NOT_ALLOWED) {
                return Response::html('Metodo nao permitido.', 405);
            }

            $handler = $route['handler'];
            $vars = array_map(
                static fn (mixed $value): mixed => is_string($value) && ctype_digit($value) ? (int) $value : $value,
                array_values($route['vars'] ?? [])
            );
            $pipeline = new \App\Http\MiddlewarePipeline(array_map(
                fn (string $class): \App\Http\MiddlewareInterface => $this->container->get($class),
                $route['middleware'] ?? []
            ));

            return $pipeline->handle($request, fn (Request $request): Response => $handler($request, ...$vars));
        } catch (Throwable $exception) {
            return $this->exceptions->render($exception);
        }
    }
}
