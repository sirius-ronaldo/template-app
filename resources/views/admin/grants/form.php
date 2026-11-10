<?php

declare(strict_types=1);

/** @var array<string,mixed> $group */
/** @var list<array<string,mixed>> $rows */
/** @var string $filter */
/** @var array<string,string> $errors */
/** @var App\Support\ViewRenderer $this */
$types = [];
foreach ($rows as $row) {
    $typeId = (int) $row['type_id'];
    $applicationId = (int) $row['application_id'];
    $types[$typeId]['code'] = $row['type_code'];
    $types[$typeId]['name'] = $row['type_name'];
    $types[$typeId]['applications'][$applicationId]['code'] = $row['application_code'];
    $types[$typeId]['applications'][$applicationId]['name'] = $row['application_name'];
    $types[$typeId]['applications'][$applicationId]['permissions'][] = $row;
}
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Autorizações</p><h1 class="ui-page-title">Grupos × Aplicações × Permissões</h1><p class="ui-page-description">Grupo: <?= $this->e($group['code'] . ' — ' . $group['name']) ?></p></div><a class="ui-button ui-button--secondary" href="/configuracoes/grupos">Voltar</a></header>
<section class="ui-card ui-card--padded">
<?php if ($group['code'] === 'ADMIN') : ?><div class="ui-flash ui-flash--success" role="status"><strong>Acesso irrestrito</strong><span>O grupo ADMIN possui acesso estrutural e sua matriz não é editável.</span></div>
<?php else : ?>
<?php if (isset($errors['global'])) : ?><div class="ui-flash ui-flash--error" role="alert"><strong>Revise os dados</strong><span><?= $this->e($errors['global']) ?></span></div><?php endif; ?>
<form class="ui-filter-grid" method="get" action="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes" hx-get="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes" hx-target="#grant-matrix" hx-push-url="true"><div class="ui-field ui-field--full"><label class="ui-label" for="filter">Buscar aplicação</label><input class="ui-input" id="filter" name="filter" placeholder="Código ou nome da aplicação" value="<?= $this->e($filter) ?>"></div><div class="ui-actions ui-field--full"><button class="ui-button ui-button--secondary" type="submit">Buscar</button></div></form>
<form class="ui-stack" method="post" action="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes" style="margin-top: 20px"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><div class="ui-stack" id="grant-matrix">
<?php if ($types === []) : ?><div class="ui-empty">Nenhuma aplicação compatível encontrada.</div><?php endif; ?>
<?php foreach ($types as $type) : ?><details open class="ui-details"><summary><?= $this->e($type['code'] . ' — ' . $type['name']) ?></summary><div class="ui-details__content ui-stack"><?php foreach ($type['applications'] as $applicationId => $application) : ?><details open class="ui-details"><summary><?= $this->e($application['code'] . ' — ' . $application['name']) ?></summary><div class="ui-details__content ui-form-grid"><?php foreach ($application['permissions'] as $permission) : ?><label class="ui-check-label"><input class="ui-checkbox" type="checkbox" name="permissions[<?= (int) $applicationId ?>][]" value="<?= (int) $permission['permission_id'] ?>" <?= (bool) $permission['granted'] ? 'checked' : '' ?>><?= $this->e($permission['permission_code'] . ' — ' . $permission['permission_name']) ?></label><?php endforeach; ?></div></details><?php endforeach; ?></div></details><?php endforeach; ?>
</div><div class="ui-actions"><button class="ui-button ui-button--primary" type="submit">Salvar permissões</button></div></form>
<?php endif; ?>
</section>
<?php
$content = (string) ob_get_clean();
echo $content;
