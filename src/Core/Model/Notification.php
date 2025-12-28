<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

/**
 * Represents a high-level notification intent.
 *
 * This is a trace/audit envelope. Actual send unit is DeliveryMessage (recipient x channel).
 */
final readonly class Notification
{
    /**
     * @param list<string> $recipients Recipient user IDs.
     * @param list<string> $channels Aggregated effective channels used for this notification.
     * @param array<string, array<string, mixed>> $content Optional pre-rendered content per channel.
     */
    public function __construct(
        public string $id,
        public string $source,
        public string $type,
        public array $recipients,
        public array $channels,
        public array $content = [],
        public int $priority = 0,
        public \DateTimeImmutable $timestamp = new \DateTimeImmutable(),
        public NotificationStatus $status = NotificationStatus::Pending,
        public ?string $parentId = null,
        public ?string $tenantId = null,
    ) {}
}
