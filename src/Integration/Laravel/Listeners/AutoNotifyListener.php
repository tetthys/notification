<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Listeners;

use Tetthys\Notification\Core\Contracts\Notifies;
use Tetthys\Notification\Core\Contracts\NotifiesChannels;
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

        $channelsOverride = null;
        if ($event instanceof NotifiesChannels) {
            $channelsOverride = $event->notificationChannels();
        }

        Notify::trigger(
            callerRole: (string) config('tetthys-notification.auto_listener.caller_role', 'system'),
            type: $event->notificationType(),
            recipients: $event->recipients(),
            data: $event->notificationData(),
            channelsOverride: $channelsOverride,
            source: (string) config('tetthys-notification.auto_listener.source', 'App'),
            tenantId: $event->tenantId(),
            parentId: $event->parentId(),
        );
    }
}
