<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

interface Preferences
{
    public function forRecipient(
        string $recipientId,
        string $type,
        ?string $tenantId = null,
    ): RecipientPrefs;
}
