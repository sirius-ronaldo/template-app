<?php

declare(strict_types=1);

namespace Tests\Integration;

use PDO;

final class IntegrationDatabase
{
    public const SCHEMA = 'template_app_test_schema';

    public static function reset(): void
    {
        $pdo = self::connection(false);
        $pdo->exec('DROP SCHEMA IF EXISTS ' . self::SCHEMA . ' CASCADE');
        $pdo->exec('CREATE SCHEMA ' . self::SCHEMA);
        $pdo->exec('SET search_path TO ' . self::SCHEMA);
        $schema = file_get_contents(dirname(__DIR__, 2) . '/estrutura_seguranca_padrao.sql');
        if ($schema === false) {
            throw new \RuntimeException('Estrutura de seguranca nao encontrada.');
        }
        $pdo->exec($schema);
    }

    public static function connection(bool $testSchema = true): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $_ENV['DB_HOST'] ?? 'dev-postgres',
            $_ENV['DB_PORT'] ?? '5432',
            $_ENV['DB_DATABASE'] ?? 'template_app'
        );

        $pdo = new PDO($dsn, $_ENV['DB_USERNAME'] ?? '', $_ENV['DB_PASSWORD'] ?? '', [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        if ($testSchema) {
            $pdo->exec('SET search_path TO ' . self::SCHEMA);
        }

        return $pdo;
    }
}
