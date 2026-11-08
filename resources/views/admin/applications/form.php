<?php

declare(strict_types=1);

/** @var array<string,mixed> $item */
/** @var list<array<string,mixed>> $types */
/** @var array<string,string> $errors */
/** @var int|null $id */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<section class="max-w-2xl rounded-3xl bg-white p-8 shadow-sm"><h1 class="text-2xl font-bold"><?= $id === null ? 'Nova aplicação' : 'Editar aplicação' ?></h1><?php if (isset($errors['global'])) : ?><p class="mt-4 rounded-xl bg-rose-50 p-3 text-rose-800"><?= $this->e($errors['global']) ?></p><?php endif; ?><form class="mt-6 space-y-4" method="post" action="<?= $id === null ? '/configuracoes/aplicacoes' : '/configuracoes/aplicacoes/' . (int) $id ?>"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><div><label>Código</label><input class="mt-1 w-full rounded-xl border px-4 py-2" name="code" value="<?= $this->e($item['code']) ?>" required><?php if (isset($errors['code'])) : ?><p class="text-sm text-rose-700"><?= $this->e($errors['code']) ?></p><?php endif; ?></div><div><label>Nome</label><input class="mt-1 w-full rounded-xl border px-4 py-2" name="name" value="<?= $this->e($item['name']) ?>" required><?php if (isset($errors['name'])) : ?><p class="text-sm text-rose-700"><?= $this->e($errors['name']) ?></p><?php endif; ?></div><div><label>Tipo</label><select class="mt-1 w-full rounded-xl border px-4 py-2" name="type_id" required><option value="">Selecione</option><?php foreach ($types as $type) : ?><option value="<?= (int) $type['id'] ?>" <?= (int) $item['type_id'] === (int) $type['id'] ? 'selected' : '' ?>><?= $this->e($type['code'] . ' — ' . $type['name']) ?></option><?php endforeach; ?></select><?php if (isset($errors['type_id'])) : ?><p class="text-sm text-rose-700"><?= $this->e($errors['type_id']) ?></p><?php endif; ?></div><div><label>Descrição</label><textarea class="mt-1 w-full rounded-xl border px-4 py-2" name="description"><?= $this->e($item['description']) ?></textarea></div><label class="flex items-center gap-2"><input type="checkbox" name="active" value="1" <?= (bool) $item['active'] ? 'checked' : '' ?>> Ativo</label><button class="rounded-xl bg-slate-900 px-4 py-2 text-white">Salvar</button></form></section>
<?php
$content = (string) ob_get_clean();
echo $content;
