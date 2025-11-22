<?php

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\Notification;

interface Channel
{
    /**
     * Returns the name/identifier of the channel.
     *
     * @return string The channel name.
     */
    public function name(): string;

    /**
     * Sends the notification to the specified recipient with the given payload.
     *
     * @param Notification $notification The notification instance to be sent.
     * @param array $payload The content payload for the notification.
     * @param string $recipientId The unique identifier of the recipient.
     *
     * @return void
     */
    public function send(Notification $notification, array $payload, string $recipientId): void;
}
