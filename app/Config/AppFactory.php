<?php

declare(strict_types=1);

namespace App\Config;

use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\PasswordController;
use App\Controller\AdminPilotController;
use App\Controller\UserController;
use App\Controller\HomeController;
use App\Controller\StatusController;
use App\Middleware\AuthenticationMiddleware;
use App\Middleware\CsrfMiddleware;
use App\Middleware\AdminMiddleware;
use App\Middleware\AuthorizationMiddleware;
use App\Repository\DatabaseDiagnosticsRepository;
use App\Repository\UserCredentialRepository;
use App\Repository\UserCredentialRepositoryInterface;
use App\Repository\UserRepository;
use App\Repository\UserRepositoryInterface;
use App\Repository\AuthorizationRepository;
use App\Service\AuthenticationService;
use App\Service\AdminChecker;
use App\Service\Authorizer;
use App\Service\HomeService;
use App\Service\PasswordChangeService;
use App\Support\Container;
use App\Support\Csrf;
use App\Support\CsrfTokenManager;
use App\Support\FileLogger;
use App\Support\FlashBag;
use App\Support\PasswordHasher;
use App\Support\Session;
use App\Support\SessionManager;
use App\Support\View;
use App\Support\ViewRenderer;
use App\Validator\UserValidator;
use App\Service\UserManagementService;
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
            FlashBag::class,
            static fn (Container $c): FlashBag => new FlashBag($c->get(Session::class))
        );
        $container->singleton(
            ViewRenderer::class,
            static fn (Container $c): ViewRenderer => new ViewRenderer(
                dirname(__DIR__, 2) . '/resources/views',
                $c->get(FlashBag::class),
                $c->get(CsrfTokenManager::class)
            )
        );
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
                $c->get(Csrf::class),
                $c->get(AdminChecker::class)
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
            UserRepository::class,
            static fn (Container $c): UserRepository => new UserRepository($c->get(PDO::class))
        );
        $container->singleton(
            UserRepositoryInterface::class,
            static fn (Container $c): UserRepositoryInterface => $c->get(UserRepository::class)
        );
        $container->singleton(UserValidator::class, static fn (): UserValidator => new UserValidator());
        $container->singleton(
            UserManagementService::class,
            static fn (Container $c): UserManagementService => new UserManagementService(
                $c->get(UserRepositoryInterface::class),
                $c->get(PasswordHasher::class),
                $c->get(UserValidator::class),
                $c->get(AdminChecker::class)
            )
        );
        $container->singleton(
            AuthorizationRepository::class,
            static fn (Container $c): AuthorizationRepository => new AuthorizationRepository($c->get(PDO::class))
        );
        $container->singleton(
            AdminChecker::class,
            static fn (Container $c): AdminChecker => new AdminChecker($c->get(AuthorizationRepository::class))
        );
        $container->singleton(
            Authorizer::class,
            static fn (Container $c): Authorizer => new Authorizer(
                $c->get(AuthorizationRepository::class),
                $c->get(AdminChecker::class)
            )
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
            AdminPilotController::class,
            static fn (Container $c): AdminPilotController => new AdminPilotController($c->get(ViewRenderer::class))
        );
        $container->singleton(
            UserController::class,
            static fn (Container $c): UserController => new UserController(
                $c->get(ViewRenderer::class),
                $c->get(UserManagementService::class),
                $c->get(SessionManager::class),
                $c->get(FlashBag::class)
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
            AdminMiddleware::class,
            static fn (Container $c): AdminMiddleware => new AdminMiddleware(
                $c->get(SessionManager::class),
                $c->get(AdminChecker::class)
            )
        );
        $container->singleton(
            AuthorizationMiddleware::class,
            static fn (Container $c): AuthorizationMiddleware => new AuthorizationMiddleware(
                $c->get(SessionManager::class),
                $c->get(Authorizer::class)
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
