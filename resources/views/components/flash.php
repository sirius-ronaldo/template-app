<?php

declare(strict_types=1);

/** @var string|null $message */
/** @var string $tone */
/** @var App\Support\ViewRenderer $this */
<?php if (is_string($message) && $message !== '') : ?>
    <div class="rounded-xl px-4 py-3 <?= $tone === 'error' ? 'bg-rose-50 text-rose-800' : 'bg-emerald-50 text-emerald-800' ?>">
        <?= $this->e($message) ?>
    </div>
<?php endif; ?>
