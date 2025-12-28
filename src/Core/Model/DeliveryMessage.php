<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

/**
 * Represents a single delivery unit (recipient x channel).
 *
 * This is what should be enqueued and processed by workers.
 */
final readonly class DeliveryMessage
{
    /**
     * @param array<string, mixed> $data Minimal template input data.
     */
    public function __construct(
        public string $notificationId,
        public string $source,
        public string $type,
        public string $recipientId,
        public string $channel,
        public array $data,
        public int $priority = 0,
        public ?string $parentId = null,
        public ?string $tenantId = null,
        public \DateTimeImmutable $timestamp = new \DateTimeImmutable(),
    ) {}
}
