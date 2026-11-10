<?php

declare(strict_types=1);

/** @var string $content */
/** @var App\Config\UiConfig $ui */
/** @var App\Support\View $this */

$appName = $ui->name;
$defaultTheme = $ui->defaultTheme;
?>
<!doctype html>
<html lang="pt-BR" data-default-theme="<?= $this->e($defaultTheme) ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="color-scheme" content="dark light">
    <title><?= $this->e($appName) ?></title>
    <script src="/assets/js/ui.js"></script>
    <link rel="stylesheet" href="/assets/css/app.css">
</head>
<body class="ui-public-body">
    <?= $content ?>
</body>
</html>
