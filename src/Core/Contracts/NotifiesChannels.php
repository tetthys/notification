<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

interface NotifiesChannels
{
    /**
     * Return channels override (e.g. ['database','email']).
     * Return null to use default channels.
     *
     * @return list<string>|null
     */
    public function notificationChannels(): ?array;
}
