<?php

declare(strict_types=1);

namespace App\Config;

use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\PasswordController;
use App\Controller\HomeController;
use App\Controller\StatusController;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Repository\DatabaseDiagnosticsRepository;
use App\Repository\UserCredentialRepository;
use App\Repository\UserCredentialRepositoryInterface;
use App\Service\AuthenticationService;
use App\Service\HomeService;
use App\Service\PasswordChangeService;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\CsrfTokenManager;
use App\Support\FileLogger;
use App\Support\PasswordHasher;
use App\Support\Session;
use App\Support\SessionManager;
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
        $container->singleton(SessionManager::class, static fn (Container $c): SessionManager => new SessionManager(
            $c->get(Session::class)
        ));
        $container->singleton(PasswordHasher::class, static fn (): PasswordHasher => new PasswordHasher());
        $container->singleton(
            CsrfTokenManager::class,
            static fn (Container $c): CsrfTokenManager => new CsrfTokenManager($c->get(Session::class))
        );
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
            UserCredentialRepository::class,
            static fn (Container $c): UserCredentialRepository => new UserCredentialRepository($c->get(PDO::class))
        );
        $container->singleton(
            UserCredentialRepositoryInterface::class,
            static fn (Container $c): UserCredentialRepositoryInterface => $c->get(UserCredentialRepository::class)
        );
        $container->singleton(
            PasswordChangeService::class,
            static fn (Container $c): PasswordChangeService => new PasswordChangeService(
                $c->get(UserCredentialRepositoryInterface::class),
                $c->get(PasswordHasher::class)
            )
        );
        $container->singleton(
            AuthenticationService::class,
            static fn (Container $c): AuthenticationService => new AuthenticationService(
                $c->get(UserCredentialRepositoryInterface::class),
                $c->get(PasswordHasher::class)
            )
        );
        $container->singleton(
            LoginController::class,
            static fn (Container $c): LoginController => new LoginController(
                $c->get(View::class),
                $c->get(AuthenticationService::class),
                $c->get(SessionManager::class)
            )
        );
        $container->singleton(
            LogoutController::class,
            static fn (Container $c): LogoutController => new LogoutController($c->get(SessionManager::class))
        );
        $container->singleton(
            PasswordController::class,
            static fn (Container $c): PasswordController => new PasswordController(
                $c->get(View::class),
                $c->get(PasswordChangeService::class),
                $c->get(SessionManager::class)
            )
        );
        $container->singleton(
            AuthenticationMiddleware::class,
            static fn (Container $c): AuthenticationMiddleware => new AuthenticationMiddleware(
                $c->get(SessionManager::class),
                $c->get(UserCredentialRepositoryInterface::class)
            )
        );
        $container->singleton(
            CsrfMiddleware::class,
            static fn (Container $c): CsrfMiddleware => new CsrfMiddleware($c->get(CsrfTokenManager::class))
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
