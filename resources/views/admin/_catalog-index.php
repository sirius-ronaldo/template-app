<?php

declare(strict_types=1);

/** @var string $catalogTitle */
/** @var string $catalogDescription */
/** @var string $catalogBase */
/** @var string $createLabel */
/** @var string $filter */
/** @var string $tablePartial */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Catálogo de segurança</p><h1 class="ui-page-title"><?= $this->e($catalogTitle) ?></h1><p class="ui-page-description"><?= $this->e($catalogDescription) ?></p></div><a class="ui-button ui-button--primary" href="<?= $this->e($catalogBase) ?>/novo"><?= $this->e($createLabel) ?></a></header>
<section class="ui-card ui-card--padded"><form class="ui-filter-grid" method="get" action="<?= $this->e($catalogBase) ?>" hx-get="<?= $this->e($catalogBase) ?>" hx-target="#catalog-table" hx-push-url="true"><div class="ui-field ui-field--full"><label class="ui-label" for="filter">Buscar no catálogo</label><input class="ui-input" id="filter" name="filter" value="<?= $this->e($filter) ?>" placeholder="Código ou nome"></div><div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Filtrar</button><a class="ui-button ui-button--secondary" href="<?= $this->e($catalogBase) ?>">Limpar</a></div></form><div id="catalog-table" class="ui-section-gap"><?php require $tablePartial; ?></div></section>
<?php
$content = (string) ob_get_clean();
echo $content;
