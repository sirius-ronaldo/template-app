<?php

declare(strict_types=1);

/** @var array<string,mixed> $group */
/** @var list<array<string,mixed>> $rows */
/** @var string $filter */
/** @var array<string,string> $errors */
/** @var App\Support\ViewRenderer $this */
$types = [];
foreach ($rows as $row) {
    $typeId = (int) $row['type_id'];
    $applicationId = (int) $row['application_id'];
    $types[$typeId]['code'] = $row['type_code'];
    $types[$typeId]['name'] = $row['type_name'];
    $types[$typeId]['applications'][$applicationId]['code'] = $row['application_code'];
    $types[$typeId]['applications'][$applicationId]['name'] = $row['application_name'];
    $types[$typeId]['applications'][$applicationId]['permissions'][] = $row;
}
ob_start();
?>
<section class="rounded-3xl bg-white p-8 shadow-sm"><div class="flex items-center justify-between gap-4"><div><h1 class="text-2xl font-bold">Grupos × Aplicações × Permissões</h1><p class="mt-2 text-sm text-slate-600">Grupo: <?= $this->e($group['code'] . ' — ' . $group['name']) ?></p></div><a href="/configuracoes/grupos" class="text-emerald-700">Voltar</a></div>
<?php if ($group['code'] === 'ADMIN') : ?><div class="mt-6 rounded-xl bg-emerald-50 p-4 text-emerald-800">O grupo ADMIN possui acesso irrestrito. A matriz de grants não é editável.</div>
<?php else : ?>
    <?php if (isset($errors['global'])) : ?><div class="mt-6 rounded-xl bg-rose-50 p-4 text-rose-800"><?= $this->e($errors['global']) ?></div><?php endif; ?>
    <form class="mt-6" method="get" action="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes" hx-get="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes" hx-target="#grant-matrix" hx-push-url="true"><input class="rounded-xl border px-4 py-2" name="filter" placeholder="Código ou nome da aplicação" value="<?= $this->e($filter) ?>"><button class="ml-2 rounded-xl bg-slate-900 px-4 py-2 text-white">Buscar</button></form>
    <form class="mt-6" method="post" action="/configuracoes/grupos/<?= (int) $group['id'] ?>/permissoes"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><div id="grant-matrix" class="space-y-3"><?php foreach ($types as $type) : ?><details open class="rounded-2xl border p-4"><summary class="cursor-pointer font-semibold"><?= $this->e($type['code'] . ' — ' . $type['name']) ?></summary><div class="mt-3 space-y-3"><?php foreach ($type['applications'] as $applicationId => $application) : ?><details open class="rounded-xl bg-slate-50 p-3"><summary class="cursor-pointer"><?= $this->e($application['code'] . ' — ' . $application['name']) ?></summary><div class="mt-3 grid gap-2 sm:grid-cols-2"><?php foreach ($application['permissions'] as $permission) : ?><label class="flex items-center gap-2"><input type="checkbox" name="permissions[<?= (int) $applicationId ?>][]" value="<?= (int) $permission['permission_id'] ?>" <?= (bool) $permission['granted'] ? 'checked' : '' ?>><?= $this->e($permission['permission_code'] . ' — ' . $permission['permission_name']) ?></label><?php endforeach; ?></div></details><?php endforeach; ?></div></details><?php endforeach; ?></div><button class="mt-6 rounded-xl bg-slate-900 px-4 py-2 text-white">Salvar permissões</button></form>
<?php endif; ?></section>
<?php
$content = (string) ob_get_clean();
echo $content;
