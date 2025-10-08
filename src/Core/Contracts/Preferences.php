<?php

namespace Tetthys\Notification\Core\Contracts;

interface Preferences
{
    public function disabledChannelsFor(
        string $userId,
        string $notificationType,
    ): array;
}
