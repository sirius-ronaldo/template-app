<?php

declare(strict_types=1);

/** @var array<string,mixed> $item */
/** @var int|null $id */
/** @var array<string,string> $errors */
/** @var string $catalogBase */
/** @var string $formTitle */
/** @var string $activeLabel */
/** @var App\Support\ViewRenderer $this */
$action = $id === null ? $catalogBase : $catalogBase . '/' . $id;
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Catálogo de segurança</p><h1 class="ui-page-title"><?= $this->e($formTitle) ?></h1><p class="ui-page-description">Códigos são normalizados e devem permanecer únicos.</p></div><a class="ui-button ui-button--secondary" href="<?= $this->e($catalogBase) ?>">Voltar</a></header>
<section class="ui-card ui-card--padded ui-form-surface"><form class="ui-form-grid" method="post" action="<?= $this->e($action) ?>"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
<div class="ui-field"><label class="ui-label" for="code">Código</label><input class="ui-input" id="code" name="code" value="<?= $this->e($item['code'] ?? '') ?>" required><?php if (isset($errors['code'])) : ?><p class="ui-field-error"><?= $this->e($errors['code']) ?></p><?php endif; ?></div>
<div class="ui-field"><label class="ui-label" for="name">Nome</label><input class="ui-input" id="name" name="name" value="<?= $this->e($item['name'] ?? '') ?>" required><?php if (isset($errors['name'])) : ?><p class="ui-field-error"><?= $this->e($errors['name']) ?></p><?php endif; ?></div>
<div class="ui-field ui-field--full"><label class="ui-label" for="description">Descrição</label><textarea class="ui-textarea" id="description" name="description"><?= $this->e($item['description'] ?? '') ?></textarea></div>
<label class="ui-check-label ui-field--full"><input class="ui-checkbox" type="checkbox" name="active" value="1" <?= (bool) ($item['active'] ?? true) ? 'checked' : '' ?>> <?= $this->e($activeLabel) ?></label>
<div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Salvar</button><a class="ui-button ui-button--secondary" href="<?= $this->e($catalogBase) ?>">Cancelar</a></div></form></section>
