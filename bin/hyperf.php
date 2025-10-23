#!/usr/bin/env php
<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Hyperf\Di\Container;
use Hyperf\Di\ContainerConfig;
use Hyperf\Server\Command\StartServer;

require_once dirname(__DIR__) . '/bootstrap.php';
require_once dirname(__DIR__) . '/vendor/autoload.php';

if (is_file($env = dirname(__DIR__) . '/.env')) {
    Dotenv::createImmutable(dirname(__DIR__))->load();
}

$container = new Container((new ContainerConfig())->setDefinitions(
    require dirname(__DIR__) . '/config/container.php'
));

/** @var StartServer $command */
$command = $container->get(StartServer::class);
$command->run();
