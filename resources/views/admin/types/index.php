<?php

declare(strict_types=1);

/** @var string $filter */
/** @var App\Support\Pagination $pagination */
/** @var array<string,mixed> $query */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<section class="rounded-3xl bg-white p-8 shadow-sm">
    <div class="flex justify-between"><h1 class="text-2xl font-bold">Tipos de aplicação</h1><a href="/configuracoes/tipos/novo" class="rounded-xl bg-slate-900 px-4 py-2 text-white">Novo tipo</a></div>
    <form class="mt-6" method="get" action="/configuracoes/tipos" hx-get="/configuracoes/tipos" hx-target="#catalog-table" hx-push-url="true"><input class="rounded-xl border px-4 py-2" name="filter" value="<?= $this->e($filter) ?>"><button class="ml-2 rounded-xl bg-slate-900 px-4 py-2 text-white">Filtrar</button></form>
    <div id="catalog-table" class="mt-6"><?php require __DIR__ . '/_table.php'; ?></div>
</section>
<?php
$content = (string) ob_get_clean();
echo $content;
