<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

enum DeliveryStatus: string
{
    case Claimed = 'claimed';
    case Sent = 'sent';
    case Failed = 'failed';
}
