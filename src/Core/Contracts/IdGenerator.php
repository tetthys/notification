<?php

declare(strict_types=1);

namespace Tetthys\Notification\Core\Contracts;

interface IdGenerator
{
    public function generate(): string;
}
