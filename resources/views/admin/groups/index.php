<?php

declare(strict_types=1);

/** @var string $filter */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Administração</p><h1 class="ui-page-title">Grupos</h1><p class="ui-page-description">Organize usuários e permissões em grupos de acesso.</p></div><a class="ui-button ui-button--primary" href="/configuracoes/grupos/novo">Novo grupo</a></header>
<section class="ui-card ui-card--padded">
    <form class="ui-filter-grid" method="get" action="/configuracoes/grupos" hx-get="/configuracoes/grupos" hx-target="#groups-table" hx-push-url="true">
        <div class="ui-field ui-field--full"><label class="ui-label" for="filter">Buscar grupo</label><input class="ui-input" id="filter" name="filter" value="<?= $this->e($filter) ?>" placeholder="Código, nome ou descrição"></div>
        <div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Filtrar</button><a class="ui-button ui-button--secondary" href="/configuracoes/grupos">Limpar</a></div>
    </form>
    <div id="groups-table" class="ui-section-gap"><?php require __DIR__ . '/_table.php'; ?></div>
</section>
<?php
$content = (string) ob_get_clean();
echo $content;
