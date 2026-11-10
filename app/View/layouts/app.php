<?php

declare(strict_types=1);

/** @var string $content */
/** @var string|null $flashSuccess */
/** @var string|null $flashError */
/** @var array<string, mixed>|null $auth */
/** @var bool $isAdmin */
/** @var string $currentPath */
/** @var App\Config\UiConfig|null $ui */
/** @var App\Support\Csrf $csrf */

$csrfToken = $csrf->token();
require dirname(__DIR__, 3) . '/resources/views/layouts/app-shell.php';
