<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Channels;

use Illuminate\Support\Facades\DB;
use Tetthys\Notification\Core\Contracts\Channel;
use Tetthys\Notification\Core\Model\Notification;

final class DbChannel implements Channel
{
    public function name(): string
    {
        return 'db';
    }

    public function send(Notification $notification, array $payload, string $recipientId): void
    {
        DB::table('notifications')->insert([
            'id' => $notification->id . ':' . $recipientId,
            'recipient_id' => $recipientId,
            'type' => $notification->type,
            'source' => $notification->source,
            'data' => json_encode($payload, JSON_UNESCAPED_UNICODE),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
