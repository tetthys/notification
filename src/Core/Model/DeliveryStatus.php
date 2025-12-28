<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

enum DeliveryStatus: string
{
    case Pending = 'pending';
    case Processing = 'processing';
    case Sent = 'sent';
    case Failed = 'failed';
}
