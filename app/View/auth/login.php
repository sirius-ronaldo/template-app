<?php

declare(strict_types=1);

/** @var App\Support\View $this */
/** @var App\Support\Csrf $csrf */
/** @var App\Config\UiConfig|null $ui */
/** @var string|null $error */

$appName = $ui->name;
$shortName = $ui->shortName;
$tagline = $ui->tagline;
ob_start();
?>
<main class="ui-public-main" id="main-content">
    <section class="ui-login-card" aria-labelledby="login-title">
        <header class="ui-login-brand">
            <?php if ($ui->loginHeaderPath !== null) : ?>
                <img src="<?= $this->e($ui->loginHeaderPath) ?>" alt="<?= $this->e($appName) ?>">
            <?php else : ?>
                <div>
                    <span class="ui-login-brand__mark" aria-hidden="true"><?= $this->e($shortName) ?></span>
                    <strong><?= $this->e($appName) ?></strong>
                    <span><?= $this->e($tagline) ?></span>
                </div>
            <?php endif; ?>
        </header>
        <form method="post" action="/login" class="ui-login-content">
            <input type="hidden" name="_csrf" value="<?= $this->e($csrf->token()) ?>">
            <p class="ui-eyebrow">Acesso seguro</p>
            <h1 class="ui-card__title" id="login-title">Entrar</h1>
            <p class="ui-card__description">Informe suas credenciais para continuar.</p>
            <?php if (isset($error) && $error !== '') : ?>
                <div class="ui-flash ui-flash--error" role="alert">
                    <strong>Credenciais inválidas</strong><span><?= $this->escape($error) ?></span>
                </div>
            <?php endif; ?>
            <div class="ui-field ui-section-gap">
                <label class="ui-label" for="login">Login</label>
                <input class="ui-input" id="login" name="login" autocomplete="username" required autofocus>
            </div>
            <div class="ui-field ui-mt-4">
                <label class="ui-label" for="password">Senha</label>
                <input
                    class="ui-input"
                    id="password"
                    name="password"
                    type="password"
                    autocomplete="current-password"
                    required
                >
            </div>
            <button class="ui-button ui-button--primary" type="submit">Entrar</button>
        </form>
        <footer class="ui-login-footer">
            <?php if ($ui->loginFooterPath !== null) : ?>
                <img src="<?= $this->e($ui->loginFooterPath) ?>" alt="">
            <?php else : ?>
                Autenticação protegida por sessão, CSRF e hash Argon2id.
            <?php endif; ?>
        </footer>
    </section>
</main>
<?php
$content = (string) ob_get_clean();
require dirname(__DIR__) . '/layouts/public.php';
