<?php

declare(strict_types=1);

use App\Controller\HomeController;
use App\Controller\LoginController;
use App\Controller\LogoutController;
use App\Controller\PasswordController;
use App\Controller\AdminPilotController;
use App\Controller\UserController;
use App\Controller\GroupController;
use App\Controller\PermissionController;
use App\Controller\ApplicationTypeController;
use App\Controller\ApplicationController;
use App\Controller\GrantController;
use App\Controller\ManifestController;
use App\Controller\StatusController;
use App\Support\Container;
use App\Support\Request;
use App\Support\Response;
use App\Support\Router;

return static function (Container $container): Router {
    $router = new Router();

    $router->add('GET', '/', static fn (Request $request): Response => $container->get(HomeController::class)->index());
    $router->add('GET', '/Home', static fn (Request $request): Response => $container->get(HomeController::class)->index(), metadata: ['auth' => true]);
    $router->add('GET', '/login', static fn (Request $request): Response => $container->get(LoginController::class)->form());
    $router->add('POST', '/login', static fn (Request $request): Response => $container->get(LoginController::class)->login($request));
    $router->add('POST', '/logout', static fn (Request $request): Response => $container->get(LogoutController::class)->logout(), metadata: ['auth' => true]);
    $router->add('GET', '/minha-senha', static fn (Request $request): Response => $container->get(PasswordController::class)->form(), metadata: ['auth' => true]);
    $router->add('POST', '/minha-senha', static fn (Request $request): Response => $container->get(PasswordController::class)->change($request), metadata: ['auth' => true]);
    $router->add(
        'GET',
        '/configuracoes/seguranca',
        static fn (Request $request): Response => $container->get(AdminPilotController::class)->index($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/usuarios',
        static fn (Request $request): Response => $container->get(UserController::class)->index($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/usuarios/novo',
        static fn (Request $request): Response => $container->get(UserController::class)->createForm($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/usuarios',
        static fn (Request $request): Response => $container->get(UserController::class)->create($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/usuarios/{id:\\d+}/editar',
        static fn (Request $request, int $id): Response => $container->get(UserController::class)->editForm($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/usuarios/{id:\\d+}',
        static fn (Request $request, int $id): Response => $container->get(UserController::class)->update($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/usuarios/{id:\\d+}/senha',
        static fn (Request $request, int $id): Response => $container->get(UserController::class)->resetForm($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/usuarios/{id:\\d+}/senha',
        static fn (Request $request, int $id): Response => $container->get(UserController::class)->reset($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/usuarios/{id:\\d+}/status',
        static fn (Request $request, int $id): Response => $container->get(UserController::class)->toggle($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/usuarios/{id:\\d+}/excluir',
        static fn (Request $request, int $id): Response => $container->get(UserController::class)->delete($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/grupos',
        static fn (Request $request): Response => $container->get(GroupController::class)->index($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/grupos/novo',
        static fn (Request $request): Response => $container->get(GroupController::class)->createForm($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/grupos',
        static fn (Request $request): Response => $container->get(GroupController::class)->create($request),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/grupos/{id:\\d+}/editar',
        static fn (Request $request, int $id): Response => $container->get(GroupController::class)->editForm($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/grupos/{id:\\d+}',
        static fn (Request $request, int $id): Response => $container->get(GroupController::class)->update($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/grupos/{id:\\d+}/status',
        static fn (Request $request, int $id): Response => $container->get(GroupController::class)->toggle($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/grupos/{id:\\d+}/excluir',
        static fn (Request $request, int $id): Response => $container->get(GroupController::class)->delete($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/grupos/{id:\\d+}/permissoes',
        static fn (Request $request, int $id): Response => $container->get(GrantController::class)->form($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/grupos/{id:\\d+}/permissoes',
        static fn (Request $request, int $id): Response => $container->get(GrantController::class)->sync($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'GET',
        '/configuracoes/usuarios/{id:\\d+}/grupos',
        static fn (Request $request, int $id): Response => $container->get(GroupController::class)->userGroupsForm($request, $id),
        metadata: ['admin' => true]
    );
    $router->add(
        'POST',
        '/configuracoes/usuarios/{id:\\d+}/grupos',
        static fn (Request $request, int $id): Response => $container->get(GroupController::class)->syncUserGroups($request, $id),
        metadata: ['admin' => true]
    );
    $router->add('GET', '/configuracoes/permissoes', static fn (Request $request): Response => $container->get(PermissionController::class)->index($request), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/permissoes/novo', static fn (Request $request): Response => $container->get(PermissionController::class)->form($request), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/permissoes', static fn (Request $request): Response => $container->get(PermissionController::class)->save($request), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/permissoes/{id:\\d+}/editar', static fn (Request $request, int $id): Response => $container->get(PermissionController::class)->form($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/permissoes/{id:\\d+}', static fn (Request $request, int $id): Response => $container->get(PermissionController::class)->save($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/permissoes/{id:\\d+}/status', static fn (Request $request, int $id): Response => $container->get(PermissionController::class)->toggle($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/permissoes/{id:\\d+}/excluir', static fn (Request $request, int $id): Response => $container->get(PermissionController::class)->delete($request, $id), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/tipos', static fn (Request $request): Response => $container->get(ApplicationTypeController::class)->index($request), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/tipos/novo', static fn (Request $request): Response => $container->get(ApplicationTypeController::class)->form($request), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/tipos', static fn (Request $request): Response => $container->get(ApplicationTypeController::class)->save($request), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/tipos/{id:\\d+}/editar', static fn (Request $request, int $id): Response => $container->get(ApplicationTypeController::class)->form($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/tipos/{id:\\d+}', static fn (Request $request, int $id): Response => $container->get(ApplicationTypeController::class)->save($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/tipos/{id:\\d+}/status', static fn (Request $request, int $id): Response => $container->get(ApplicationTypeController::class)->toggle($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/tipos/{id:\\d+}/excluir', static fn (Request $request, int $id): Response => $container->get(ApplicationTypeController::class)->delete($request, $id), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/tipos/{id:\\d+}/permissoes', static fn (Request $request, int $id): Response => $container->get(ApplicationTypeController::class)->relationForm($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/tipos/{id:\\d+}/permissoes', static fn (Request $request, int $id): Response => $container->get(ApplicationTypeController::class)->syncRelation($request, $id), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/aplicacoes', static fn (Request $request): Response => $container->get(ApplicationController::class)->index($request), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/aplicacoes/novo', static fn (Request $request): Response => $container->get(ApplicationController::class)->form($request), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/aplicacoes', static fn (Request $request): Response => $container->get(ApplicationController::class)->save($request), metadata: ['admin' => true]);
    $router->add('GET', '/configuracoes/aplicacoes/{id:\\d+}/editar', static fn (Request $request, int $id): Response => $container->get(ApplicationController::class)->form($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/aplicacoes/{id:\\d+}', static fn (Request $request, int $id): Response => $container->get(ApplicationController::class)->save($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/aplicacoes/{id:\\d+}/status', static fn (Request $request, int $id): Response => $container->get(ApplicationController::class)->toggle($request, $id), metadata: ['admin' => true]);
    $router->add('POST', '/configuracoes/aplicacoes/{id:\\d+}/excluir', static fn (Request $request, int $id): Response => $container->get(ApplicationController::class)->delete($request, $id), metadata: ['admin' => true]);
    $router->add('GET', '/security/manifest', static fn (Request $request): Response => $container->get(ManifestController::class)->show($request), metadata: ['admin' => true]);
    $router->add('POST', '/security/manifest/validate', static fn (Request $request): Response => $container->get(ManifestController::class)->validate($request), metadata: ['admin' => true]);
    $router->add('POST', '/security/manifest/preview', static fn (Request $request): Response => $container->get(ManifestController::class)->preview($request), metadata: ['admin' => true]);
    $router->add('POST', '/security/manifest/apply', static fn (Request $request): Response => $container->get(ManifestController::class)->apply($request), metadata: ['admin' => true]);
    $router->add('GET', '/status', static fn (Request $request): Response => $container->get(StatusController::class)->show());

    return $router;
};
