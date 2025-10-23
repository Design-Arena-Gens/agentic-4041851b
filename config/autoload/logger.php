<?php

declare(strict_types=1);

return [
    'default' => [
        'handler' => [
            'class' => Monolog\Handler\StreamHandler::class,
            'constructor' => [
                'stream' => env('LOG_FILE', 'php://stdout'),
                'level' => env('LOG_LEVEL', Monolog\Logger::DEBUG),
            ],
        ],
        'formatter' => [
            'class' => Monolog\Formatter\LineFormatter::class,
            'constructor' => [
                'format' => null,
                'allowInlineLineBreaks' => true,
            ],
        ],
    ],
];
