<?php

declare(strict_types=1);

namespace App\SocketIO\Namespaces;

use App\Validation\SocketValidated;
use Hyperf\Context\Context;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\Socket;
use Hyperf\SocketIOServer\SocketIOConfig;
use Hyperf\SocketIOServer\SidProvider\SidProviderInterface;
use Hyperf\WebSocketServer\Sender;

class ChatNamespace extends BaseNamespace
{
    public function __construct(Sender $sender, SidProviderInterface $sidProvider, ?SocketIOConfig $config = null)
    {
        parent::__construct($sender, $sidProvider, $config);

        $this->on('connect', static function (Socket $socket) {
            $socket->emit('system:welcome', [
                'message' => 'Connected to Hyperf Socket.IO server with automatic validation.',
            ]);
        });

        $this->on('message:send', [$this, 'onMessageSend']);
    }

    #[SocketValidated(
        rules: [
            'room' => 'required|string|max:50',
            'message' => 'required|string|max:500',
            'author' => 'required|string|max:50',
        ],
        payloadArgument: 'data'
    )]
    public function onMessageSend(Socket $socket, array $data): void
    {
        $validated = Context::get('socket.validated', $data);

        $room = $validated['room'];
        $socket->join($room);

        $socket->to($room)->emit('message:received', [
            'room' => $room,
            'message' => $validated['message'],
            'author' => $validated['author'],
            'timestamp' => microtime(true),
        ]);
    }
}
