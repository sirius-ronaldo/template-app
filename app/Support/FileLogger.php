<?php

declare(strict_types=1);

namespace App\Support;

use Psr\Log\AbstractLogger;
use Stringable;

final class FileLogger extends AbstractLogger
{
    public function __construct(private readonly string $path)
    {
    }

    /** @param array<string, mixed> $context */
    public function log($level, Stringable|string $message, array $context = []): void
    {
        $directory = dirname($this->path);
        if (!is_dir($directory)) {
            mkdir($directory, 0775, true);
        }

        $record = [
            'timestamp' => date(DATE_ATOM),
            'level' => (string) $level,
            'message' => (string) $message,
            'context' => $context,
        ];

        file_put_contents(
            $this->path,
            json_encode($record, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL,
            FILE_APPEND | LOCK_EX
        );
    }
}
