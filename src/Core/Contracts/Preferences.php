<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Recipient preferences for notification delivery.
 */
interface Preferences
{
    /**
     * Return a list of disabled channel names for a user and notification type.
     *
     * @return list<string>
     */
    public function disabledChannelsFor(string $userId, string $notificationType): array;
}
