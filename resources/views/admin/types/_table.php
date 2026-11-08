<?php

declare(strict_types=1);

/** @var list<array<string,mixed>> $items */
/** @var App\Support\Pagination $pagination */
/** @var App\Support\ViewRenderer $this */
?>
<div class="overflow-x-auto rounded-2xl border"><table class="w-full text-left text-sm"><thead class="bg-slate-50"><tr><th class="p-3">Código</th><th class="p-3">Nome</th><th class="p-3">Status</th><th class="p-3">Ações</th></tr></thead><tbody class="divide-y"><?php foreach ($items as $item) : ?><tr><td class="p-3"><?= $this->e($item['code']) ?></td><td class="p-3"><?= $this->e($item['name']) ?></td><td class="p-3"><?= (bool) $item['active'] ? 'Ativo' : 'Inativo' ?></td><td class="p-3"><a class="mr-3 text-emerald-700" href="/configuracoes/tipos/<?= (int) $item['id'] ?>/editar">Editar</a><a class="mr-3 text-emerald-700" href="/configuracoes/tipos/<?= (int) $item['id'] ?>/permissoes">Permissões</a><form class="mr-3 inline" method="post" action="/configuracoes/tipos/<?= (int) $item['id'] ?>/status"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><input type="hidden" name="active" value="<?= (bool) $item['active'] ? '0' : '1' ?>"><button class="text-emerald-700" type="submit"><?= (bool) $item['active'] ? 'Inativar' : 'Ativar' ?></button></form><form class="inline" method="post" action="/configuracoes/tipos/<?= (int) $item['id'] ?>/excluir"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="text-rose-700" type="submit">Excluir</button></form></td></tr><?php endforeach; ?></tbody></table><p class="p-3 text-sm">Página <?= $pagination->page ?> de <?= $pagination->pages() ?></p></div>
