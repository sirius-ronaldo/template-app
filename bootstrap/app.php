<?php

declare(strict_types=1);

use App\Config\AppFactory;
use App\Support\Request;
use App\Support\Session;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
date_default_timezone_set($_ENV['APP_TIMEZONE'] ?? 'America/Sao_Paulo');

$container = AppFactory::build();
$container->get(Session::class)->start();
$request = Request::capture();
