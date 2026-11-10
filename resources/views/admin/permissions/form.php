<?php

declare(strict_types=1);

$catalogBase = '/configuracoes/permissoes';
$formTitle = $id === null ? 'Nova permissão' : 'Editar permissão';
$activeLabel = 'Permissão ativa';
require dirname(__DIR__) . '/_catalog-form.php';
