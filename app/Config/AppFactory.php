<?php

declare(strict_types=1);

namespace App\Config;

use App\Controller\HomeController;
use App\Controller\StatusController;
use App\Repository\DatabaseDiagnosticsRepository;
use App\Service\HomeService;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\FileLogger;
use App\Support\Session;
use App\Support\View;
use PDO;
use Psr\Log\LoggerInterface;

final class AppFactory
{
    public static function build(?AppConfig $config = null): Container
    {
        $config ??= AppConfig::fromEnvironment($_ENV, dirname(__DIR__, 2));
        $container = new Container();

        $container->singleton(AppConfig::class, static fn (): AppConfig => $config);
        $container->singleton(
            LoggerInterface::class,
            static fn (Container $c): FileLogger => new FileLogger($c->get(AppConfig::class)->logPath)
        );

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
        $container->singleton(PDO::class, static function () use ($config): PDO {
            $dsn = sprintf(
                'pgsql:host=%s;port=%s;dbname=%s',
                $config->dbHost,
                $config->dbPort,
                $config->dbName
            );

            $pdo = new PDO($dsn, $config->dbUser, $config->dbPassword, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);

            if ($config->dbSchema !== '') {
                $schema = str_replace('"', '""', $config->dbSchema);
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
