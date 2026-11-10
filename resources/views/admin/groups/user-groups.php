<?php

declare(strict_types=1);

/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $groups */
/** @var list<int> $selected */
/** @var array<string,string> $errors */
/** @var int $userId */
/** @var App\Support\ViewRenderer $this */
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Vínculos de acesso</p><h1 class="ui-page-title">Usuário × Grupos</h1><p class="ui-page-description">Usuário: <?= $this->e($user['name']) ?></p></div><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Voltar</a></header>
<section class="ui-card ui-card--padded ui-form-surface">
<?php if (isset($errors['global'])) : ?><div class="ui-flash ui-flash--error" role="alert"><strong>Revise os dados</strong><span><?= $this->e($errors['global']) ?></span></div><?php endif; ?>
<form class="ui-stack" method="post" action="/configuracoes/usuarios/<?= $userId ?>/grupos"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
<div class="ui-stack"><?php foreach ($groups as $group) : ?><label class="ui-details ui-check-label ui-choice-card <?= !$group['active'] ? 'is-disabled' : '' ?>"><input class="ui-checkbox" type="checkbox" name="group_ids[]" value="<?= (int) $group['id'] ?>" <?= in_array((int) $group['id'], $selected, true) ? 'checked' : '' ?> <?= !$group['active'] ? 'disabled' : '' ?>><span><strong><?= $this->e($group['code']) ?></strong> — <?= $this->e($group['name']) ?></span></label><?php endforeach; ?></div>
<div class="ui-actions"><button class="ui-button ui-button--primary" type="submit">Salvar grupos</button><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Cancelar</a></div></form></section>
