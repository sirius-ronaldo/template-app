<?php

declare(strict_types=1);

use App\Config\UiConfig;

/** @var string $content */
/** @var string|null $flashSuccess */
/** @var string|null $flashError */
/** @var array<string, mixed>|null $auth */
/** @var bool $isAdmin */
/** @var string $currentPath */
/** @var UiConfig|null $ui */
/** @var string $csrfToken */

$auth = $auth ?? null;
$isAdmin = $isAdmin ?? false;
$currentPath = $currentPath ?? '/';
$ui = $ui ?? null;
$appName = $ui?->name ?? 'Template App';
$shortName = $ui?->shortName ?? 'TA';
$tagline = $ui?->tagline ?? 'Base segura para novos projetos PHP';
$environmentLabel = $ui?->environmentLabel ?? 'Ambiente de desenvolvimento';
$defaultTheme = $ui?->defaultTheme ?? 'dark';
$logoPath = $ui?->logoPath;
$login = is_array($auth) ? (string) ($auth['login'] ?? '') : '';
$isCurrent = static function (string $path) use ($currentPath): bool {
    return $path === '/Home'
        ? in_array($currentPath, ['/', '/Home'], true)
        : $currentPath === $path || str_starts_with($currentPath, rtrim($path, '/') . '/');
};
$adminExpanded = str_starts_with($currentPath, '/configuracoes')
    || str_starts_with($currentPath, '/security');
?>
<!doctype html>
<html lang="pt-BR" data-default-theme="<?= $this->e($defaultTheme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title><?= $this->e($appName) ?></title>
    <script src="/assets/js/ui.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://unpkg.com/htmx.org@2.0.4" defer></script>
</head>
<body class="ui-body">
    <a class="ui-skip-link" href="#main-content">Ir para o conteúdo</a>
    <div class="ui-shell">
        <aside class="ui-sidebar" id="app-sidebar" aria-label="Navegação principal">
            <div class="ui-sidebar__header">
                <a class="ui-brand" href="/Home" aria-label="Página inicial de <?= $this->e($appName) ?>">
                    <?php if (is_string($logoPath) && $logoPath !== '') : ?>
                        <img class="ui-brand__image" src="<?= $this->e($logoPath) ?>" alt="<?= $this->e($appName) ?>">
                    <?php else : ?>
                        <span class="ui-brand__mark" aria-hidden="true"><?= $this->e($shortName) ?></span>
                        <span>
                            <strong class="ui-brand__name"><?= $this->e($appName) ?></strong>
                            <small class="ui-brand__tagline"><?= $this->e($tagline) ?></small>
                        </span>
                    <?php endif; ?>
                </a>
                <button class="ui-icon-button ui-sidebar__close" type="button" data-sidebar-close aria-label="Fechar menu">
                    <span aria-hidden="true">×</span>
                </button>
            </div>

            <nav class="ui-nav" aria-label="Menu principal">
                <p class="ui-nav__label">Visão geral</p>
                <a class="ui-nav__item <?= $isCurrent('/Home') ? 'is-active' : '' ?>" href="/Home" <?= $isCurrent('/Home') ? 'aria-current="page"' : '' ?>>
                    <span class="ui-nav__icon" aria-hidden="true">⌂</span>
                    <span><strong>Início</strong><small>Resumo do ambiente</small></span>
                </a>
                <?php if (is_array($auth)) : ?>
                    <a class="ui-nav__item <?= $isCurrent('/minha-senha') ? 'is-active' : '' ?>" href="/minha-senha" <?= $isCurrent('/minha-senha') ? 'aria-current="page"' : '' ?>>
                        <span class="ui-nav__icon" aria-hidden="true">◇</span>
                        <span><strong>Minha senha</strong><small>Segurança da conta</small></span>
                    </a>
                <?php endif; ?>

                <?php if ($isAdmin) : ?>
                    <p class="ui-nav__label">Administração</p>
                    <button
                        class="ui-nav__item ui-nav__toggle <?= $adminExpanded ? 'is-active' : '' ?>"
                        type="button"
                        data-nav-toggle
                        aria-expanded="<?= $adminExpanded ? 'true' : 'false' ?>"
                        aria-controls="admin-navigation"
                    >
                        <span class="ui-nav__icon" aria-hidden="true">⚙</span>
                        <span><strong>Configurações</strong><small>Cadastros e acessos</small></span>
                        <span class="ui-nav__chevron" aria-hidden="true">⌄</span>
                    </button>
                    <div class="ui-nav__group" id="admin-navigation" <?= $adminExpanded ? '' : 'hidden' ?>>
                        <a class="<?= $isCurrent('/configuracoes/seguranca') ? 'is-active' : '' ?>" href="/configuracoes/seguranca">Segurança</a>
                        <a class="<?= $isCurrent('/configuracoes/usuarios') ? 'is-active' : '' ?>" href="/configuracoes/usuarios">Usuários</a>
                        <a class="<?= $isCurrent('/configuracoes/grupos') ? 'is-active' : '' ?>" href="/configuracoes/grupos">Grupos</a>
                        <a class="<?= $isCurrent('/configuracoes/permissoes') ? 'is-active' : '' ?>" href="/configuracoes/permissoes">Permissões</a>
                        <a class="<?= $isCurrent('/configuracoes/tipos') ? 'is-active' : '' ?>" href="/configuracoes/tipos">Tipos</a>
                        <a class="<?= $isCurrent('/configuracoes/aplicacoes') ? 'is-active' : '' ?>" href="/configuracoes/aplicacoes">Aplicações</a>
                        <a class="<?= $isCurrent('/security/manifest') ? 'is-active' : '' ?>" href="/security/manifest">Manifesto</a>
                    </div>
                <?php endif; ?>
            </nav>

            <div class="ui-environment-card">
                <span class="ui-status-dot" aria-hidden="true"></span>
                <div><strong>Ambiente</strong><span><?= $this->e($environmentLabel) ?></span></div>
            </div>
        </aside>

        <button class="ui-backdrop" type="button" data-sidebar-close aria-label="Fechar menu" tabindex="-1"></button>

        <div class="ui-workspace">
            <header class="ui-topbar">
                <div class="ui-topbar__start">
                    <button class="ui-icon-button ui-menu-button" type="button" data-sidebar-open aria-controls="app-sidebar" aria-expanded="false" aria-label="Abrir menu">
                        <span aria-hidden="true">☰</span>
                    </button>
                    <div class="ui-date"><span>Hoje</span><strong><?= $this->e(date('d/m/Y')) ?></strong></div>
                    <button class="ui-theme-toggle" type="button" data-theme-toggle aria-label="Alternar tema">
                        <span aria-hidden="true" data-theme-icon>◐</span><span data-theme-label>Tema claro</span>
                    </button>
                </div>
                <div class="ui-topbar__brand"><strong><?= $this->e($shortName) ?></strong><span><?= $this->e($tagline) ?></span></div>
                <div class="ui-user">
                    <?php if (is_array($auth)) : ?>
                        <span class="ui-user__meta"><small>Usuário conectado</small><strong><?= $this->e($login) ?></strong></span>
                        <form method="post" action="/logout">
                            <input type="hidden" name="_csrf" value="<?= $this->e($csrfToken) ?>">
                            <button class="ui-button ui-button--secondary ui-button--compact" type="submit">Sair</button>
                        </form>
                    <?php else : ?>
                        <a class="ui-button ui-button--secondary ui-button--compact" href="/login">Entrar</a>
                    <?php endif; ?>
                </div>
            </header>

            <main class="ui-main" id="main-content" tabindex="-1">
                <div class="ui-content">
                    <?php if (is_string($flashSuccess) && $flashSuccess !== '') : ?>
                        <div class="ui-flash ui-flash--success" role="alert">
                            <strong>Sucesso</strong><span><?= $this->e($flashSuccess) ?></span>
                        </div>
                    <?php endif; ?>
                    <?php if (is_string($flashError) && $flashError !== '') : ?>
                        <div class="ui-flash ui-flash--error" role="alert">
                            <strong>Não foi possível concluir</strong><span><?= $this->e($flashError) ?></span>
                        </div>
                    <?php endif; ?>
                    <?= $content ?>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
