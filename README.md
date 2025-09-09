# Tetthys/Notification

A **framework-agnostic notification core** with a thin **Laravel adapter**.  
It provides a standardized notification data model and orchestrator, following the principles of modularity, RBAC, user preferences, and fan-out delivery described in the *Generalized Notification Data Architecture for Scalable E-commerce and Beyond* paper.

---

## ✨ Features
- **Framework-agnostic Core** (pure PHP, no Laravel dependencies)
- **Standardized Notification Entity** (id, type, source, recipients, channels, content, priority, timestamp, status, tenant)
- **RBAC Policy** support (role → type authorization)
- **User Preferences** filtering per notification type/channel
- **Template Engine** abstraction (Blade adapter included)
- **Channel abstraction** (Email, In-App, extendable to SMS, Push, Slack…)
- **Queue fan-out** (each channel dispatched as a separate job)
- **Laravel integration** via ServiceProvider and auto-discovery

---

## 📦 Installation

Require via Composer:

```bash
composer require tetthys/notification
````

---

## ⚙️ Laravel Setup

1. **Publish config & migrations:**

```bash
php artisan vendor:publish --tag=tetthys-notification-config
php artisan vendor:publish --tag=tetthys-notification-migrations
php artisan notifications:table
php artisan migrate
```

2. **Run the queue worker:**

```bash
php artisan queue:work
```

---

## 🚀 Usage Example (Laravel)

Trigger a notification when an order is completed:

```php
use App\Events\OrderCompleted;
use Tetthys\Notification\Integration\Laravel\LaravelNotifier;

final class SendOrderCompletedNotification
{
    public function __construct(private LaravelNotifier $notifier) {}

    public function handle(OrderCompleted $event): void
    {
        $order = $event->order;

        $this->notifier->send([
            'callerRole' => 'OrderService',
            'type'       => 'OrderCompleted',
            'recipients' => [(string)$order->user_id],
            'data'       => [
                'subject'  => "Order #{$order->id} completed",
                'title'    => "Order Completed",
                'body'     => "Total: {$order->amount}₩. Thank you for your purchase!",
                'priority' => 1,
                'tenantId' => (string)$order->tenant_id,
            ],
            'defaults'   => ['email','inApp'],
            'source'     => 'OrderService',
        ]);
    }
}
```

---

## 🧪 Testing

The core is fully testable with in-memory doubles.
Example (Pest):

```php
it('filters channels by user preferences and enqueues jobs', function () {
    $svc = app(\Tetthys\Notification\Core\NotificationService::class);

    $notification = $svc->trigger(
        callerRole: 'OrderService',
        type: 'OrderCompleted',
        recipients: ['U1'],
        data: ['subject' => 'Your order', 'body' => 'Confirmed'],
        defaultChs: ['email','sms','inApp'],
        source: 'OrderService'
    );

    expect($notification->type)->toBe('OrderCompleted');
});
```

---

## 🛠️ Extending

* Add new `Channel` implementations (e.g., SMS, Push, Slack)
* Add custom `TemplateEngine` implementations
* Override `Preferences` with your own storage (DB, Redis, API)

---

## 📜 License

MIT © Tetthys