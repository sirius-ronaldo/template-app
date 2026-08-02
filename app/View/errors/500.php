<?php

declare(strict_types=1);

/** @var string|null $message */
/** @var App\Support\View $this */
ob_start();
?>
<section class="mx-auto max-w-xl rounded-3xl bg-white p-8 shadow-sm">
    <h1 class="text-2xl font-bold">500</h1>
    <p class="mt-3 text-sm text-slate-600">Erro interno da aplicacao.</p>
    <?php if (is_string($message) && $message !== '') : ?>
        <pre class="mt-4 overflow-auto rounded-2xl bg-slate-900 p-4 text-xs text-slate-100"><?=
            $this->escape($message)
        ?></pre>
    <?php endif; ?>
</section>
<?php
$content = (string) ob_get_clean();
require dirname(__DIR__) . '/layouts/public.php';
