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
    /**
     * @var string[] The list of available channel names.
     */
    private array $channelNames;

    /**
     * Constructs a new NotificationService instance.
     * 
     * @param IdGenerator $ids Service to generate unique IDs.
     * @param RbacPolicy $rbac Service to enforce RBAC policies.
     * @param Preferences $prefs Service to manage user preferences.
     * @param TemplateEngine $tpl Service to render notification templates.
     * @param QueueBus $bus Service to enqueue notifications for delivery.
     * @param Channel[] $channels List of available notification channels.
     * 
     * @return void
     */
    public function __construct(
        private IdGenerator $ids,
        private RbacPolicy $rbac,
        private Preferences $prefs,
        private TemplateEngine $tpl,
        private QueueBus $bus,
        private array $channels,
    ) {
        // Initialize the list of available channel names.
        $this->channelNames = array_values(
            array_unique(
                array_map(static fn(Channel $channel) => $channel->name(), $channels),
            ),
        );
    }

    /**
     * Triggers a new notification.
     * 
     * @param string $callerRole The role of the caller triggering the notification.
     * @param string $type The type/category of the notification.
     * @param array $recipients The list of recipient user IDs.
     * @param array $data Data to be used in rendering the notification content.
     * @param array $defaultChs Default channels to use if no preferences are set.
     * @param string $source The source of the notification.
     * 
     * @return Notification The created notification instance.
     * 
     * @throws \Exception If the caller is not authorized to send this type of notification.
     */
    public function trigger(
        string $callerRole,
        string $type,
        array $recipients,
        array $data,
        array $defaultChs = ["email"],
        string $source = "App",
    ): Notification {
        $this->rbac->assertCanSend($callerRole, $type);

        $defaults = $this->normalizeDefaults($defaultChs);

        $channels = $this->filterChannelsByPrefs(
            $recipients,
            $type,
            $defaults,
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

    /**
     * Normalizes the default channels to ensure they are valid.
     * 
     * @param array $defaults The list of default channel names.
     * 
     * @return array The normalized list of valid default channel names.
     */
    private function normalizeDefaults(array $defaults): array
    {
        $valid = array_values(array_intersect($this->channelNames, $defaults));

        if ($valid !== []) {
            return $valid;
        }

        return $this->channelNames;
    }

    /**
     * Filters channels based on user preferences.
     * 
     * @param array $recipients The list of recipient user IDs.
     * @param string $type The type/category of the notification.
     * @param array $defaults The list of default channel names.
     * 
     * @return array The effective list of channels after applying user preferences.
     */
    private function filterChannelsByPrefs(array $recipients, string $type, array $defaults): array
    {
        $effective = $defaults;
        foreach ($recipients as $uid) {
            $disabled = $this->prefs->disabledChannelsFor($uid, $type);
            $effective = array_values(array_diff($effective, $disabled));
        }
        return $effective;
    }
}
