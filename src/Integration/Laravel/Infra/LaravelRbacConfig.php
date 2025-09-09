<?php

namespace Tetthys\Notification\Integration\Laravel\Infra;

use Tetthys\Notification\Core\Contracts\RbacPolicy;

final class LaravelRbacConfig implements RbacPolicy
{
    public function assertCanSend(string $callerRole, string $notificationType): void
    {
        $map = config('notifications.rbac', []);
        $allowed = $map[$notificationType] ?? [];
        if (!in_array($callerRole, $allowed, true)) {
            throw new \RuntimeException('Not authorized');
        }
    }
}