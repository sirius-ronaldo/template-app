<?php

declare(strict_types=1);

/** @var string $filter */
/** @var App\Support\Pagination $pagination */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<section class="rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">Configurações &gt; Segurança</h1>
    <p class="mt-2 text-sm text-slate-600">Tela piloto para validar filtros, paginação e respostas HTMX.</p>
    <form class="mt-6" method="get" action="/configuracoes/seguranca" hx-get="/configuracoes/seguranca" hx-target="#security-table" hx-push-url="true">
        <label class="text-sm font-medium" for="filter">Filtro</label>
        <input class="mt-2 rounded-xl border border-slate-300 px-4 py-2" id="filter" name="filter" value="<?= $this->e($filter) ?>">
        <button class="ml-2 rounded-xl bg-slate-900 px-4 py-2 text-sm font-medium text-white" type="submit">Filtrar</button>
    </form>
    <div id="security-table" class="mt-6">
        <?php require __DIR__ . '/_table.php'; ?>
    </div>
</section>
<?php
$content = (string) ob_get_clean();
echo $content;
