<?php

declare(strict_types=1);

/** @var string $filter */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Configurações</p><h1 class="ui-page-title">Segurança</h1><p class="ui-page-description">Acesso central aos cadastros e controles de autorização.</p></div><a class="ui-button ui-button--secondary" href="/security/manifest">Atualizar aplicações</a></header>
<section class="ui-grid ui-grid--2" aria-label="Atalhos de segurança">
    <a class="ui-card ui-card--compact ui-link" href="/configuracoes/usuarios"><strong>Usuários</strong><p class="ui-card__description">Cadastros, credenciais e grupos.</p></a>
    <a class="ui-card ui-card--compact ui-link" href="/configuracoes/grupos"><strong>Grupos</strong><p class="ui-card__description">Vínculos e permissões concedidas.</p></a>
    <a class="ui-card ui-card--compact ui-link" href="/configuracoes/permissoes"><strong>Permissões</strong><p class="ui-card__description">Catálogo de ações autorizáveis.</p></a>
    <a class="ui-card ui-card--compact ui-link" href="/configuracoes/tipos"><strong>Tipos de aplicação</strong><p class="ui-card__description">Compatibilidade entre aplicações e permissões.</p></a>
</section>
<section class="ui-card ui-card--padded" style="margin-top: 20px"><div class="ui-card__header"><div><h2 class="ui-card__title">Diagnóstico da infraestrutura</h2><p class="ui-card__description">Filtro e paginação com atualização parcial por HTMX.</p></div></div>
<form class="ui-filter-grid" method="get" action="/configuracoes/seguranca" hx-get="/configuracoes/seguranca" hx-target="#security-table" hx-push-url="true"><div class="ui-field ui-field--full"><label class="ui-label" for="filter">Filtro</label><input class="ui-input" id="filter" name="filter" value="<?= $this->e($filter) ?>"></div><div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Filtrar</button></div></form>
<div id="security-table" style="margin-top: 20px"><?php require __DIR__ . '/_table.php'; ?></div></section>
<?php
$content = (string) ob_get_clean();
echo $content;
