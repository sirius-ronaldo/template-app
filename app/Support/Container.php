<?php

declare(strict_types=1);

namespace App\Support;

use Closure;
use RuntimeException;

final class Container
{
    /** @var array<string, Closure(self): mixed> */
    private array $bindings = [];

    /** @var array<string, mixed> */
    private array $instances = [];

    public function singleton(string $id, Closure $factory): void
    {
        $this->bindings[$id] = $factory;
    }

    public function get(string $id): mixed
    {
        if (array_key_exists($id, $this->instances)) {
            return $this->instances[$id];
        }

        if (!array_key_exists($id, $this->bindings)) {
            throw new RuntimeException(sprintf('Servico "%s" nao encontrado.', $id));
        }

        return $this->instances[$id] = $this->bindings[$id]($this);
    }
}
