<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Optional: resolves available channels in the system.
 *
 * Useful when channels are runtime-registered, or provided by plugins.
 */
interface ChannelRegistry
{
    /**
     * @return list<string> List of available channel names.
     */
    public function names(): array;

    /**
     * Whether the given channel name exists.
     */
    public function has(string $channel): bool;
}
