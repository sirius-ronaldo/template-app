<?php

declare(strict_types=1);

/** @var int $userId */
/** @var array<string,string> $errors */
/** @var App\Support\ViewRenderer $this */
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Usuários</p><h1 class="ui-page-title">Redefinir senha</h1><p class="ui-page-description">Defina uma nova credencial temporária para o usuário.</p></div><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Voltar</a></header>
<section class="ui-card ui-card--padded ui-form-surface--narrow">
    <?php if (isset($errors['global'])) : ?><div class="ui-flash ui-flash--error" role="alert"><strong>Revise os dados</strong><span><?= $this->e($errors['global']) ?></span></div><?php endif; ?>
    <form class="ui-form-grid" method="post" action="/configuracoes/usuarios/<?= (int) $userId ?>/senha">
        <input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
        <div class="ui-field"><label class="ui-label" for="password">Nova senha</label><input class="ui-input" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><?php if (isset($errors['password'])) : ?><p class="ui-field-error"><?= $this->e($errors['password']) ?></p><?php endif; ?></div>
        <div class="ui-field"><label class="ui-label" for="password_confirmation">Confirmação</label><input class="ui-input" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required><?php if (isset($errors['password_confirmation'])) : ?><p class="ui-field-error"><?= $this->e($errors['password_confirmation']) ?></p><?php endif; ?></div>
        <div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Redefinir senha</button><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Cancelar</a></div>
    </form>
</section>
