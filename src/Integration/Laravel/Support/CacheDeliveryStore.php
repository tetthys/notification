<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Support;

use Illuminate\Contracts\Cache\Repository as CacheRepository;
use Tetthys\Notification\Core\Contracts\DeliveryStore;
use Tetthys\Notification\Core\Model\{DeliveryKey, DeliveryResult, DeliveryStatus};

final class CacheDeliveryStore implements DeliveryStore
{
    public function __construct(
        private readonly CacheRepository $cache
    ) {}

    public function claim(DeliveryKey $key): bool
    {
        // Use atomic "add" when available to ensure idempotency across workers.
        $cacheKey = $this->key($key);

        $ttl = (int) config('tetthys-notification.store.cache.ttl_seconds', 604800);

        // Value stores current status for optional debugging.
        return $this->cache->add($cacheKey, DeliveryStatus::Claimed->value, $ttl);
    }

    public function markSent(DeliveryKey $key): void
    {
        $cacheKey = $this->key($key);
        $ttl = (int) config('tetthys-notification.store.cache.ttl_seconds', 604800);
        $this->cache->put($cacheKey, DeliveryStatus::Sent->value, $ttl);
    }

    public function markFailed(DeliveryKey $key, string $reason): void
    {
        $cacheKey = $this->key($key);
        $ttl = (int) config('tetthys-notification.store.cache.ttl_seconds', 604800);

        // Store a compact payload for debugging.
        $this->cache->put($cacheKey, [
            'status' => DeliveryStatus::Failed->value,
            'reason' => $reason,
        ], $ttl);
    }

    public function get(DeliveryKey $key): ?DeliveryResult
    {
        $v = $this->cache->get($this->key($key));

        if ($v === null) {
            return null;
        }

        if (is_string($v)) {
            return new DeliveryResult(DeliveryStatus::from($v));
        }

        if (is_array($v) && isset($v['status'])) {
            $status = DeliveryStatus::from((string) $v['status']);
            $reason = isset($v['reason']) ? (string) $v['reason'] : null;
            return new DeliveryResult($status, $reason);
        }

        return null;
    }

    private function key(DeliveryKey $key): string
    {
        $prefix = (string) config('tetthys-notification.store.cache.prefix', 'tetthys:notification:delivery:');

        // Keep it deterministic and small.
        return $prefix . sha1($key->notificationId . '|' . $key->recipientId . '|' . $key->channel);
    }
}
