<?php

declare(strict_types=1);

/** @var string|null $message */
/** @var App\Support\ViewRenderer $this */
<?php if (is_string($message) && $message !== '') : ?>
    <p class="ui-field-error"><?= $this->e($message) ?></p>
<?php endif; ?>
