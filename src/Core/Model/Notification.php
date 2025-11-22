<?php

namespace Tetthys\Notification\Core\Model;

final class Notification
{
    /**
     * Constructs a new Notification instance.
     * 
     * @param string $id The unique identifier for the notification.
     * @param string $source The source of the notification.
     * @param string $type The type/category of the notification.
     * @param array $recipients List of recipients for the notification.
     * @param array $channels Channels through which the notification will be sent.
     * @param array $content Rendered content for each channel.
     * @param int $priority Priority level of the notification.
     * @param \DateTimeImmutable $timestamp Timestamp when the notification was created.
     * @param string $status Current status of the notification.
     * @param string|null $parentId Optional parent notification ID.
     * @param string|null $tenantId Optional tenant ID for multi-tenant systems.
     * 
     * @return void
     */
    public function __construct(
        public string $id,
        public string $source,
        public string $type,
        public array $recipients,
        public array $channels,
        public array $content,
        public int $priority = 0,
        public \DateTimeImmutable $timestamp = new \DateTimeImmutable(),
        public string $status = "Pending",
        public ?string $parentId = null,
        public ?string $tenantId = null,
    ) {}
}
