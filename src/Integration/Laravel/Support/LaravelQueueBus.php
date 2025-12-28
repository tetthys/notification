<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Support;

use Tetthys\Notification\Core\Contracts\QueueBus;
use Tetthys\Notification\Core\Model\DeliveryMessage;
use Tetthys\Notification\Integration\Laravel\Jobs\DeliverNotification;

final class LaravelQueueBus implements QueueBus
{
    public function enqueue(DeliveryMessage $message): void
    {
        $job = new DeliverNotification($message);

        $conn = config('tetthys-notification.queue.connection');
        $queue = config('tetthys-notification.queue.queue');

        if ($conn) {
            $job->onConnection((string) $conn);
        }
        if ($queue) {
            $job->onQueue((string) $queue);
        }

        dispatch($job);
    }
}
