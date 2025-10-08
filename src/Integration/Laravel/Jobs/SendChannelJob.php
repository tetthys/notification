<?php

namespace Tetthys\Notification\Integration\Laravel\Jobs;

use Tetthys\Notification\Core\Model\Notification;
use Tetthys\Notification\Core\Contracts\Channel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;

class SendChannelJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public string $channel,
        public Notification $notification,
    ) {}

    public function handle(\Illuminate\Contracts\Container\Container $app): void
    {
        /** @var Channel[] $channels */
        $channels = $app->make("notif.channels");

        foreach ($channels as $ch) {
            if ($ch->name() !== $this->channel) {
                continue;
            }

            foreach ($this->notification->recipients as $uid) {
                $payload = $this->notification->content[$this->channel] ?? [];
                $ch->send($this->notification, $payload, (string) $uid);
            }
            break;
        }
    }
}
