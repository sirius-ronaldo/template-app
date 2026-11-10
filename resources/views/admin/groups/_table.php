<?php

declare(strict_types=1);

/** @var list<array<string,mixed>> $groups */
/** @var App\Support\Pagination $pagination */
/** @var array<string,mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>Código</th><th>Nome</th><th>Descrição</th><th>Status</th><th>Ações</th></tr></thead><tbody>
<?php if ($groups === []) : ?><tr><td colspan="5"><div class="ui-empty">Nenhum grupo encontrado.</div></td></tr><?php endif; ?>
<?php foreach ($groups as $group) : ?><tr><td><strong><?= $this->e($group['code']) ?></strong></td><td><?= $this->e($group['name']) ?></td><td><?= $this->e($group['description']) ?></td><td><span class="ui-badge <?= (bool) $group['active'] ? 'ui-badge--success' : 'ui-badge--danger' ?>"><?= (bool) $group['active'] ? 'Ativo' : 'Inativo' ?></span></td><td><div class="ui-table__actions"><a class="ui-link" href="/configuracoes/grupos/<?= (int) $group['id'] ?>/editar">Editar</a><a class="ui-link" href="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes">Permissões</a></div></td></tr><?php endforeach; ?>
</tbody></table><nav class="ui-pagination" aria-label="Paginação de grupos"><span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span><?php if ($pagination->page < $pagination->pages()) : ?><?php $next = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?><a class="ui-link" href="?<?= $this->e($next) ?>" hx-get="/configuracoes/grupos?<?= $this->e($next) ?>" hx-target="#groups-table" hx-push-url="true">Próxima</a><?php endif; ?></nav></div>
