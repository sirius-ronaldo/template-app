<?php

declare(strict_types=1);

/** @var App\Support\Pagination $pagination */
/** @var array<string, mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<nav aria-label="Paginação" class="flex items-center justify-between text-sm">
    <span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span>
    <?php if ($pagination->page < $pagination->pages()) : ?>
        <?php $next = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?>
        <a href="?<?= $this->e($next) ?>" hx-push-url="true">Próxima</a>
    <?php endif; ?>
</nav>
