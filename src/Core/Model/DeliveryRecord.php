<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

/**
 * Optional: delivery state snapshot for persistence layers.
 */
final readonly class DeliveryRecord
{
    public function __construct(
        public string $notificationId,
        public string $recipientId,
        public string $channel,
        public DeliveryStatus $status,
        public ?string $reason = null,
        public ?\DateTimeImmutable $claimedAt = null,
        public ?\DateTimeImmutable $sentAt = null,
        public ?\DateTimeImmutable $failedAt = null,
    ) {}
}
