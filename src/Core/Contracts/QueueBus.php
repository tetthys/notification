<?php

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\Notification;

interface QueueBus
{
    public function enqueue(string $channel, Notification $notification): void;
}
