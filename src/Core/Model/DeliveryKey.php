<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

/**
 * Idempotency key for a delivery.
 */
final readonly class DeliveryKey
{
    public function __construct(
        public string $notificationId,
        public string $recipientId,
        public string $channel,
    ) {}
}
