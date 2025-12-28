<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

interface Notifies
{
    /**
     * @return list<string> recipient ids
     */
    public function recipients(): array;

    /**
     * Domain notification type (e.g. "order.paid")
     */
    public function notificationType(): string;

    /**
     * @return array<string, mixed>
     */
    public function notificationData(): array;

    public function tenantId(): ?string;

    public function parentId(): ?string;
}
