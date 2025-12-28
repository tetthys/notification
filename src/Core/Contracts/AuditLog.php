<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Optional: audit log for notification events (triggered, queued, sent, failed).
 *
 * Keep it separate from DeliveryStore because audit retention and schema often differ.
 */
interface AuditLog
{
    /**
     * Record that a notification was triggered.
     *
     * @param list<string> $recipients
     * @param list<string> $channels
     * @param array<string, mixed> $meta
     */
    public function notificationTriggered(
        string $notificationId,
        string $source,
        string $type,
        array $recipients,
        array $channels,
        array $meta = [],
    ): void;

    /**
     * Record that a delivery was queued.
     *
     * @param array<string, mixed> $meta
     */
    public function deliveryQueued(
        string $notificationId,
        string $recipientId,
        string $channel,
        array $meta = [],
    ): void;

    /**
     * Record that a delivery was sent.
     *
     * @param array<string, mixed> $meta
     */
    public function deliverySent(
        string $notificationId,
        string $recipientId,
        string $channel,
        array $meta = [],
    ): void;

    /**
     * Record that a delivery failed.
     *
     * @param array<string, mixed> $meta
     */
    public function deliveryFailed(
        string $notificationId,
        string $recipientId,
        string $channel,
        string $reason,
        array $meta = [],
    ): void;
}
