<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

/**
 * Queue payload: one unit of work (recipient x channel).
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
        public ?string $tenantId = null,
        public ?string $parentId = null,
        public ?string $locale = null,
        public \DateTimeImmutable $timestamp = new \DateTimeImmutable(),
    ) {}
}
