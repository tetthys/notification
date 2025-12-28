<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

use Tetthys\Notification\Core\Model\DeliveryKey;
use Tetthys\Notification\Core\Model\DeliveryResult;

/**
 * Idempotency + delivery outcome storage.
 */
interface DeliveryStore
{
    /**
     * Claim returns false if already claimed/sent/failed (policy-dependent).
     */
    public function claim(DeliveryKey $key): bool;

    public function markSent(DeliveryKey $key): void;

    public function markFailed(DeliveryKey $key, string $reason): void;

    /**
     * Optional lookup (useful for debugging).
     */
    public function get(DeliveryKey $key): ?DeliveryResult;
}
