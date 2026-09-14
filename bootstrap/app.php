<?php

declare(strict_types=1);

use App\Config\AppConfig;
use App\Config\AppFactory;
use App\Support\Request;
use App\Support\Session;
use Dotenv\Dotenv;

require dirname(__DIR__) . '/vendor/autoload.php';

Dotenv::createImmutable(dirname(__DIR__))->safeLoad();
$config = AppConfig::fromEnvironment($_ENV, dirname(__DIR__));
date_default_timezone_set($config->timezone);

$container = AppFactory::build($config);
$container->get(Session::class)->start();
$request = Request::capture();
