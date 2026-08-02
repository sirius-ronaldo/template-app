<?php

declare(strict_types=1);

namespace App\Support;

use FastRoute\Dispatcher;

use function FastRoute\simpleDispatcher;

final class Router
{
    /** @var array<int, array{method:string, route:string, handler:callable, middleware:string[]}> */
    private array $routes = [];

    /**
     * @param string[] $middleware
     */
    public function add(string $method, string $route, callable $handler, array $middleware = []): void
    {
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'handler' => $handler,
            'middleware' => $middleware,
        ];
    }

    /**
     * @return array{
     *     status:int,
     *     handler?:callable,
     *     middleware?:string[],
     *     vars?:array<string, mixed>
     * }
     */
    public function dispatch(Request $request): array
    {
        $routeMap = [];
        $dispatcher = simpleDispatcher(function (\FastRoute\RouteCollector $collector) use (&$routeMap): void {
            foreach ($this->routes as $index => $route) {
                $collector->addRoute($route['method'], $route['route'], $index);
                $routeMap[$index] = $route;
            }
        });

        $result = $dispatcher->dispatch($request->method(), $request->path());

        if ($result[0] !== Dispatcher::FOUND) {
            return ['status' => $result[0]];
        }

        $route = $routeMap[$result[1]];

        return [
            'status' => Dispatcher::FOUND,
            'handler' => $route['handler'],
            'middleware' => $route['middleware'],
            'vars' => $result[2],
        ];
    }
}
