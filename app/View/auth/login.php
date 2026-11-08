<?php

declare(strict_types=1);

/** @var App\Support\View $this */
/** @var string|null $error */
?>
<main class="flex min-h-screen items-center justify-center px-6 py-12">
    <form method="post" action="/login" class="w-full max-w-md rounded-3xl bg-white p-8 shadow-sm">
        <p class="text-sm font-semibold tracking-[0.3em] text-emerald-700">TEMPLATE</p>
        <h1 class="mt-2 text-2xl font-bold">Entrar</h1>
        <?php if (isset($error) && $error !== '') : ?>
            <p class="mt-4 rounded-xl bg-rose-50 px-4 py-3 text-sm text-rose-800">
                <?= $this->escape($error) ?>
            </p>
        <?php endif; ?>
        <label class="mt-6 block text-sm font-medium" for="login">Login</label>
        <input
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"
            id="login"
            name="login"
            required
            autofocus
        >
        <label class="mt-4 block text-sm font-medium" for="password">Senha</label>
        <input
            class="mt-2 w-full rounded-xl border border-slate-300 px-4 py-3"
            id="password"
            name="password"
            type="password"
            required
        >
        <button
            class="mt-6 w-full rounded-xl bg-slate-900 px-4 py-3 font-medium text-white"
            type="submit"
        >Entrar</button>
    </form>
</main>
