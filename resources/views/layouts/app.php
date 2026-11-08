<?php

declare(strict_types=1);

/** @var string $content */
/** @var string|null $flashSuccess */
/** @var string|null $flashError */
/** @var App\Support\ViewRenderer $this */
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
    <header class="border-b border-slate-200 bg-white">
        <div class="mx-auto flex max-w-6xl items-center justify-between px-6 py-5">
            <a href="/Home" class="font-bold">Template App</a>
            <nav class="flex gap-4 text-sm">
                <a href="/Home">Início</a>
                <a href="/minha-senha">Minha senha</a>
                <a href="/configuracoes/seguranca">Segurança</a>
                <a href="/configuracoes/aplicacoes">Aplicações</a>
            </nav>
        </div>
    </header>
    <main class="mx-auto max-w-6xl px-6 py-8">
        <?php if (is_string($flashSuccess) && $flashSuccess !== '') : ?>
            <div class="mb-4 rounded-xl bg-emerald-50 px-4 py-3 text-emerald-800"><?= $this->e($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if (is_string($flashError) && $flashError !== '') : ?>
            <div class="mb-4 rounded-xl bg-rose-50 px-4 py-3 text-rose-800"><?= $this->e($flashError) ?></div>
        <?php endif; ?>
        <?= $content ?>
    </main>
</body>
</html>
