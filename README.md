# Hyperf Socket.IO Auto Validation

This project delivers a Socket.IO server on top of the Hyperf framework that automatically validates incoming event payloads. A reusable `#[SocketValidated]` attribute decorates event handlers to enforce validation rules before business logic runs. Invalid payloads are never processed; instead, clients receive structured `validation:error` events.

## Requirements

- PHP 8.1+
- Composer
- Swoole extension (>= 5.0) enabled for PHP

## Install & Run

```bash
composer install
cp .env.example .env
php bin/hyperf.php start
```

The Socket.IO server listens on `0.0.0.0:9502` by default. You can adjust `SOCKET_HOST` and `SOCKET_PORT` in `.env`.

A simple browser client is available at `public/index.html`. Open it and connect to `http://localhost:9502` to publish chat messages that pass through the validation pipeline.

## Project Highlights

- `src/Validation/SocketValidated.php` – attribute describing validation rules and response options.
- `src/Aspect/SocketValidatedAspect.php` – AOP aspect that validates payloads, shares sanitized data via `Context`, and emits consistent error responses.
- `src/SocketIO/Namespaces/ChatNamespace.php` – example namespace that relies on validated payloads to broadcast chat messages.

Extend the behavior by attaching `#[SocketValidated]` to any namespace event handler and supplying your validation rules.
