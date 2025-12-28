# Tetthys Notification (Laravel Integration)

Framework-agnostic notification core with a thin Laravel integration.
Designed for queue-safe, idempotent delivery with first-class database inbox support.

---

## Overview

This package provides:

- A **framework-independent Core pipeline**
- A **minimal Laravel integration**
- A built-in **DatabaseChannel** compatible with Laravel’s `notifications` table
- Queue-based fan-out (recipient × channel)
- Idempotent delivery (retry-safe)

The Core never depends on Laravel.
Laravel is used only for DI, queue, cache, and database integration.

---

## High-level Pipeline

```

Event / Service / Controller
↓
Notify::trigger(...)
↓
NotificationService (Core)

* RBAC check
* recipient preferences
* channel selection
* enqueue DeliveryMessage(s)
  ↓
  Laravel Queue
  ↓
  DeliveryWorker (Core)
* idempotency claim
* template rendering
* Channel::send(...)
  ↓
  DatabaseChannel / EmailChannel / ...

````

**Important design rule**  
> _“Database notifications are just another channel.”_

---

## Installation (Laravel 12)

### 1. Install via Composer

```bash
composer require tetthys/notification
````

The service provider is auto-discovered.

---

### 2. Publish configuration

```bash
php artisan vendor:publish --tag=tetthys-notification-config
```

This creates:

```php
config/tetthys-notification.php
```

---

### 3. Create Laravel notifications table

Use Laravel’s official migration:

```bash
php artisan notifications:table
php artisan migrate
```

Schema (simplified):

```sql
notifications
- id (uuid, primary)
- type
- notifiable_type
- notifiable_id
- data (text)
- read_at
- created_at / updated_at
```

---

## Default Configuration

```php
return [
    'default_channels' => ['database'],

    'channels' => [
        'database' => Tetthys\Notification\Integration\Laravel\Channels\DatabaseChannel::class,
    ],

    'database' => [
        'notifiable_type' => App\Models\User::class,
        'type_class' => Tetthys\Notification\Integration\Laravel\Notifications\TetthysDatabaseNotification::class,
    ],
];
```

By default, **every notification is stored in the database inbox**.

---

## Basic Usage

### Sending a notification

```php
use Tetthys\Notification\Integration\Laravel\Facades\Notify;

Notify::trigger(
    callerRole: 'system',
    type: 'order.paid',
    recipients: [$userId],
    data: [
        'title' => 'Payment completed',
        'body'  => 'Your order has been paid successfully.',
    ],
);
```

What happens:

* A database notification row is created
* The operation is queued
* Delivery is retry-safe and idempotent

---

## Event-based Usage (Recommended)

### 1. Define an event

```php
final class OrderPaid
{
    public function __construct(
        public string $orderId,
        public string $userId,
    ) {}
}
```

---

### 2. Listener sends the notification

```php
use Tetthys\Notification\Integration\Laravel\Facades\Notify;

final class SendOrderPaidNotification
{
    public function handle(OrderPaid $event): void
    {
        Notify::trigger(
            callerRole: 'system',
            type: 'order.paid',
            recipients: [$event->userId],
            data: [
                'title' => 'Payment completed',
                'body'  => "Order ID: {$event->orderId}",
            ],
        );
    }
}
```

---

### 3. Dispatch the event

```php
event(new OrderPaid($orderId, $userId));
```

> **Best practice:** Dispatch events *after* DB transactions are committed.

---

## Reading Notifications (Laravel-native)

Because the DatabaseChannel is compatible with Laravel’s schema:

```php
$user->notifications;
$user->unreadNotifications;

$notification->markAsRead();
```

No custom queries required.

---

## Channels

* `database` (built-in, default)
* `email`, `sms`, etc. can be added by implementing:

```php
Tetthys\Notification\Core\Contracts\Channel
```

All channels share the same pipeline and guarantees.

---

## Design Principles

* Core is framework-agnostic
* Laravel integration is thin and replaceable
* Database inbox ≠ delivery log
* Channels are symmetric (DB is not special-cased)
* Queue retries are safe by design

---

## Summary

* Use `Notify::trigger(...)` anywhere
* Prefer **events + listeners**
* Database notifications work out of the box
* Extend by adding channels, not branching logic

This package is optimized for **real-world Laravel applications** that need
clean architecture without sacrificing developer experience.
