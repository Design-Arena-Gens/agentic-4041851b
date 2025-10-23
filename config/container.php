<?php

declare(strict_types=1);

use Hyperf\Config\Config;
use Hyperf\Contract\ConfigInterface;
use Hyperf\Contract\StdoutLoggerInterface;
use Hyperf\Di\Definition\FactoryDefinition;
use Hyperf\Logger\Logger;
use Hyperf\Logger\LoggerFactory;

return [
    StdoutLoggerInterface::class => static function (LoggerFactory $factory): Logger {
        return $factory->get('log', 'default');
    },
    ConfigInterface::class => new FactoryDefinition(function () {
        return new Config(include dirname(__DIR__) . '/config/packages.php');
    }),
];
