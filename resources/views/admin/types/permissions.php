<?php

declare(strict_types=1);

/** @var array<string,mixed> $type */
/** @var list<array<string,mixed>> $permissions */
/** @var list<int> $selected */
/** @var int $id */
/** @var App\Support\ViewRenderer $this */
?>
<section class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm"><h1 class="text-2xl font-bold">Tipo × Permissões</h1><p class="mt-2 text-sm text-slate-600"><?= $this->e($type['name']) ?></p><form class="mt-6" method="post" action="/configuracoes/tipos/<?= $id ?>/permissoes"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><div class="space-y-3"><?php foreach ($permissions as $permission) : ?><label class="flex items-center gap-3 rounded-xl border p-3 <?= !$permission['active'] ? 'opacity-50' : '' ?>"><input type="checkbox" name="permission_ids[]" value="<?= (int) $permission['id'] ?>" <?= in_array((int) $permission['id'], $selected, true) ? 'checked' : '' ?> <?= !$permission['active'] ? 'disabled' : '' ?>><span><?= $this->e($permission['code']) ?> — <?= $this->e($permission['name']) ?></span></label><?php endforeach; ?></div><button class="mt-6 rounded-xl bg-slate-900 px-4 py-3 text-white">Salvar relações</button></form></section>
