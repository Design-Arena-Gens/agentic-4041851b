<?php

declare(strict_types=1);

namespace App\Aspect;

use App\Validation\SocketValidated;
use Hyperf\Context\Context;
use Hyperf\Di\Aop\AbstractAspect;
use Hyperf\Di\Aop\ProceedingJoinPoint;
use Hyperf\Di\Annotation\Aspect;
use Hyperf\Support\Arr;
use Hyperf\SocketIOServer\Socket;
use Hyperf\Validation\Contract\ValidatorFactoryInterface;
use Psr\Log\LoggerInterface;

#[Aspect]
class SocketValidatedAspect extends AbstractAspect
{
    public array $annotations = [
        SocketValidated::class,
    ];

    public function __construct(
        private readonly ValidatorFactoryInterface $validatorFactory,
        private readonly LoggerInterface $logger,
    ) {
    }

    public function process(ProceedingJoinPoint $proceedingJoinPoint)
    {
        $reflection = $proceedingJoinPoint->getReflector();
        $attribute = $this->resolveAttribute($reflection);

        if (! $attribute instanceof SocketValidated) {
            return $proceedingJoinPoint->process();
        }

        $arguments = $proceedingJoinPoint->arguments;
        $argumentMeta = $this->resolveArgumentMeta($reflection, $arguments);

        $socket = $argumentMeta['socket'];
        $payloadKey = $attribute->payloadArgument;

        if (! array_key_exists($payloadKey, $arguments['keys'])) {
            $this->logger->warning(sprintf(
                'Unable to locate payload argument "%s" for %s::%s',
                $payloadKey,
                $reflection->getDeclaringClass()->getName(),
                $reflection->getName()
            ));

            return $proceedingJoinPoint->process();
        }

        $payloadIndex = $argumentMeta['indexes'][$payloadKey] ?? null;
        $payload = $arguments['keys'][$payloadKey];

        if (! is_array($payload)) {
            $payload = Arr::wrap($payload);
        }

        $validator = $this->validatorFactory->make(
            $payload,
            $attribute->rules,
            $attribute->messages,
            $attribute->customAttributes
        );

        if ($validator->fails()) {
            $errors = $validator->errors()->toArray();
            $message = 'Validation failed';

            if ($socket instanceof Socket) {
                $socket->emit($attribute->errorEvent, [
                    'message' => $message,
                    'errors' => $errors,
                ]);
            }

            Context::set($attribute->contextKey, null);

            return null;
        }

        $validated = $validator->validated();
        $arguments['keys'][$payloadKey] = $validated;
        if ($payloadIndex !== null) {
            $arguments['values'][$payloadIndex] = $validated;
        }

        Context::set($attribute->contextKey, $validated);

        try {
            return $proceedingJoinPoint->process($arguments);
        } finally {
            Context::set($attribute->contextKey, null);
        }
    }

    private function resolveAttribute(\ReflectionMethod $reflection): ?SocketValidated
    {
        $attributes = $reflection->getAttributes(SocketValidated::class);
        if ($attributes === []) {
            return null;
        }

        $attribute = $attributes[0]->newInstance();
        if ($attribute instanceof SocketValidated) {
            return $attribute;
        }

        return null;
    }

    private function resolveArgumentMeta(\ReflectionMethod $reflection, array $arguments): array
    {
        $indexes = [];
        $socket = null;
        foreach ($reflection->getParameters() as $index => $parameter) {
            $name = $parameter->getName();
            $indexes[$name] = $index;

            if ($socket === null && array_key_exists($name, $arguments['keys'])) {
                $value = $arguments['keys'][$name];
                if ($value instanceof Socket) {
                    $socket = $value;
                }
            }
        }

        return [
            'indexes' => $indexes,
            'socket' => $socket,
        ];
    }
}
