<?php

declare(strict_types=1);

/** @var list<string> $items */
/** @var App\Support\Pagination $pagination */
/** @var array<string, mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<div class="rounded-2xl border border-slate-200">
    <ul class="divide-y divide-slate-200">
        <?php foreach ($items as $item) : ?>
            <li class="px-4 py-3"><?= $this->e($item) ?></li>
        <?php endforeach; ?>
    </ul>
    <div class="flex items-center justify-between p-4 text-sm">
        <span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span>
        <?php if ($pagination->page < $pagination->pages()) : ?>
            <?php $nextQuery = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?>
            <a href="?<?= $this->e($nextQuery) ?>" hx-get="/configuracoes/seguranca?<?= $this->e($nextQuery) ?>" hx-target="#security-table" hx-push-url="true">Próxima</a>
        <?php endif; ?>
    </div>
</div>
