<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\DeliveryMessage;

interface QueueBus
{
    public function enqueue(DeliveryMessage $message): void;
}
