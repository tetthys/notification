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
        DB::table("notifications")->insert([
            "id" => (string) Str::uuid(),
            "type" => "core.inapp",
            "notifiable_type" => \App\Models\User::class,
            "notifiable_id" => $recipientId,
            "data" => json_encode(
                [
                    "core_id" => $notification->id,
                    "type" => $notification->type,
                    "title" => $payload["title"] ?? null,
                    "body" => $payload["body"] ?? null,
                    "source" => $notification->source,
                    "priority" => $notification->priority,
                    "channels" => $notification->channels,
                    "tenantId" => $notification->tenantId,
                    "timestamp" => $notification->timestamp->format(DATE_ATOM),
                ],
                JSON_UNESCAPED_UNICODE,
            ),
            "read_at" => null,
            "created_at" => now(),
            "updated_at" => now(),
        ]);
    }
}
