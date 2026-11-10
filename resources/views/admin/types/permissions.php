<?php

declare(strict_types=1);

/** @var array<string,mixed> $type */
/** @var list<array<string,mixed>> $permissions */
/** @var list<int> $selected */
/** @var int $id */
/** @var App\Support\ViewRenderer $this */
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Compatibilidade</p><h1 class="ui-page-title">Tipo × Permissões</h1><p class="ui-page-description"><?= $this->e($type['name']) ?></p></div><a class="ui-button ui-button--secondary" href="/configuracoes/tipos">Voltar</a></header>
<section class="ui-card ui-card--padded ui-form-surface"><form class="ui-stack" method="post" action="/configuracoes/tipos/<?= $id ?>/permissoes"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><div class="ui-stack"><?php foreach ($permissions as $permission) : ?><label class="ui-details ui-check-label ui-choice-card <?= !$permission['active'] ? 'is-disabled' : '' ?>"><input class="ui-checkbox" type="checkbox" name="permission_ids[]" value="<?= (int) $permission['id'] ?>" <?= in_array((int) $permission['id'], $selected, true) ? 'checked' : '' ?> <?= !$permission['active'] ? 'disabled' : '' ?>><span><strong><?= $this->e($permission['code']) ?></strong> — <?= $this->e($permission['name']) ?></span></label><?php endforeach; ?></div><div class="ui-actions"><button class="ui-button ui-button--primary" type="submit">Salvar relações</button><a class="ui-button ui-button--secondary" href="/configuracoes/tipos">Cancelar</a></div></form></section>
