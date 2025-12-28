<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Renders channel-specific payload for a given notification type.
 */
interface TemplateEngine
{
    /**
     * Render a payload for the given type and channel.
     *
     * @return array<string, mixed> Channel-specific payload.
     */
    public function render(string $notificationType, string $channel, array $data): array;
}
