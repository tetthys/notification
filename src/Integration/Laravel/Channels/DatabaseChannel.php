<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Channels;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tetthys\Notification\Core\Contracts\Channel;
use Tetthys\Notification\Core\Model\Notification;

final class DatabaseChannel implements Channel
{
    public function name(): string
    {
        return 'database';
    }

    public function send(Notification $notification, array $payload, string $recipientId): void
    {
        $notifiableType = (string) config(
            'tetthys-notification.database.notifiable_type',
            \App\Models\User::class,
        );

        DB::table('notifications')->insert([
            // Laravel notifications table expects a per-row UUID
            'id' => (string) Str::uuid(),

            // Convention: notification class FQCN
            'type' => (string) config(
                'tetthys-notification.database.type_class',
                \Tetthys\Notification\Integration\Laravel\Notifications\TetthysDatabaseNotification::class,
            ),

            'notifiable_type' => $notifiableType,
            'notifiable_id' => $recipientId,

            // Store Core notification context inside data
            'data' => json_encode([
                'notification_id' => $notification->id,   // Core notification id
                'type' => $notification->type,             // domain-level type (e.g. order.paid)
                'source' => $notification->source,
                'tenant_id' => $notification->tenantId,
                'parent_id' => $notification->parentId,
                'payload' => $payload,                      // rendered template payload
            ], JSON_UNESCAPED_UNICODE),

            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
