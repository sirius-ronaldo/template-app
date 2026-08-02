<?php

declare(strict_types=1);

namespace Tests\Integration;

use PDO;

final class IntegrationDatabase
{
    public const SCHEMA = 'template_app_test_schema';

    public static function reset(): void
    {
        $pdo = self::rootConnection();
        $pdo->exec('DROP SCHEMA IF EXISTS ' . self::SCHEMA . ' CASCADE');
        $pdo->exec('CREATE SCHEMA ' . self::SCHEMA);
        $pdo->exec('SET search_path TO ' . self::SCHEMA);
    }

    private static function rootConnection(): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $_ENV['DB_HOST'] ?? 'dev-postgres',
            $_ENV['DB_PORT'] ?? '5432',
            $_ENV['DB_DATABASE'] ?? 'template_app'
        );

        return new PDO($dsn, $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }
}
