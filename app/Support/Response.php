<?php

declare(strict_types=1);

namespace App\Support;

final class Response
{
    /**
     * @param array<string, string> $headers
     */
    public function __construct(
        private readonly string $body,
        private readonly int $status = 200,
        private readonly array $headers = ['Content-Type' => 'text/html; charset=UTF-8']
    ) {
    }

    /**
     * @param array<string, string> $headers
     */
    public static function html(string $body, int $status = 200, array $headers = []): self
    {
        return new self($body, $status, $headers + ['Content-Type' => 'text/html; charset=UTF-8']);
    }

    /**
     * @param array<string, string> $headers
     */
    public static function redirect(string $location, int $status = 302, array $headers = []): self
    {
        return new self('', $status, $headers + ['Location' => $location]);
    }

    public static function download(string $body, string $filename, string $contentType): self
    {
        $dispositionName = str_replace(['"', "\r", "\n"], '', $filename);

        return new self($body, 200, [
            'Content-Type' => $contentType,
            'Content-Length' => (string) strlen($body),
            'Content-Disposition' => 'attachment; filename="' . $dispositionName . '"',
        ]);
    }

    public function send(): void
    {
        http_response_code($this->status);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }

        echo $this->body;
    }
}
