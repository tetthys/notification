<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\Notification;

/**
 * A concrete delivery channel adapter (email, sms, push, webhook, ...).
 */
interface Channel
{
    /**
     * Return unique channel name (e.g. "email", "sms", "push").
     */
    public function name(): string;

    /**
     * Send a notification to a recipient using a channel-specific payload.
     *
     * Implementations should throw on failure to allow queue retries.
     *
     * @param array<string, mixed> $payload
     */
    public function send(Notification $notification, array $payload, string $recipientId): void;
}
