<?php

declare(strict_types=1);

use App\SocketIO\Namespaces\ChatNamespace;

return [
    'server' => [
        'public_path' => BASE_PATH . '/public',
        'handle_listener' => [],
    ],
    'namespaces' => [
        '/' => ChatNamespace::class,
    ],
];
