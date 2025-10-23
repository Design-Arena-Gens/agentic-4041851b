<?php

declare(strict_types=1);

return [
    'mode' => defined('SWOOLE_PROCESS') ? SWOOLE_PROCESS : 3,
    'servers' => [
        [
            'name' => 'socket-io',
            'type' => Hyperf\Server\Server::SERVER_WEBSOCKET,
            'host' => env('SOCKET_HOST', '0.0.0.0'),
            'port' => (int) env('SOCKET_PORT', 9502),
            'sock_type' => defined('SWOOLE_SOCK_TCP') ? SWOOLE_SOCK_TCP : 1,
            'callbacks' => [],
            'settings' => [
                'enable_http2' => false,
            ],
        ],
    ],
    'settings' => [
        'worker_num' => function_exists('swoole_cpu_num') ? swoole_cpu_num() : 1,
        'task_worker_num' => 0,
    ],
];
