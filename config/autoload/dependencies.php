<?php

declare(strict_types=1);

use App\SocketIO\Namespaces\ChatNamespace;
use Hyperf\SocketIOServer\Room\AdapterInterface;
use Hyperf\SocketIOServer\Room\MemoryAdapter;

return [
    AdapterInterface::class => MemoryAdapter::class,
    ChatNamespace::class => ChatNamespace::class,
];
