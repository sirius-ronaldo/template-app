<?php

declare(strict_types=1);

namespace Tests\Integration;

use PDO;

final class IntegrationDatabase
{
    public const SCHEMA = 'template_app_test_schema';

    private static bool $migrated = false;

    public static function reset(): void
    {
        if ((string) ($_ENV['APP_ENV'] ?? '') !== 'testing') {
            throw new \RuntimeException('Testes de integracao exigem APP_ENV=testing.');
        }
        $pdo = self::connection(false);
        if (!self::$migrated) {
            $pdo->exec('DROP SCHEMA IF EXISTS ' . self::SCHEMA . ' CASCADE');
            $pdo->exec('CREATE SCHEMA ' . self::SCHEMA);
            $command = 'vendor/bin/phinx migrate -e testing --no-info 2>&1';
            exec($command, $output, $status);
            if ($status !== 0) {
                throw new \RuntimeException('Falha ao executar migrations de teste: ' . implode(' ', $output));
            }
            self::$migrated = true;
            return;
        }
        $pdo->exec('SET search_path TO ' . self::SCHEMA);
        $pdo->exec(
            'TRUNCATE TABLE seg_grupos_aplicacoes_permissoes, '
            . 'seg_tipos_aplicacoes_permissoes, seg_aplicacoes, seg_tipos_aplicacoes, '
            . 'seg_permissoes, seg_usuarios_grupos, seg_grupos, seg_usuarios '
            . 'RESTART IDENTITY CASCADE'
        );
        $pdo->exec(
            "INSERT INTO seg_grupos "
            . "(seg_gru_codigo, seg_gru_nome, seg_gru_descricao, seg_gru_ativo) "
            . "VALUES ('ADMIN', 'Administradores', 'Grupo estrutural', true)"
        );
    }

    public static function connection(bool $testSchema = true): PDO
    {
        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $_ENV['DB_HOST'] ?? 'dev-postgres',
            $_ENV['DB_PORT'] ?? '5432',
            $_ENV['DB_DATABASE_TEST'] ?? ''
        );

        $pdo = new PDO($dsn, $_ENV['DB_USERNAME_TEST'] ?? '', $_ENV['DB_PASSWORD_TEST'] ?? '', [
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
