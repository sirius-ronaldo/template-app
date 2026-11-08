<?php

declare(strict_types=1);

/** @var App\Support\View $this */
/** @var App\Support\Csrf $csrf */
/** @var string|null $error */
?>
<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <form method="post" action="/minha-senha" class="w-full max-w-md rounded-3xl bg-white p-8 shadow-sm">
        <h1 class="text-2xl font-bold">Alterar minha senha</h1>
        <?php if (isset($error) && $error !== '') : ?>
            <p class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800"><?= $this->e($error) ?></p>
        <?php endif; ?>
        <input type="hidden" name="_csrf" value="<?= $this->e($csrf->token()) ?>">
        <label class="mt-6 block text-sm font-medium" for="current_password">Senha atual</label>
        <input
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"
            id="current_password"
            name="current_password"
            type="password"
            required
        >
        <label class="mt-4 block text-sm font-medium" for="new_password">Nova senha</label>
        <input
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"
            id="new_password"
            name="new_password"
            type="password"
            minlength="8"
            required
        >
        <label class="mt-4 block text-sm font-medium" for="new_password_confirmation">Confirme a nova senha</label>
        <input
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"
            id="new_password_confirmation"
            name="new_password_confirmation"
            type="password"
            minlength="8"
            required
        >
        <button
            class="mt-6 w-full rounded-xl bg-slate-900 px-4 py-3 font-medium text-white"
            type="submit"
        >Salvar nova senha</button>
    </form>
</main>
