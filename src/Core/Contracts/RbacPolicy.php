<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

interface RbacPolicy
{
    public function assertCanSend(string $callerRole, string $notificationType): void;
}
