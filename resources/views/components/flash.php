<?php

declare(strict_types=1);

/** @var string|null $message */
/** @var string $tone */
/** @var App\Support\ViewRenderer $this */
<?php if (is_string($message) && $message !== '') : ?>
    <div class="ui-flash <?= $tone === 'error' ? 'ui-flash--error' : 'ui-flash--success' ?>" role="alert">
        <strong><?= $tone === 'error' ? 'Não foi possível concluir' : 'Sucesso' ?></strong>
        <span><?= $this->e($message) ?></span>
    </div>
<?php endif; ?>
