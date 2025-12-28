<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

final readonly class RecipientPrefs
{
    /**
     * @param list<string> $disabledChannels
     */
    public function __construct(
        public array $disabledChannels = [],
        public ?string $locale = null,
    ) {}
}
