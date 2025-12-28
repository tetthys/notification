<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Service;

use Tetthys\Notification\Core\Contracts\{
    ChannelResolver,
    DeliveryStore,
    TemplateEngine,
};
use Tetthys\Notification\Core\Model\{
    DeliveryKey,
    DeliveryMessage,
    Notification,
    NotificationStatus,
};

final class DeliveryWorker
{
    public function __construct(
        private readonly DeliveryStore $store,
        private readonly TemplateEngine $tpl,
        private readonly ChannelResolver $channels,
    ) {}

    public function handle(DeliveryMessage $msg): void
    {
        $key = new DeliveryKey($msg->notificationId, $msg->recipientId, $msg->channel);

        // Idempotency gate.
        if (!$this->store->claim($key)) {
            return;
        }

        try {
            $payload = $this->tpl->render(
                type: $msg->type,
                channel: $msg->channel,
                recipientId: $msg->recipientId,
                locale: $msg->locale,
                data: $msg->data,
                tenantId: $msg->tenantId,
            );

            $notification = new Notification(
                id: $msg->notificationId,
                source: $msg->source,
                type: $msg->type,
                recipients: [$msg->recipientId],
                channels: [$msg->channel],
                priority: $msg->priority,
                timestamp: $msg->timestamp,
                status: NotificationStatus::Queued,
                tenantId: $msg->tenantId,
                parentId: $msg->parentId,
            );

            $this->channels->get($msg->channel)->send($notification, $payload, $msg->recipientId);

            $this->store->markSent($key);
        } catch (\Throwable $e) {
            $this->store->markFailed($key, $e->getMessage());
            throw $e; // Let queue retry policy decide.
        }
    }
}
