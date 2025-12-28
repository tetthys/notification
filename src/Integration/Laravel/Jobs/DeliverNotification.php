<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Tetthys\Notification\Core\Model\DeliveryMessage;
use Tetthys\Notification\Core\Service\DeliveryWorker;

final class DeliverNotification implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly DeliveryMessage $message
    ) {}

    public function handle(DeliveryWorker $worker): void
    {
        // Execute the Core pipeline in a retryable job context.
        $worker->handle($this->message);
    }
}
