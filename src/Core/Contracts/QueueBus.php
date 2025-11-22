<?php

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\Notification;

interface QueueBus
{
    /**
     * Enqueues the given notification for processing on the specified channel.
     *
     * @param string $channel The channel through which the notification will be sent.
     * @param Notification $notification The notification instance to enqueue.
     *
     * @return void
     */
    public function enqueue(string $channel, Notification $notification): void;
}
