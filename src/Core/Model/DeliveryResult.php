<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Model;

final readonly class DeliveryResult
{
    public function __construct(
        public DeliveryStatus $status,
        public ?string $reason = null,
    ) {}
}
