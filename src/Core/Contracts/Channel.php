<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\Notification;

interface Channel
{
    public function name(): string;

    /**
     * @param array<string, mixed> $payload
     */
    public function send(Notification $notification, array $payload, string $recipientId): void;
}
