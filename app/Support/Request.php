<?php

declare(strict_types=1);

namespace App\Support;

final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $parsedBody
     * @param array<string, array<string, mixed>> $files
     * @param array<string, string> $server
     * @param array<string, mixed> $attributes
     */
    public function __construct(
        private readonly string $method,
        private readonly string $path,
        private readonly array $query,
        private readonly array $parsedBody,
        private readonly array $files,
        private readonly array $server,
        private readonly array $attributes = []
    ) {
    }

    public static function capture(): self
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = $_SERVER['REQUEST_URI'] ?? '/';
        $path = parse_url($uri, PHP_URL_PATH);

        return new self(
            $method,
            is_string($path) ? $path : '/',
            $_GET,
            $_POST,
            $_FILES,
            array_map(
                static fn (mixed $value): string => is_scalar($value) ? (string) $value : '',
                $_SERVER
            )
        );
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function input(string $key, mixed $default = null): mixed
    {
        return $this->parsedBody[$key] ?? $this->query[$key] ?? $default;
    }

    /**
     * @return array<string, mixed>
     */
    public function all(): array
    {
        return array_merge($this->query, $this->parsedBody);
    }

    /**
     * @return array<string, mixed>
     */
    public function queryParams(): array
    {
        return $this->query;
    }

    public function uploadedFile(string $key): ?UploadedFile
    {
        $file = $this->files[$key] ?? null;
        if (!is_array($file)) {
            return null;
        }

        return UploadedFile::fromPhpArray($file);
    }

    public function header(string $name): ?string
    {
        $key = 'HTTP_' . strtoupper(str_replace('-', '_', $name));

        return $this->server[$key] ?? null;
    }

    public function isHtmx(): bool
    {
        return $this->header('HX-Request') === 'true';
    }

    public function attribute(string $key, mixed $default = null): mixed
    {
        return $this->attributes[$key] ?? $default;
    }

    /** @param array<string, mixed> $attributes */
    public function withAttributes(array $attributes): self
    {
        return new self(
            $this->method,
            $this->path,
            $this->query,
            $this->parsedBody,
            $this->files,
            $this->server,
            $attributes + $this->attributes
        );
    }
}
