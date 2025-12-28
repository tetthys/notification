<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\DeliveryMessage;

/**
 * Abstraction over the queue mechanism used to fan-out and process deliveries.
 */
interface QueueBus
{
    /**
     * Enqueue a single delivery message (recipient x channel).
     */
    public function enqueueDelivery(DeliveryMessage $message): void;
}
