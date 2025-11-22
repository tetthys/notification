<?php

namespace Tetthys\Notification\Core\Contracts;

interface RbacPolicy
{
    /**
     * Asserts that the caller with the given role is authorized to send notifications of the specified type.
     * 
     * @param string $callerRole The role of the caller attempting to send the notification.
     * @param string $notificationType The type/category of the notification.
     */
    public function assertCanSend(string $callerRole, string $notificationType): void;
}
