<?php

declare(strict_types=1);

namespace App\Support;

use FastRoute\Dispatcher;

use function FastRoute\simpleDispatcher;

final class Router
{
    /** @var array<int, array{method:string, route:string, handler:callable, middleware:string[], metadata:array<string,mixed>}> */
    private array $routes = [];

    /**
     * @param string[] $middleware
     * @param array<string, mixed> $metadata
     */
    public function add(
        string $method,
        string $route,
        callable $handler,
        array $middleware = [],
        array $metadata = []
    ): void {
        $metadata = $this->validateMetadata($metadata);
        $this->routes[] = [
            'method' => strtoupper($method),
            'route' => $route,
            'handler' => $handler,
            'middleware' => $middleware,
            'metadata' => $metadata,
        ];
    }

    /**
     * @return array{
     *     status:int,
     *     handler?:callable,
     *     middleware?:string[],
     *     vars?:array<string, mixed>,
     *     allowed?:list<string>,
     *     metadata?:array<string, mixed>
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
            return [
                'status' => $result[0],
                'allowed' => $result[0] === Dispatcher::METHOD_NOT_ALLOWED ? $result[1] : [],
            ];
        }

        $route = $routeMap[$result[1]];

        return [
            'status' => Dispatcher::FOUND,
            'handler' => $route['handler'],
            'middleware' => $route['middleware'],
            'vars' => $result[2],
            'metadata' => $route['metadata'],
        ];
    }

    /**
     * @param array<string, mixed> $metadata
     * @return array<string, mixed>
     */
    private function validateMetadata(array $metadata): array
    {
        foreach (['auth', 'admin'] as $key) {
            if (isset($metadata[$key]) && !is_bool($metadata[$key])) {
                throw new \InvalidArgumentException(sprintf('Metadata "%s" deve ser booleana.', $key));
            }
        }
        foreach (['application', 'permission'] as $key) {
            if (isset($metadata[$key]) && (!is_string($metadata[$key]) || $metadata[$key] === '')) {
                throw new \InvalidArgumentException(sprintf('Metadata "%s" deve ser uma string nao vazia.', $key));
            }
        }
        $hasApplication = array_key_exists('application', $metadata);
        $hasPermission = array_key_exists('permission', $metadata);
        if ($hasApplication xor $hasPermission) {
            throw new \InvalidArgumentException('Metadata application e permission devem ser informadas juntas.');
        }
        if (($metadata['admin'] ?? false) === true) {
            $metadata['auth'] = true;
        }
        if ($hasApplication && $hasPermission) {
            $metadata['auth'] = true;
        }

        return $metadata;
    }
}
