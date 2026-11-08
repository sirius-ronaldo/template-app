<?php

declare(strict_types=1);

/** @var string $mode */
/** @var array<string,mixed> $user */
/** @var array<string,string> $errors */
/** @var int $userId */
/** @var App\Support\ViewRenderer $this */
$editing = $mode === 'edit';
$action = $editing ? '/configuracoes/usuarios/' . (int) $userId : '/configuracoes/usuarios';
?>
<section class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold"><?= $editing ? 'Editar usuário' : 'Novo usuário' ?></h1>
    <?php if (isset($errors['global'])) : ?><p class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-rose-800"><?= $this->e($errors['global']) ?></p><?php endif; ?>
    <form class="mt-6" method="post" action="<?= $this->e($action) ?>">
        <input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
        <label class="block text-sm font-medium" for="login">Login</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="login" name="login" value="<?= $this->e($user['login']) ?>" required>
        <?php if (isset($errors['login'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['login']) ?></p><?php endif; ?>
        <label class="mt-4 block text-sm font-medium" for="name">Nome</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="name" name="name" value="<?= $this->e($user['name']) ?>" required>
        <?php if (isset($errors['name'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['name']) ?></p><?php endif; ?>
        <label class="mt-4 block text-sm font-medium" for="email">E-mail</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="email" name="email" type="email" value="<?= $this->e($user['email']) ?>" required>
        <?php if (isset($errors['email'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['email']) ?></p><?php endif; ?>
        <?php if (!$editing) : ?>
            <label class="mt-4 block text-sm font-medium" for="password">Senha inicial</label>
            <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="password" name="password" type="password" minlength="8" required>
            <?php if (isset($errors['password'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['password']) ?></p><?php endif; ?>
            <label class="mt-4 block text-sm font-medium" for="password_confirmation">Confirmação</label>
            <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
        <?php endif; ?>
        <label class="mt-4 flex items-center gap-2 text-sm"><input name="active" type="checkbox" value="1" <?= (bool) $user['active'] ? 'checked' : '' ?>> Usuário ativo</label>
        <button class="mt-6 rounded-xl bg-slate-900 px-4 py-3 font-medium text-white" type="submit">Salvar</button>
    </form>
</section>
