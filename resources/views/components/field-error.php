<?php

declare(strict_types=1);

/** @var string|null $message */
/** @var App\Support\ViewRenderer $this */
<?php if (is_string($message) && $message !== '') : ?>
    <p class="mt-1 text-sm text-rose-700"><?= $this->e($message) ?></p>
<?php endif; ?>
