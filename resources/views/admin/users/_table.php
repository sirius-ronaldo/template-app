<?php

declare(strict_types=1);

/** @var list<array<string,mixed>> $users */
/** @var App\Support\Pagination $pagination */
/** @var array<string,mixed> $query */
/** @var App\Support\ViewRenderer $this */
?>
<div class="ui-table-wrap">
    <table class="ui-table">
        <thead><tr><th>Login</th><th>Nome</th><th>E-mail</th><th>Status</th><th>Ações</th></tr></thead>
        <tbody>
        <?php if ($users === []) : ?>
            <tr><td colspan="5"><div class="ui-empty">Nenhum usuário encontrado.</div></td></tr>
        <?php endif; ?>
        <?php foreach ($users as $user) : ?>
            <tr>
                <td><strong><?= $this->e($user['login']) ?></strong></td>
                <td><?= $this->e($user['name']) ?></td>
                <td><?= $this->e($user['email']) ?></td>
                <td><span class="ui-badge <?= (bool) $user['active'] ? 'ui-badge--success' : 'ui-badge--danger' ?>"><?= (bool) $user['active'] ? 'Ativo' : 'Inativo' ?></span></td>
                <td><div class="ui-table__actions"><a class="ui-link" href="/configuracoes/usuarios/<?= (int) $user['id'] ?>/editar">Editar</a><a class="ui-link" href="/configuracoes/usuarios/<?= (int) $user['id'] ?>/senha">Senha</a><a class="ui-link" href="/configuracoes/usuarios/<?= (int) $user['id'] ?>/grupos">Grupos</a></div></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <nav class="ui-pagination" aria-label="Paginação de usuários">
        <span>Página <?= $pagination->page ?> de <?= $pagination->pages() ?></span>
        <?php if ($pagination->page < $pagination->pages()) : ?>
            <?php $next = http_build_query($pagination->queryForPage($pagination->page + 1, $query)); ?>
            <a class="ui-link" href="?<?= $this->e($next) ?>" hx-get="/configuracoes/usuarios?<?= $this->e($next) ?>" hx-target="#users-table" hx-push-url="true">Próxima</a>
        <?php endif; ?>
    </nav>
</div>
