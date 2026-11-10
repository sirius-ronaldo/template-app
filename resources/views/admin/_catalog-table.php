<?php

declare(strict_types=1);

/** @var list<array<string,mixed>> $items */
/** @var App\Support\Pagination $pagination */
/** @var string $catalogBase */
/** @var bool $hasRelations */
/** @var string $activeText */
/** @var string $inactiveText */
/** @var App\Support\ViewRenderer $this */
?>
<div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>Código</th><th>Nome</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php if ($items === []) : ?><tr><td colspan="4"><div class="ui-empty">Nenhum item encontrado.</div></td></tr><?php endif; ?>
<?php foreach ($items as $item) : ?><tr><td><strong><?= $this->e($item['code']) ?></strong></td><td><?= $this->e($item['name']) ?></td><td><span class="ui-badge <?= (bool) $item['active'] ? 'ui-badge--success' : 'ui-badge--danger' ?>"><?= (bool) $item['active'] ? $this->e($activeText) : $this->e($inactiveText) ?></span></td><td><div class="ui-table__actions"><a class="ui-link" href="<?= $this->e($catalogBase) ?>/<?= (int) $item['id'] ?>/editar">Editar</a><?php if ($hasRelations) : ?><a class="ui-link" href="<?= $this->e($catalogBase) ?>/<?= (int) $item['id'] ?>/permissoes">Permissões</a><?php endif; ?><form method="post" action="<?= $this->e($catalogBase) ?>/<?= (int) $item['id'] ?>/status"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><input type="hidden" name="active" value="<?= (bool) $item['active'] ? '0' : '1' ?>"><button class="ui-link-button" type="submit"><?= (bool) $item['active'] ? 'Inativar' : 'Ativar' ?></button></form><form method="post" action="<?= $this->e($catalogBase) ?>/<?= (int) $item['id'] ?>/excluir"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="ui-link-button ui-link-button--danger" type="submit">Excluir</button></form></div></td></tr><?php endforeach; ?>
</tbody></table><div class="ui-pagination"><span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span></div></div>
