<?php

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\Notification;

interface Channel
{
    public function name(): string;
    public function send(
        Notification $notification,
        array $payload,
        string $recipientId,
    ): void;
}
