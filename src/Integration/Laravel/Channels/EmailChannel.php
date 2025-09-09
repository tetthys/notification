<?php

namespace Tetthys\Notification\Integration\Laravel\Channels;

use Tetthys\Notification\Core\Contracts\Channel;
use Tetthys\Notification\Core\Model\Notification as CoreNotification;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

final class EmailChannel implements Channel
{
    public function name(): string { return 'email'; }

    public function send(CoreNotification $notification, array $payload, string $recipientId): void
    {
        $user = User::query()->findOrFail($recipientId);

        Mail::raw($payload['body'] ?? '', function ($m) use ($user, $payload) {
            $m->to($user->email, $user->name ?? null)
              ->subject($payload['subject'] ?? '[Notification]');
        });
    }
}