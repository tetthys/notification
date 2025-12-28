<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

enum NotificationStatus: string
{
    case Pending = 'pending';
    case Queued  = 'queued';
    case Processing = 'processing';
    case Done = 'done';
}
