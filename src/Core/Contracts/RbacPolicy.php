<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * RBAC / authorization policy for sending notifications.
 */
interface RbacPolicy
{
    /**
     * Assert that the caller is allowed to send notifications of the given type.
     *
     * Implementations should throw on denial.
     */
    public function assertCanSend(string $callerRole, string $notificationType): void;
}
