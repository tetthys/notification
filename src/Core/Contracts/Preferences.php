<?php

namespace Tetthys\Notification\Core\Contracts;

interface Preferences
{
    /**
     * Retrieves the list of disabled channels for a given user and notification type.
     *
     * @param string $userId The unique identifier of the user.
     * @param string $notificationType The type/category of the notification.
     *
     * @return array List of disabled channel names.
     */
    public function disabledChannelsFor(string $userId, string $notificationType): array;
}
