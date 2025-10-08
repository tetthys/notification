<?php

namespace Tetthys\Notification\Core\Contracts;

interface TemplateEngine
{
    public function render(
        string $notificationType,
        string $channel,
        array $data,
    ): array;
}
