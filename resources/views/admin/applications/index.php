<?php

declare(strict_types=1);

/** @var string $filter */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Catálogo de segurança</p><h1 class="ui-page-title">Aplicações</h1><p class="ui-page-description">Sistemas protegidos e seus tipos de autorização.</p></div><a class="ui-button ui-button--primary" href="/configuracoes/aplicacoes/novo">Nova aplicação</a></header>
<section class="ui-card ui-card--padded"><form class="ui-filter-grid" method="get" action="/configuracoes/aplicacoes" hx-get="/configuracoes/aplicacoes" hx-target="#catalog-table" hx-push-url="true"><div class="ui-field ui-field--full"><label class="ui-label" for="filter">Buscar aplicação</label><input class="ui-input" id="filter" name="filter" value="<?= $this->e($filter) ?>" placeholder="Código ou nome"></div><div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Filtrar</button><a class="ui-button ui-button--secondary" href="/configuracoes/aplicacoes">Limpar</a></div></form><div id="catalog-table" class="ui-section-gap"><?php require __DIR__ . '/_table.php'; ?></div></section>
<?php
$content = (string) ob_get_clean();
echo $content;
