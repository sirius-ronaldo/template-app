<?php

declare(strict_types=1);

namespace App\Config;

use DateTimeZone;
use InvalidArgumentException;

final class AppConfig
{
    public function __construct(
        public readonly string $environment,
        public readonly bool $debug,
        public readonly string $timezone,
        public readonly string $logLevel,
        public readonly string $logPath,
        public readonly string $dbHost,
        public readonly int $dbPort,
        public readonly string $dbName,
        public readonly string $dbUser,
        public readonly string $dbPassword,
        public readonly string $dbSchema,
    ) {
    }

    /** @param array<string, mixed> $environment */
    public static function fromEnvironment(array $environment, string $rootPath): self
    {
        $timezone = (string) ($environment['APP_TIMEZONE'] ?? 'America/Bahia');
        if (!in_array($timezone, DateTimeZone::listIdentifiers(), true)) {
            throw new InvalidArgumentException(sprintf('APP_TIMEZONE invalido: %s.', $timezone));
        }

        return new self(
            environment: (string) ($environment['APP_ENV'] ?? 'development'),
            debug: self::toBool($environment['APP_DEBUG'] ?? false),
            timezone: $timezone,
            logLevel: (string) ($environment['LOG_LEVEL'] ?? 'info'),
            logPath: $rootPath . '/storage/logs/app.log',
            dbHost: (string) ($environment['DB_HOST'] ?? 'dev-postgres'),
            dbPort: (int) ($environment['DB_PORT'] ?? 5432),
            dbName: (string) ($environment['DB_DATABASE'] ?? 'template_app'),
            dbUser: (string) ($environment['DB_USERNAME'] ?? ''),
            dbPassword: (string) ($environment['DB_PASSWORD'] ?? ''),
            dbSchema: (string) ($environment['DB_SCHEMA'] ?? 'public'),
        );
    }

    private static function toBool(mixed $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? false;
    }
}
