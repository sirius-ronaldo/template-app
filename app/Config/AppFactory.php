<?php

declare(strict_types=1);

namespace App\Config;

use App\Controller\HomeController;
use App\Controller\StatusController;
use App\Repository\DatabaseDiagnosticsRepository;
use App\Service\HomeService;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\Session;
use App\Support\View;
use PDO;

final class AppFactory
{
    public static function build(): Container
    {
        $container = new Container();

        $container->singleton(Session::class, static fn (): Session => new Session());
        $container->singleton(
            Csrf::class,
            static fn (Container $c): Csrf => new Csrf($c->get(Session::class))
        );
        $container->singleton(
            View::class,
            static fn (Container $c): View => new View(
                dirname(__DIR__) . '/View',
                $c->get(Session::class),
                $c->get(Csrf::class)
            )
        );
        $container->singleton(PDO::class, static function (): PDO {
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

            if (!empty($_ENV['DB_SCHEMA'])) {
                $schema = str_replace('"', '""', (string) $_ENV['DB_SCHEMA']);
                $pdo->exec(sprintf('SET search_path TO "%s"', $schema));
            }

            return $pdo;
        });
        $container->singleton(
            DatabaseDiagnosticsRepository::class,
            static fn (Container $c): DatabaseDiagnosticsRepository => new DatabaseDiagnosticsRepository(
                $c->get(PDO::class)
            )
        );
        $container->singleton(
            HomeService::class,
            static fn (Container $c): HomeService => new HomeService($c->get(DatabaseDiagnosticsRepository::class))
        );
        $container->singleton(
            HomeController::class,
            static fn (Container $c): HomeController => new HomeController(
                $c->get(View::class),
                $c->get(HomeService::class)
            )
        );
        $container->singleton(
            StatusController::class,
            static fn (): StatusController => new StatusController()
        );

        return $container;
    }
}
