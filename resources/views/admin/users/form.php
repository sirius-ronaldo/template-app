<?php

declare(strict_types=1);

/** @var string $mode */
/** @var array<string,mixed> $user */
/** @var array<string,string> $errors */
/** @var int $userId */
/** @var App\Support\ViewRenderer $this */
$editing = $mode === 'edit';
$action = $editing ? '/configuracoes/usuarios/' . (int) $userId : '/configuracoes/usuarios';
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Usuários</p><h1 class="ui-page-title"><?= $editing ? 'Editar usuário' : 'Novo usuário' ?></h1><p class="ui-page-description">Informe os dados cadastrais e a situação do acesso.</p></div><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Voltar</a></header>
<section class="ui-card ui-card--padded ui-form-surface">
    <?php if (isset($errors['global'])) : ?><div class="ui-flash ui-flash--error" role="alert"><strong>Revise os dados</strong><span><?= $this->e($errors['global']) ?></span></div><?php endif; ?>
    <form class="ui-form-grid" method="post" action="<?= $this->e($action) ?>">
        <input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>">
        <div class="ui-field"><label class="ui-label" for="login">Login</label><input class="ui-input" id="login" name="login" value="<?= $this->e($user['login']) ?>" autocomplete="username" required><?php if (isset($errors['login'])) : ?><p class="ui-field-error"><?= $this->e($errors['login']) ?></p><?php endif; ?></div>
        <div class="ui-field"><label class="ui-label" for="name">Nome</label><input class="ui-input" id="name" name="name" value="<?= $this->e($user['name']) ?>" required><?php if (isset($errors['name'])) : ?><p class="ui-field-error"><?= $this->e($errors['name']) ?></p><?php endif; ?></div>
        <div class="ui-field ui-field--full"><label class="ui-label" for="email">E-mail</label><input class="ui-input" id="email" name="email" type="email" value="<?= $this->e($user['email']) ?>" autocomplete="email" required><?php if (isset($errors['email'])) : ?><p class="ui-field-error"><?= $this->e($errors['email']) ?></p><?php endif; ?></div>
        <?php if (!$editing) : ?>
            <div class="ui-field"><label class="ui-label" for="password">Senha inicial</label><input class="ui-input" id="password" name="password" type="password" minlength="8" autocomplete="new-password" required><?php if (isset($errors['password'])) : ?><p class="ui-field-error"><?= $this->e($errors['password']) ?></p><?php endif; ?></div>
            <div class="ui-field"><label class="ui-label" for="password_confirmation">Confirmação</label><input class="ui-input" id="password_confirmation" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required></div>
        <?php endif; ?>
        <label class="ui-check-label ui-field--full"><input class="ui-checkbox" name="active" type="checkbox" value="1" <?= (bool) $user['active'] ? 'checked' : '' ?>> Usuário ativo</label>
        <div class="ui-actions ui-field--full"><button class="ui-button ui-button--primary" type="submit">Salvar usuário</button><a class="ui-button ui-button--secondary" href="/configuracoes/usuarios">Cancelar</a></div>
    </form>
</section>
