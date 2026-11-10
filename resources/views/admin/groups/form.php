<?php

declare(strict_types=1);

/** @var string $mode */
/** @var array<string,mixed> $group */
/** @var array<string,string> $errors */
/** @var int $groupId */
/** @var App\Support\ViewRenderer $this */
$editing = $mode === 'edit';
$action = $editing ? '/configuracoes/grupos/' . (int) $groupId : '/configuracoes/grupos';
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Grupos</p><h1 class="ui-page-title"><?= $editing ? 'Editar grupo' : 'Novo grupo' ?></h1><p class="ui-page-description">Configure a identificação e a situação do grupo.</p></div><a class="ui-button ui-button--secondary" href="/configuracoes/grupos">Voltar</a></header>
<section class="ui-card ui-card--padded ui-form-surface">
<?php if (isset($errors['global'])) : ?><div class="ui-flash ui-flash--error" role="alert"><strong>Revise os dados</strong><span><?= $this->e($errors['global']) ?></span></div><?php endif; ?>
<form class="ui-form-grid" method="post" action="<?= $this->e($action) ?>"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
<div class="ui-field"><label class="ui-label" for="code">Código</label><input class="ui-input" id="code" name="code" value="<?= $this->e($group['code']) ?>" required><?php if (isset($errors['code'])) : ?><p class="ui-field-error"><?= $this->e($errors['code']) ?></p><?php endif; ?></div>
<div class="ui-field"><label class="ui-label" for="name">Nome</label><input class="ui-input" id="name" name="name" value="<?= $this->e($group['name']) ?>" required><?php if (isset($errors['name'])) : ?><p class="ui-field-error"><?= $this->e($errors['name']) ?></p><?php endif; ?></div>
<div class="ui-field ui-field--full"><label class="ui-label" for="description">Descrição</label><textarea class="ui-textarea" id="description" name="description"><?= $this->e($group['description']) ?></textarea></div>
<label class="ui-check-label ui-field--full"><input class="ui-checkbox" name="active" type="checkbox" value="1" <?= (bool) $group['active'] ? 'checked' : '' ?>> Grupo ativo</label>
<div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Salvar grupo</button><a class="ui-button ui-button--secondary" href="/configuracoes/grupos">Cancelar</a></div></form></section>
