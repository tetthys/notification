<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Stores and enforces idempotency and delivery states.
 *
 * Core goal: prevent duplicate sends under at-least-once queue semantics.
 */
interface DeliveryStore
{
    /**
     * Claim a delivery for processing (idempotency gate).
     *
     * Must return false if the delivery has already been claimed/sent, to stop duplicates.
     */
    public function claim(string $notificationId, string $recipientId, string $channel): bool;

    /**
     * Mark a claimed delivery as successfully sent.
     */
    public function markSent(string $notificationId, string $recipientId, string $channel): void;

    /**
     * Mark a claimed delivery as failed.
     */
    public function markFailed(
        string $notificationId,
        string $recipientId,
        string $channel,
        string $reason,
    ): void;
}
