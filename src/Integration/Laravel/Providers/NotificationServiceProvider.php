<?php

namespace Tetthys\Notification\Integration\Laravel\Providers;

use Illuminate\Support\ServiceProvider;
use Tetthys\Notification\Core\NotificationService;
use Tetthys\Notification\Core\Contracts\{
    IdGenerator,
    RbacPolicy,
    Preferences,
    TemplateEngine,
    QueueBus,
    Channel,
};
use Tetthys\Notification\Integration\Laravel\LaravelNotifier;
use Tetthys\Notification\Integration\Laravel\Infra\{
    UuidIds,
    LaravelRbacConfig,
    DbPreferences,
    BladeTemplateEngine,
    LaravelQueueBus,
};
use Tetthys\Notification\Integration\Laravel\Channels\{
    EmailChannel,
    InAppChannel,
};

final class NotificationServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(IdGenerator::class, fn() => new UuidIds());
        $this->app->bind(RbacPolicy::class, fn() => new LaravelRbacConfig());
        $this->app->bind(Preferences::class, fn() => new DbPreferences());
        $this->app->bind(
            TemplateEngine::class,
            fn() => new BladeTemplateEngine(),
        );
        $this->app->bind(QueueBus::class, fn() => new LaravelQueueBus());

        $this->app->bind(
            "notif.channels",
            fn() => [new EmailChannel(), new InAppChannel()],
        );

        $this->app->singleton(NotificationService::class, function ($app) {
            return new NotificationService(
                $app->make(IdGenerator::class),
                $app->make(RbacPolicy::class),
                $app->make(Preferences::class),
                $app->make(TemplateEngine::class),
                $app->make(QueueBus::class),
                $app->make("notif.channels"),
            );
        });

        $this->app->singleton(
            LaravelNotifier::class,
            fn($app) => new LaravelNotifier(
                $app->make(NotificationService::class),
            ),
        );
    }

    public function boot(): void
    {
        $this->publishes(
            [
                __DIR__ .
                    "/../../../../resources/config/notifications.php" => config_path(
                    "notifications.php",
                ),
            ],
            "tetthys-notification-config",
        );

        $this->publishes(
            [
                __DIR__ . "/../../../../resources/migrations/" => database_path(
                    "migrations",
                ),
            ],
            "tetthys-notification-migrations",
        );
    }
}
