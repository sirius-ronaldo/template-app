<?php

declare(strict_types=1);

/** @var array<string,mixed> $item */
/** @var int|null $id */
/** @var array<string,string> $errors */
/** @var App\Support\ViewRenderer $this */
$action = $id === null ? '/configuracoes/permissoes' : '/configuracoes/permissoes/' . $id;
?>
<section class="mx-auto max-w-2xl rounded-3xl bg-white p-8 shadow-sm"><h1 class="text-2xl font-bold"><?= $id === null ? 'Nova permissão' : 'Editar permissão' ?></h1><form class="mt-6" method="post" action="<?= $this->e($action) ?>"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><label class="block text-sm">Código<input class="mt-2 w-full rounded-xl border p-3" name="code" value="<?= $this->e($item['code']) ?>" required></label><?php if (isset($errors['code'])) : ?><p class="text-sm text-rose-700"><?= $this->e($errors['code']) ?></p><?php endif; ?><label class="mt-4 block text-sm">Nome<input class="mt-2 w-full rounded-xl border p-3" name="name" value="<?= $this->e($item['name']) ?>" required></label><label class="mt-4 block text-sm">Descrição<textarea class="mt-2 w-full rounded-xl border p-3" name="description"><?= $this->e($item['description']) ?></textarea></label><label class="mt-4 block"><input type="checkbox" name="active" value="1" <?= (bool) $item['active'] ? 'checked' : '' ?>> Ativa</label><button class="mt-6 rounded-xl bg-slate-900 px-4 py-3 text-white">Salvar</button></form></section>
