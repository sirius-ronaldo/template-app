<?php

declare(strict_types=1);

/** @var array<string,mixed> $item */
/** @var list<array<string,mixed>> $types */
/** @var array<string,string> $errors */
/** @var int|null $id */
/** @var App\Support\ViewRenderer $this */
$action = $id === null ? '/configuracoes/aplicacoes' : '/configuracoes/aplicacoes/' . (int) $id;
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Aplicações</p><h1 class="ui-page-title"><?= $id === null ? 'Nova aplicação' : 'Editar aplicação' ?></h1><p class="ui-page-description">Vincule a aplicação a um tipo compatível.</p></div><a class="ui-button ui-button--secondary" href="/configuracoes/aplicacoes">Voltar</a></header>
<section class="ui-card ui-card--padded" style="max-width: 760px"><?php if (isset($errors['global'])) : ?><div class="ui-flash ui-flash--error" role="alert"><strong>Revise os dados</strong><span><?= $this->e($errors['global']) ?></span></div><?php endif; ?><form class="ui-form-grid" method="post" action="<?= $this->e($action) ?>"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
<div class="ui-field"><label class="ui-label" for="code">Código</label><input class="ui-input" id="code" name="code" value="<?= $this->e($item['code']) ?>" required><?php if (isset($errors['code'])) : ?><p class="ui-field-error"><?= $this->e($errors['code']) ?></p><?php endif; ?></div>
<div class="ui-field"><label class="ui-label" for="name">Nome</label><input class="ui-input" id="name" name="name" value="<?= $this->e($item['name']) ?>" required><?php if (isset($errors['name'])) : ?><p class="ui-field-error"><?= $this->e($errors['name']) ?></p><?php endif; ?></div>
<div class="ui-field ui-field--full"><label class="ui-label" for="type_id">Tipo</label><select class="ui-select" id="type_id" name="type_id" required><option value="">Selecione</option><?php foreach ($types as $type) : ?><option value="<?= (int) $type['id'] ?>" <?= (int) $item['type_id'] === (int) $type['id'] ? 'selected' : '' ?>><?= $this->e($type['code'] . ' — ' . $type['name']) ?></option><?php endforeach; ?></select><?php if (isset($errors['type_id'])) : ?><p class="ui-field-error"><?= $this->e($errors['type_id']) ?></p><?php endif; ?></div>
<div class="ui-field ui-field--full"><label class="ui-label" for="description">Descrição</label><textarea class="ui-textarea" id="description" name="description"><?= $this->e($item['description']) ?></textarea></div><label class="ui-check-label ui-field--full"><input class="ui-checkbox" type="checkbox" name="active" value="1" <?= (bool) $item['active'] ? 'checked' : '' ?>> Aplicação ativa</label><div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Salvar aplicação</button><a class="ui-button ui-button--secondary" href="/configuracoes/aplicacoes">Cancelar</a></div></form></section>
<?php
$content = (string) ob_get_clean();
echo $content;
