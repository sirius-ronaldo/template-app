<?php

declare(strict_types=1);

/** @var string|null $message */
/** @var App\Support\View $this */
ob_start();
?>
<main class="ui-public-main" id="main-content">
<section class="ui-error-card" aria-labelledby="error-title">
    <p class="ui-error-code" aria-hidden="true">404</p>
    <h1 class="ui-card__title" id="error-title">Página não encontrada</h1>
    <p class="ui-card__description">O endereço informado não existe ou foi movido.</p>
    <?php if (is_string($message) && $message !== '') : ?>
        <pre class="ui-code"><?=
            $this->escape($message)
        ?></pre>
    <?php endif; ?>
</section>
</main>
<?php
$content = (string) ob_get_clean();
require dirname(__DIR__) . '/layouts/public.php';
