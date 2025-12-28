<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Minimal channel lookup abstraction.
 * Keeps Core independent from DI containers / registries.
 */
interface ChannelResolver
{
    public function has(string $channel): bool;

    public function get(string $channel): Channel;

    /**
     * @return list<string>
     */
    public function names(): array;
}
