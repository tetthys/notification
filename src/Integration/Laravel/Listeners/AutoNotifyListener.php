<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Listeners;

use Tetthys\Notification\Core\Contracts\Notifies;
use Tetthys\Notification\Integration\Laravel\Facades\Notify;

final class AutoNotifyListener
{
    /**
     * Wildcard listener signature.
     *
     * @param array<int, mixed> $data
     */
    public function handle(string $eventName, array $data): void
    {
        $event = $data[0] ?? null;
        if (!$event instanceof Notifies) {
            return;
        }

        Notify::trigger(
            callerRole: (string) config('tetthys-notification.auto_listener.caller_role', 'system'),
            type: $event->notificationType(),
            recipients: $event->recipients(),
            data: $event->notificationData(),
            channelsOverride: null, // 기본 채널(database) 사용
            source: (string) config('tetthys-notification.auto_listener.source', 'App'),
            tenantId: $event->tenantId(),
            parentId: $event->parentId(),
        );
    }
}
