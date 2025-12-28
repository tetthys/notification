<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Support;

use Tetthys\Notification\Core\Contracts\{Channel, ChannelResolver};

final class ContainerChannelResolver implements ChannelResolver
{
    /** @var array<string, string> */
    private array $map;

    public function __construct()
    {
        /** @var array<string, string> $map */
        $map = (array) config('tetthys-notification.channels', []);
        $this->map = $map;
    }

    public function has(string $channel): bool
    {
        return isset($this->map[$channel]) && $this->map[$channel] !== '';
    }

    public function get(string $channel): Channel
    {
        if (!$this->has($channel)) {
            throw new \InvalidArgumentException("Channel not registered: {$channel}");
        }

        $binding = $this->map[$channel];

        /** @var mixed $obj */
        $obj = app($binding);

        if (!$obj instanceof Channel) {
            throw new \RuntimeException("Channel binding must implement Channel: {$binding}");
        }

        return $obj;
    }

    public function names(): array
    {
        return array_values(array_keys($this->map));
    }
}
