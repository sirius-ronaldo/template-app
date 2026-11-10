<?php

declare(strict_types=1);

$catalogBase = '/configuracoes/tipos';
$formTitle = $id === null ? 'Novo tipo' : 'Editar tipo';
$activeLabel = 'Tipo ativo';
require dirname(__DIR__) . '/_catalog-form.php';
