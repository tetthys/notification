<?php

namespace Tetthys\Notification\Integration\Laravel;

use Tetthys\Notification\Core\NotificationService;

final class LaravelNotifier
{
    public function __construct(private NotificationService $core) {}

    public function send(array $payload)
    {
        return $this->core->trigger(
            callerRole: $payload['callerRole'] ?? 'App',
            type:       $payload['type'],
            recipients: $payload['recipients'],
            data:       $payload['data'] ?? [],
            defaultChs: $payload['defaults'] ?? ['email'],
            source:     $payload['source'] ?? 'App'
        );
    }
}