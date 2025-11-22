<?php

namespace Tetthys\Notification\Core\Contracts;

interface TemplateEngine
{
    /**
     * Renders the notification content for the specified channel and notification type using the provided data.
     * 
     * @param string $notificationType The type/category of the notification.
     * @param string $channel The channel for which the content is being rendered.
     * @param array $data The data to be used in the template rendering.
     */
    public function render(string $notificationType, string $channel, array $data): array;
}
