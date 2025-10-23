<?php

declare(strict_types=1);

namespace App\Validation;

use Attribute;

#[Attribute(Attribute::TARGET_METHOD)]
class SocketValidated
{
    public function __construct(
        public array $rules,
        public array $messages = [],
        public array $customAttributes = [],
        public string $payloadArgument = 'data',
        public string $contextKey = 'socket.validated',
        public string $errorEvent = 'validation:error'
    ) {
    }
}
