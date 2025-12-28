<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Events\Dispatcher;
use Tetthys\Notification\Core\Contracts\{
    ChannelResolver,
    DeliveryStore,
    IdGenerator,
    Preferences,
    QueueBus,
    RbacPolicy,
    TemplateEngine
};
use Tetthys\Notification\Core\Service\{
    DeliveryWorker,
    NotificationService
};
use Tetthys\Notification\Integration\Laravel\Support\{
    CacheDeliveryStore,
    ContainerChannelResolver,
    LaravelIdGenerator,
    LaravelQueueBus
};
use Tetthys\Notification\Integration\Laravel\Listeners\AutoNotifyListener;

final class LaravelNotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../../../config/tetthys-notification.php', 'tetthys-notification');

        $this->app->singleton(IdGenerator::class, LaravelIdGenerator::class);
        $this->app->singleton(QueueBus::class, LaravelQueueBus::class);
        $this->app->singleton(ChannelResolver::class, ContainerChannelResolver::class);
        $this->app->singleton(DeliveryStore::class, CacheDeliveryStore::class);

        $this->app->singleton(RbacPolicy::class, function () {
            return new class implements RbacPolicy {
                public function assertCanSend(string $callerRole, string $notificationType): void {}
            };
        });

        $this->app->singleton(Preferences::class, function ($app) {
            return new class($app) implements Preferences {
                public function __construct(private readonly \Illuminate\Contracts\Foundation\Application $app) {}

                public function forRecipient(
                    string $recipientId,
                    string $type,
                    ?string $tenantId = null
                ): \Tetthys\Notification\Core\Contracts\RecipientPrefs {
                    $cfg = (array) config('tetthys-notification.preferences', []);
                    $disabled = (array) ($cfg['disabled_channels'] ?? []);
                    $locale = $cfg['locale'] ?? $this->app->getLocale();

                    return new \Tetthys\Notification\Core\Contracts\RecipientPrefs(
                        disabledChannels: array_values(array_map('strval', $disabled)),
                        locale: $locale ? (string) $locale : null,
                    );
                }
            };
        });

        $this->app->singleton(\Tetthys\Notification\Core\Contracts\TemplateEngine::class, function () {
            return new class implements \Tetthys\Notification\Core\Contracts\TemplateEngine {
                public function render(
                    string $type,
                    string $channel,
                    string $recipientId,
                    ?string $locale,
                    array $data,
                    ?string $tenantId = null,
                ): array {
                    $title = htmlspecialchars((string)($data['title'] ?? $type), ENT_QUOTES, 'UTF-8');
                    $body  = htmlspecialchars((string)($data['body'] ?? ''), ENT_QUOTES, 'UTF-8');

                    return [
                        'subject' => $title,
                        'html' => "<h1>{$title}</h1><p>{$body}</p>",
                    ];
                }
            };
        });

        $this->app->singleton(DeliveryWorker::class, function ($app) {
            return new DeliveryWorker(
                store: $app->make(DeliveryStore::class),
                tpl: $app->make(TemplateEngine::class),
                channels: $app->make(ChannelResolver::class),
            );
        });

        $this->app->singleton(NotificationService::class, function ($app) {
            /** @var list<string> $defaultChannels */
            $defaultChannels = (array) config('tetthys-notification.default_channels', ['email']);

            return new NotificationService(
                ids: $app->make(IdGenerator::class),
                rbac: $app->make(RbacPolicy::class),
                prefs: $app->make(Preferences::class),
                channels: $app->make(ChannelResolver::class),
                bus: $app->make(QueueBus::class),
                defaultChannels: array_values(array_map('strval', $defaultChannels)),
            );
        });

        $this->app->alias(NotificationService::class, 'tetthys.notify');
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../../../config/tetthys-notification.php' => config_path('tetthys-notification.php'),
        ], 'tetthys-notification-config');

        if ((bool) config('tetthys-notification.auto_listener.enabled', true)) {
            /** @var Dispatcher $events */
            $events = $this->app->make(Dispatcher::class);

            // Listen to all events, filter by Notifies interface.
            $events->listen('*', [AutoNotifyListener::class, 'handle']);
        }
    }
}
