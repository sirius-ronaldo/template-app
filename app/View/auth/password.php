<?php

declare(strict_types=1);

/** @var App\Support\View $this */
/** @var App\Support\Csrf $csrf */
/** @var string|null $error */

ob_start();
?>
<header class="ui-page-header">
    <div>
        <p class="ui-eyebrow">Conta</p>
        <h1 class="ui-page-title">Alterar minha senha</h1>
        <p class="ui-page-description">Use uma senha exclusiva com pelo menos oito caracteres.</p>
    </div>
</header>
<section class="ui-card ui-card--padded ui-form-surface--narrow" aria-labelledby="password-form-title">
    <h2 class="ui-card__title" id="password-form-title">Nova credencial</h2>
    <?php if (isset($error) && $error !== '') : ?>
        <div class="ui-flash ui-flash--error ui-section-gap" role="alert">
            <strong>Revise os dados</strong><span><?= $this->e($error) ?></span>
        </div>
    <?php endif; ?>
    <form method="post" action="/minha-senha" class="ui-stack ui-section-gap">
        <input type="hidden" name="_csrf" value="<?= $this->e($csrf->token()) ?>">
        <div class="ui-field">
            <label class="ui-label" for="current_password">Senha atual</label>
            <input
                class="ui-input"
                id="current_password"
                name="current_password"
                type="password"
                autocomplete="current-password"
                required
            >
        </div>
        <div class="ui-form-grid">
            <div class="ui-field">
                <label class="ui-label" for="new_password">Nova senha</label>
                <input
                    class="ui-input"
                    id="new_password"
                    name="new_password"
                    type="password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>
            <div class="ui-field">
                <label class="ui-label" for="new_password_confirmation">Confirme a nova senha</label>
                <input
                    class="ui-input"
                    id="new_password_confirmation"
                    name="new_password_confirmation"
                    type="password"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >
            </div>
        </div>
        <div class="ui-actions">
            <button class="ui-button ui-button--primary" type="submit">Salvar nova senha</button>
            <a class="ui-button ui-button--secondary" href="/Home">Cancelar</a>
        </div>
    </form>
</section>
<?php
$content = (string) ob_get_clean();
require dirname(__DIR__) . '/layouts/app.php';
