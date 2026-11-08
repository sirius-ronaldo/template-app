<?php

declare(strict_types=1);

/** @var array<string,mixed> $user */
/** @var list<array<string,mixed>> $groups */
/** @var list<int> $selected */
/** @var array<string,string> $errors */
/** @var int $userId */
/** @var App\Support\ViewRenderer $this */
?>
<section class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">Usuário × Grupos</h1>
    <p class="mt-2 text-sm text-slate-600">Usuário: <?= $this->e($user['name']) ?></p>
    <?php if (isset($errors['global'])) : ?><p class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-rose-800"><?= $this->e($errors['global']) ?></p><?php endif; ?>
    <form class="mt-6" method="post" action="/configuracoes/usuarios/<?= $userId ?>/grupos">
        <input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
        <div class="space-y-3">
            <?php foreach ($groups as $group) : ?>
                <label class="flex items-center gap-3 rounded-xl border border-slate-200 px-4 py-3 <?= !$group['active'] ? 'opacity-50' : '' ?>">
                    <input type="checkbox" name="group_ids[]" value="<?= (int) $group['id'] ?>" <?= in_array((int) $group['id'], $selected, true) ? 'checked' : '' ?> <?= !$group['active'] ? 'disabled' : '' ?>>
                    <span><?= $this->e($group['code']) ?> — <?= $this->e($group['name']) ?></span>
                </label>
            <?php endforeach; ?>
        </div>
        <button class="mt-6 rounded-xl bg-slate-900 px-4 py-3 font-medium text-white" type="submit">Salvar grupos</button>
    </form>
</section>
