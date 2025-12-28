<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

/**
 * Renders channel payload.
 */
interface TemplateEngine
{
    /**
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function render(
        string $type,
        string $channel,
        string $recipientId,
        ?string $locale,
        array $data,
        ?string $tenantId = null,
    ): array;
}
