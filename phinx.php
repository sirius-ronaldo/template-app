<?php

declare(strict_types=1);

use Dotenv\Dotenv;

require __DIR__ . '/vendor/autoload.php';

Dotenv::createImmutable(__DIR__)->safeLoad();

return [
    'paths' => [
        'migrations' => 'database/migrations',
        'seeds' => 'database/seeds',
    ],
    'environments' => [
        'default_migration_table' => 'phinxlog',
        'default_environment' => 'default',
        'default' => [
            'adapter' => 'pgsql',
            'host' => $_ENV['DB_HOST'] ?? 'dev-postgres',
            'name' => $_ENV['DB_DATABASE'] ?? 'template_app',
            'user' => $_ENV['DB_USERNAME'] ?? 'template_app_user',
            'pass' => $_ENV['DB_PASSWORD'] ?? '',
            'port' => (int) ($_ENV['DB_PORT'] ?? 5432),
            'charset' => 'utf8',
        ],
    ],
    'version_order' => 'creation',
];
