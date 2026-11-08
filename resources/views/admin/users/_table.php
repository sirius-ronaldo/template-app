<?php

declare(strict_types=1);

/** @var list<array<string,mixed>> $users */
/** @var App\Support\Pagination $pagination */
/** @var array<string,mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<div class="overflow-x-auto rounded-2xl border border-slate-200">
    <table class="w-full text-left text-sm">
        <thead class="bg-slate-50"><tr><th class="px-4 py-3">Login</th><th class="px-4 py-3">Nome</th><th class="px-4 py-3">E-mail</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Ações</th></tr></thead>
        <tbody class="divide-y divide-slate-200">
        <?php foreach ($users as $user) : ?>
            <tr>
                <td class="px-4 py-3"><?= $this->e($user['login']) ?></td>
                <td class="px-4 py-3"><?= $this->e($user['name']) ?></td>
                <td class="px-4 py-3"><?= $this->e($user['email']) ?></td>
                <td class="px-4 py-3"><?= (bool) $user['active'] ? 'Ativo' : 'Inativo' ?></td>
                <td class="px-4 py-3"><a class="mr-3 text-emerald-700" href="/configuracoes/usuarios/<?= (int) $user['id'] ?>/editar">Editar</a><a class="mr-3 text-emerald-700" href="/configuracoes/usuarios/<?= (int) $user['id'] ?>/senha">Resetar senha</a><a class="text-emerald-700" href="/configuracoes/usuarios/<?= (int) $user['id'] ?>/grupos">Grupos</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <div class="flex items-center justify-between p-4 text-sm">
        <span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span>
        <?php if ($pagination->page < $pagination->pages()) : ?>
            <?php $next = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?>
            <a href="?<?= $this->e($next) ?>" hx-get="/configuracoes/usuarios?<?= $this->e($next) ?>" hx-target="#users-table" hx-push-url="true">Próxima</a>
        <?php endif; ?>
    </div>
</div>
