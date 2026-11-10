<?php

declare(strict_types=1);

/** @var list<string> $items */
/** @var App\Support\Pagination $pagination */
/** @var array<string, mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<div class="ui-table-wrap"><table class="ui-table"><thead><tr><th>Item de diagnóstico</th><th>Estado</th></tr></thead><tbody><?php if ($items === []) : ?><tr><td colspan="2"><div class="ui-empty">Nenhum item encontrado.</div></td></tr><?php endif; ?><?php foreach ($items as $item) : ?><tr><td><?= $this->e($item) ?></td><td><span class="ui-badge ui-badge--success">Disponível</span></td></tr><?php endforeach; ?></tbody></table><nav class="ui-pagination" aria-label="Paginação do diagnóstico"><span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span><?php if ($pagination->page < $pagination->pages()) : ?><?php $nextQuery = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?><a class="ui-link" href="?<?= $this->e($nextQuery) ?>" hx-get="/configuracoes/seguranca?<?= $this->e($nextQuery) ?>" hx-target="#security-table" hx-push-url="true">Próxima</a><?php endif; ?></nav></div>
