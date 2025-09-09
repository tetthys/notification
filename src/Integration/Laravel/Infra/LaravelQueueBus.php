<?php

namespace Tetthys\Notification\Integration\Laravel\Infra;

use Tetthys\Notification\Core\Contracts\QueueBus;
use Tetthys\Notification\Core\Model\Notification;
use Tetthys\Notification\Integration\Laravel\Jobs\SendChannelJob;

final class LaravelQueueBus implements QueueBus
{
    public function enqueue(string $channel, Notification $notification): void
    {
        SendChannelJob::dispatch($channel, $notification);
    }
}