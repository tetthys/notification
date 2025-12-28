<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core;

use Tetthys\Notification\Core\Contracts\{
    IdGenerator,
    RbacPolicy,
    Preferences,
    QueueBus,
    Channel,
    AuditLog,
};
use Tetthys\Notification\Core\Model\{
    Notification,
    NotificationStatus,
    DeliveryMessage,
};

/**
 * Orchestrates notification triggering:
 * - RBAC gate
 * - Resolve effective channels per recipient (fan-out)
 * - Enqueue delivery messages (recipient x channel)
 * - Return immutable Notification envelope for trace/audit
 */
final class NotificationService
{
    /** @var list<string> */
    private array $channelNames;

    /**
     * @param list<Channel> $channels
     */
    public function __construct(
        private readonly IdGenerator $ids,
        private readonly RbacPolicy $rbac,
        private readonly Preferences $prefs,
        private readonly QueueBus $bus,
        private readonly array $channels,
        private readonly ?AuditLog $audit = null,
    ) {
        $this->channelNames = array_values(
            array_unique(
                array_map(static fn(Channel $channel): string => $channel->name(), $channels),
            ),
        );
    }

    /**
     * Trigger a new notification and enqueue deliveries.
     *
     * @param list<string> $recipients
     * @param array<string, mixed> $data
     * @param list<string> $defaultChs
     */
    public function trigger(
        string $callerRole,
        string $type,
        array $recipients,
        array $data,
        array $defaultChs = ['email'],
        string $source = 'App',
    ): Notification {
        $this->rbac->assertCanSend($callerRole, $type);

        $defaults = $this->normalizeDefaults($defaultChs);

        $notificationId = $this->ids->generate();
        $priority = (int)($data['priority'] ?? 0);
        $parentId = isset($data['parentId']) ? (string)$data['parentId'] : null;
        $tenantId = isset($data['tenantId']) ? (string)$data['tenantId'] : null;

        $allChannelsUsed = [];

        foreach ($recipients as $uid) {
            $uid = (string)$uid;

            $chs = $this->channelsForRecipient($uid, $type, $defaults);
            if ($chs === []) {
                continue;
            }

            foreach ($chs as $ch) {
                $allChannelsUsed[$ch] = true;

                $msg = new DeliveryMessage(
                    notificationId: $notificationId,
                    source: $source,
                    type: $type,
                    recipientId: $uid,
                    channel: $ch,
                    data: $this->minimizeDataForQueue($data),
                    priority: $priority,
                    parentId: $parentId,
                    tenantId: $tenantId,
                );

                $this->bus->enqueueDelivery($msg);

                $this->audit?->deliveryQueued(
                    notificationId: $notificationId,
                    recipientId: $uid,
                    channel: $ch,
                    meta: [
                        'type' => $type,
                        'source' => $source,
                        'priority' => $priority,
                        'parentId' => $parentId,
                        'tenantId' => $tenantId,
                    ],
                );
            }
        }

        $channelsUsed = array_keys($allChannelsUsed);

        $notification = new Notification(
            id: $notificationId,
            source: $source,
            type: $type,
            recipients: array_values(array_map('strval', $recipients)),
            channels: $channelsUsed,
            content: [], // Not pre-rendered; worker will render.
            priority: $priority,
            timestamp: new \DateTimeImmutable(),
            status: NotificationStatus::Queued,
            parentId: $parentId,
            tenantId: $tenantId,
        );

        $this->audit?->notificationTriggered(
            notificationId: $notificationId,
            source: $source,
            type: $type,
            recipients: $notification->recipients,
            channels: $channelsUsed,
            meta: [
                'priority' => $priority,
                'parentId' => $parentId,
                'tenantId' => $tenantId,
                'defaults' => $defaults,
            ],
        );

        return $notification;
    }

    /**
     * Normalize defaults to valid channel names.
     *
     * Policy:
     * - If provided defaults contain at least one valid channel, use them.
     * - If none are valid, fall back to a safe minimal set ("email") if available,
     *   otherwise fall back to all available channels.
     *
     * @param list<string> $defaults
     * @return list<string>
     */
    private function normalizeDefaults(array $defaults): array
    {
        $valid = array_values(array_intersect($this->channelNames, $defaults));
        if ($valid !== []) {
            return $valid;
        }

        $safe = array_values(array_intersect($this->channelNames, ['email']));
        if ($safe !== []) {
            return $safe;
        }

        return $this->channelNames;
    }

    /**
     * Resolve effective channels for a single recipient.
     *
     * @param list<string> $defaults
     * @return list<string>
     */
    private function channelsForRecipient(string $recipientId, string $type, array $defaults): array
    {
        $disabled = $this->prefs->disabledChannelsFor($recipientId, $type);

        // Defensive: only remove channels that actually exist.
        $disabled = array_values(array_intersect($this->channelNames, $disabled));

        return array_values(array_diff($defaults, $disabled));
    }

    /**
     * Reduce queue payload to minimize sensitive data exposure.
     * Override/extend this policy depending on your threat model.
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function minimizeDataForQueue(array $data): array
    {
        // Example policy: drop known sensitive keys.
        // Implementations may instead whitelist allowed keys.
        $deny = [
            'password',
            'secret',
            'token',
            'access_token',
            'refresh_token',
            'api_key',
            'private_key',
            'seed',
            'mnemonic',
        ];

        foreach ($deny as $k) {
            if (array_key_exists($k, $data)) {
                unset($data[$k]);
            }
        }

        return $data;
    }
}
