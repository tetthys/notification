<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Generates unique identifiers for notifications and deliveries.
 */
interface IdGenerator
{
    /**
     * Generate a globally unique identifier.
     */
    public function generate(): string;
}
