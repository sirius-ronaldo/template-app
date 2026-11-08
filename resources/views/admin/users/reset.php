<?php

declare(strict_types=1);

/** @var int $userId */
/** @var array<string,string> $errors */
/** @var App\Support\ViewRenderer $this */
?>
<section class="mx-auto max-w-xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">Reset administrativo de senha</h1>
    <?php if (isset($errors['global'])) : ?><p class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-rose-800"><?= $this->e($errors['global']) ?></p><?php endif; ?>
    <form class="mt-6" method="post" action="/configuracoes/usuarios/<?= (int) $userId ?>/senha">
        <input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
        <label class="block text-sm font-medium" for="password">Nova senha</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="password" name="password" type="password" minlength="8" required>
        <?php if (isset($errors['password'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['password']) ?></p><?php endif; ?>
        <label class="mt-4 block text-sm font-medium" for="password_confirmation">Confirmação</label>
        <input class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3" id="password_confirmation" name="password_confirmation" type="password" minlength="8" required>
        <?php if (isset($errors['password_confirmation'])) : ?><p class="mt-1 text-sm text-rose-700"><?= $this->e($errors['password_confirmation']) ?></p><?php endif; ?>
        <button class="mt-6 rounded-xl bg-slate-900 px-4 py-3 font-medium text-white" type="submit">Redefinir senha</button>
    </form>
</section>
