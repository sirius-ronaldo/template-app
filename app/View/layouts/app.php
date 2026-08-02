<?php

declare(strict_types=1);

/** @var string $content */
/** @var string|null $flashSuccess */
/** @var string|null $flashError */
/** @var App\Support\View $this */
?>
<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Template App</title>
    <link rel="stylesheet" href="/assets/css/app.css">
    <script src="https://unpkg.com/htmx.org@2.0.4"></script>
</head>
<body class="min-h-screen bg-slate-100 text-slate-950">
    <div class="min-h-screen">
        <header class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-6xl px-6 py-5">
                <p class="text-sm font-semibold tracking-[0.3em] text-emerald-700">TEMPLATE</p>
                <h1 class="text-2xl font-bold">Template App</h1>
                <p class="mt-1 text-sm text-slate-600">
                    Base para projetos PHP 8.2 com Docker, PostgreSQL, HTMX, Tailwind CSS e Codex.
                </p>
            </div>
        </header>
        <main class="mx-auto max-w-6xl px-6 py-8">
            <?php if (is_string($flashSuccess) && $flashSuccess !== '') : ?>
                <div class="mb-4 rounded-xl border border-emerald-300 bg-emerald-50 px-4 py-3 text-emerald-800">
                    <?= $this->escape($flashSuccess) ?>
                </div>
            <?php endif; ?>
            <?php if (is_string($flashError) && $flashError !== '') : ?>
                <div class="mb-4 rounded-xl border border-rose-300 bg-rose-50 px-4 py-3 text-rose-800">
                    <?= $this->escape($flashError) ?>
                </div>
            <?php endif; ?>
            <?= $content ?>
        </main>
    </div>
</body>
</html>
