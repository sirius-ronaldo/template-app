<?php

declare(strict_types=1);

/** @var string $mode */
/** @var array<string,mixed> $group */
/** @var array<string,string> $errors */
/** @var int $groupId */
/** @var App\Support\ViewRenderer $this */
$editing = $mode === 'edit';
$action = $editing ? '/configuracoes/grupos/' . (int) $groupId : '/configuracoes/grupos';
?>
<section class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold"><?= $editing ? 'Editar grupo' : 'Novo grupo' ?></h1>
    <?php if (isset($errors['global'])) : ?><p class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-rose-800"><?= $this->e($errors['global']) ?></p><?php endif; ?>
    <form class="mt-6" method="post" action="<?= $this->e($action) ?>">
        <input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
        <label class="block text-sm font-medium" for="code">Código</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="code" name="code" value="<?= $this->e($group['code']) ?>" required>
        <?php if (isset($errors['code'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['code']) ?></p><?php endif; ?>
        <label class="mt-4 block text-sm font-medium" for="name">Nome</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="name" name="name" value="<?= $this->e($group['name']) ?>" required>
        <?php if (isset($errors['name'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['name']) ?></p><?php endif; ?>
        <label class="mt-4 block text-sm font-medium" for="description">Descrição</label>
        <textarea class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="description" name="description"><?= $this->e($group['description']) ?></textarea>
        <label class="mt-4 flex items-center gap-2 text-sm"><input name="active" type="checkbox" value="1" <?= (bool) $group['active'] ? 'checked' : '' ?>> Grupo ativo</label>
        <button class="mt-6 rounded-xl bg-slate-900 px-4 py-3 font-medium text-white" type="submit">Salvar</button>
    </form>
</section>
