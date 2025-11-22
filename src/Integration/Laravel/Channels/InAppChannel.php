<?php

namespace Tetthys\Notification\Integration\Laravel\Channels;

use Tetthys\Notification\Core\Contracts\Channel;
use Tetthys\Notification\Core\Model\Notification as CoreNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class InAppChannel implements Channel
{
    public function name(): string
    {
        return "inApp";
    }

    public function send(
        CoreNotification $notification,
        array $payload,
        string $recipientId,
    ): void {
        $defaults = [
            'notification_id' => (string) Str::uuid(),
            'laravel_type' => static::class,
            'notifiable_type' => \App\Models\User::class,
            'notifiable_id' => $recipientId,
            'read_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        $payload = array_merge($defaults, $payload);

        $data = array_filter(
            array_merge(
                [
                    'core_id' => $notification->id,
                    'type' => $notification->type,
                    'source' => $notification->source,
                    'priority' => $notification->priority,
                    'channels' => $notification->channels,
                    'tenantId' => $notification->tenantId,
                    'timestamp' => $notification->timestamp->format(DATE_ATOM),
                ],
                array_diff_key($payload, $defaults)
            ),
            static fn($value) => $value !== null,
        );

        DB::table('notifications')->insert([
            'id' => $payload['notification_id'],
            'type' => $payload['laravel_type'],
            'notifiable_type' => $payload['notifiable_type'],
            'notifiable_id' => (string) $payload['notifiable_id'],
            'data' => json_encode($data, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR),
            'read_at' => $payload['read_at'],
            'created_at' => $payload['created_at'],
            'updated_at' => $payload['updated_at'],
        ]);
    }
}
