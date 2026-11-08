<?php

declare(strict_types=1);

/** @var bool $valid */
/** @var list<string> $diagnostics */
/** @var App\Manifest\ManifestDiff|null $diff */
/** @var array<string,int>|null $counts */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<section class="max-w-4xl rounded-3xl bg-white p-8 shadow-sm"><h1 class="text-2xl font-bold">Manifesto de segurança</h1><p class="mt-2 text-sm text-slate-600">Fonte estrutural: <code>config/security/manifest.yaml</code></p><div class="mt-6 flex gap-3"><form method="post" action="/security/manifest/validate"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="rounded-xl border px-4 py-2">Validar</button></form><form method="post" action="/security/manifest/preview"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="rounded-xl border px-4 py-2">Prévia</button></form><?php if ($valid) : ?><form method="post" action="/security/manifest/apply"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="rounded-xl bg-slate-900 px-4 py-2 text-white">Aplicar</button></form><?php endif; ?></div><?php if (!$valid || $diagnostics !== []) : ?><div class="mt-6 rounded-xl bg-rose-50 p-4 text-rose-800"><h2 class="font-semibold">Diagnósticos</h2><ul class="mt-2 list-disc pl-5"><?php foreach ($diagnostics as $diagnostic) : ?><li><?= $this->e($diagnostic) ?></li><?php endforeach; ?></ul></div><?php endif; ?><?php if ($diff !== null) : ?><div class="mt-6 rounded-xl bg-slate-50 p-4"><h2 class="font-semibold">Prévia das alterações</h2><ul class="mt-2 list-disc pl-5"><li>Permissões: <?= (int) $diff->counts['permissoes_inserir_atualizar'] ?></li><li>Tipos: <?= (int) $diff->counts['tipos_inserir_atualizar'] ?></li><li>Aplicações: <?= (int) $diff->counts['aplicacoes_inserir_atualizar'] ?></li><li>Relações alteradas: <?= (int) $diff->counts['relacoes_alterar'] ?></li></ul><?php foreach ($diff->missing as $section => $items) : ?><?php if ($items !== []) : ?><p class="mt-3 text-sm"><strong><?= $this->e($section) ?> ausentes no manifesto:</strong> <?= $this->e(implode(', ', $items)) ?>. Nenhuma ação destrutiva será executada.</p><?php endif; ?><?php endforeach; ?></div><?php endif; ?><?php if ($counts !== null) : ?><div class="mt-6 rounded-xl bg-emerald-50 p-4 text-emerald-800">Sincronização aplicada com sucesso.</div><?php endif; ?></section>
<?php
$content = (string) ob_get_clean();
echo $content;
