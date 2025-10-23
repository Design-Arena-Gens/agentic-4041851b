<?php

declare(strict_types=1);

require dirname(__DIR__) . '/bootstrap.php';
require dirname(__DIR__) . '/vendor/autoload.php';

echo json_encode(['status' => 'socketio ready']);
