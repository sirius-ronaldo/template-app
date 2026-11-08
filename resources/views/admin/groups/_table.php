<?php

declare(strict_types=1);

/** @var list<array<string,mixed>> $groups */
/** @var App\Support\Pagination $pagination */
/** @var array<string,mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<div class="overflow-x-auto rounded-2xl border border-slate-200">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50"><tr><th class="px-4 py-3">Código</th><th class="px-4 py-3">Nome</th><th class="px-4 py-3">Descrição</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Ações</th></tr></thead>
        <tbody class="divide-y divide-slate-200">
        <?php foreach ($groups as $group) : ?>
            <tr>
                <td class="px-4 py-3"><?= $this->e($group['code']) ?></td>
                <td class="px-4 py-3"><?= $this->e($group['name']) ?></td>
                <td class="px-4 py-3"><?= $this->e($group['description']) ?></td>
                <td class="px-4 py-3"><?= (bool) $group['active'] ? 'Ativo' : 'Inativo' ?></td>
                <td class="px-4 py-3"><a class="mr-3 text-emerald-700" href="/configuracoes/grupos/<?= (int) $group['id'] ?>/editar">Editar</a><a class="text-emerald-700" href="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes">Permissões</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="flex items-center justify-between p-4 text-sm">
        <span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span>
        <?php if ($pagination->page < $pagination->pages()) : ?>
            <?php $next = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?>
            <a href="?<?= $this->e($next) ?>" hx-get="/configuracoes/grupos?<?= $this->e($next) ?>" hx-target="#groups-table" hx-push-url="true">Próxima</a>
        <?php endif; ?>
    </div>
</div>
