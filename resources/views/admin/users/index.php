<?php

declare(strict_types=1);

/** @var string $filter */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<header class="ui-page-header">
    <div><p class="ui-eyebrow">Administração</p><h1 class="ui-page-title">Usuários</h1><p class="ui-page-description">Cadastro, situação, grupos e credenciais de acesso.</p></div>
    <a class="ui-button ui-button--primary" href="/configuracoes/usuarios/novo">Novo usuário</a>
</header>
<section class="ui-card ui-card--padded">
    <form class="ui-filter-grid" method="get" action="/configuracoes/usuarios" hx-get="/configuracoes/usuarios" hx-target="#users-table" hx-push-url="true">
        <div class="ui-field ui-field--full"><label class="ui-label" for="filter">Buscar usuário</label><input class="ui-input" id="filter" name="filter" value="<?= $this->e($filter) ?>" placeholder="Login, nome ou e-mail"></div>
        <div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Filtrar</button><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Limpar</a></div>
    </form>
    <div id="users-table" class="ui-section-gap"><?php require __DIR__ . '/_table.php'; ?></div>
</section>
<?php
$content = (string) ob_get_clean();
echo $content;
