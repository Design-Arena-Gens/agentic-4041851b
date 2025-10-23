<?php

declare(strict_types=1);

$paths = glob(__DIR__ . '/autoload/*.php');
$configs = [];

foreach ($paths as $path) {
    $key = basename($path, '.php');
    $configs[$key] = include $path;
}

$configs['routes'] = include __DIR__ . '/routes.php';

return $configs;
