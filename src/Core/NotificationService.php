<?php

namespace Tetthys\Notification\Core;

use Tetthys\Notification\Core\Contracts\{
    IdGenerator,
    RbacPolicy,
    Preferences,
    TemplateEngine,
    Channel,
    QueueBus,
};
use Tetthys\Notification\Core\Model\Notification;

final class NotificationService
{
    public function __construct(
        private IdGenerator $ids,
        private RbacPolicy $rbac,
        private Preferences $prefs,
        private TemplateEngine $tpl,
        private QueueBus $bus,
        private array $channels,
    ) {}

    public function trigger(
        string $callerRole,
        string $type,
        array $recipients,
        array $data,
        array $defaultChs = ["email"],
        string $source = "App",
    ): Notification {
        $this->rbac->assertCanSend($callerRole, $type);

        $channels = $this->filterChannelsByPrefs(
            $recipients,
            $type,
            $defaultChs,
        );

        $content = [];
        foreach ($channels as $ch) {
            $content[$ch] = $this->tpl->render($type, $ch, $data);
        }

        $notification = new Notification(
            id: $this->ids->generate(),
            source: $source,
            type: $type,
            recipients: $recipients,
            channels: $channels,
            content: $content,
            priority: $data["priority"] ?? 0,
            timestamp: new \DateTimeImmutable(),
            status: "Queued",
            parentId: $data["parentId"] ?? null,
            tenantId: $data["tenantId"] ?? null,
        );

        foreach ($channels as $ch) {
            $this->bus->enqueue($ch, $notification);
        }

        return $notification;
    }

    private function filterChannelsByPrefs(
        array $recipients,
        string $type,
        array $defaults,
    ): array {
        $effective = $defaults;
        foreach ($recipients as $uid) {
            $disabled = $this->prefs->disabledChannelsFor($uid, $type);
            $effective = array_values(array_diff($effective, $disabled));
        }
        return $effective;
    }
}
