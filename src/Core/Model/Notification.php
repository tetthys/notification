<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

final readonly class Notification
{
    /**
     * @param list<string> $recipients
     * @param list<string> $channels Aggregated channels used.
     */
    public function __construct(
        public string $id,
        public string $source,
        public string $type,
        public array $recipients,
        public array $channels,
        public int $priority = 0,
        public \DateTimeImmutable $timestamp = new \DateTimeImmutable(),
        public NotificationStatus $status = NotificationStatus::Pending,
        public ?string $tenantId = null,
        public ?string $parentId = null,
    ) {}
}
