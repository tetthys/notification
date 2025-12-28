<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Service;

use Tetthys\Notification\Core\Contracts\{
    ChannelResolver,
    IdGenerator,
    Preferences,
    QueueBus,
    RbacPolicy,
};
use Tetthys\Notification\Core\Model\{
    DeliveryMessage,
    Notification,
    NotificationStatus,
};

final class NotificationService
{
    /**
     * @param list<string> $defaultChannels
     */
    public function __construct(
        private readonly IdGenerator $ids,
        private readonly RbacPolicy $rbac,
        private readonly Preferences $prefs,
        private readonly ChannelResolver $channels,
        private readonly QueueBus $bus,
        private readonly array $defaultChannels = ['email'],
    ) {}

    /**
     * Trigger notification and fan-out deliveries.
     *
     * @param list<string> $recipients
     * @param array<string, mixed> $data
     * @param list<string>|null $channelsOverride If provided, used instead of defaults.
     */
    public function trigger(
        string $callerRole,
        string $type,
        array $recipients,
        array $data = [],
        ?array $channelsOverride = null,
        string $source = 'App',
        ?string $tenantId = null,
        ?string $parentId = null,
    ): Notification {
        $this->rbac->assertCanSend($callerRole, $type);

        $notificationId = $this->ids->generate();
        $priority = (int)($data['priority'] ?? 0);

        $baseChannels = $this->normalizeChannels($channelsOverride ?? $this->defaultChannels);

        $usedChannels = [];

        foreach ($recipients as $rid) {
            $rid = (string)$rid;

            $rp = $this->prefs->forRecipient($rid, $type, $tenantId);

            $effective = array_values(array_diff($baseChannels, $rp->disabledChannels));
            if ($effective === []) {
                continue;
            }

            foreach ($effective as $ch) {
                if (!$this->channels->has($ch)) {
                    continue;
                }

                $usedChannels[$ch] = true;

                $this->bus->enqueue(new DeliveryMessage(
                    notificationId: $notificationId,
                    source: $source,
                    type: $type,
                    recipientId: $rid,
                    channel: $ch,
                    data: $this->minimizeQueueData($data),
                    priority: $priority,
                    tenantId: $tenantId,
                    parentId: $parentId,
                    locale: $rp->locale,
                    timestamp: new \DateTimeImmutable(),
                ));
            }
        }

        return new Notification(
            id: $notificationId,
            source: $source,
            type: $type,
            recipients: array_values(array_map('strval', $recipients)),
            channels: array_keys($usedChannels),
            priority: $priority,
            timestamp: new \DateTimeImmutable(),
            status: NotificationStatus::Queued,
            tenantId: $tenantId,
            parentId: $parentId,
        );
    }

    /**
     * @param list<string> $chs
     * @return list<string>
     */
    private function normalizeChannels(array $chs): array
    {
        $valid = array_values(array_filter(
            array_values(array_unique($chs)),
            fn(string $ch): bool => $this->channels->has($ch),
        ));

        // Safe fallback: prefer email if available.
        if ($valid !== []) {
            return $valid;
        }

        return $this->channels->has('email') ? ['email'] : $this->channels->names();
    }

    /**
     * Reduce queue data to avoid propagating sensitive fields.
     * This is intentionally simple to keep file-count low.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function minimizeQueueData(array $data): array
    {
        foreach (['password', 'secret', 'token', 'access_token', 'refresh_token', 'api_key', 'private_key', 'seed', 'mnemonic'] as $k) {
            unset($data[$k]);
        }
        return $data;
    }
}
