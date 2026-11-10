<?php

declare(strict_types=1);

/** @var bool $valid */
/** @var list<string> $diagnostics */
/** @var App\Manifest\ManifestDiff|null $diff */
/** @var array<string,int>|null $counts */
/** @var App\Support\ViewRenderer $this */
ob_start();
?>
<header class="ui-page-header"><div><p class="ui-eyebrow">Sincronização estrutural</p><h1 class="ui-page-title">Manifesto de segurança</h1><p class="ui-page-description">Fonte: <code>config/security/manifest.yaml</code></p></div><a class="ui-button ui-button--secondary" href="/configuracoes/seguranca">Voltar</a></header>
<section class="ui-card ui-card--padded"><div class="ui-actions"><form method="post" action="/security/manifest/validate"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="ui-button ui-button--secondary" type="submit">Validar</button></form><form method="post" action="/security/manifest/preview"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="ui-button ui-button--secondary" type="submit">Gerar prévia</button></form><?php if ($valid) : ?><form method="post" action="/security/manifest/apply"><input type="hidden" name="_csrf" value="<?= $this->e($this->csrfToken()) ?>"><button class="ui-button ui-button--primary" type="submit">Aplicar manifesto</button></form><?php endif; ?></div>
<?php if (!$valid || $diagnostics !== []) : ?><div class="ui-flash ui-flash--error" role="alert" style="margin-top: 20px"><strong>Diagnósticos</strong><ul><?php foreach ($diagnostics as $diagnostic) : ?><li><?= $this->e($diagnostic) ?></li><?php endforeach; ?></ul></div><?php endif; ?>
<?php if ($diff !== null) : ?><div class="ui-card ui-card--compact" style="margin-top: 20px"><h2 class="ui-card__title">Prévia das alterações</h2><div class="ui-grid ui-grid--metrics" style="margin-top: 16px"><div class="ui-metric"><span>Permissões</span><strong><?= (int) $diff->counts['permissoes_inserir_atualizar'] ?></strong></div><div class="ui-metric"><span>Tipos</span><strong><?= (int) $diff->counts['tipos_inserir_atualizar'] ?></strong></div><div class="ui-metric"><span>Aplicações</span><strong><?= (int) $diff->counts['aplicacoes_inserir_atualizar'] ?></strong></div><div class="ui-metric"><span>Relações</span><strong><?= (int) $diff->counts['relacoes_alterar'] ?></strong></div></div><?php foreach ($diff->missing as $section => $items) : ?><?php if ($items !== []) : ?><p class="ui-help"><strong><?= $this->e($section) ?> ausentes:</strong> <?= $this->e(implode(', ', $items)) ?>. Nenhuma ação destrutiva será executada.</p><?php endif; ?><?php endforeach; ?></div><?php endif; ?>
<?php if ($counts !== null) : ?><div class="ui-flash ui-flash--success" role="status" style="margin-top: 20px"><strong>Sincronização concluída</strong><span>O manifesto foi aplicado com sucesso.</span></div><?php endif; ?>
</section>
<?php
$content = (string) ob_get_clean();
echo $content;
