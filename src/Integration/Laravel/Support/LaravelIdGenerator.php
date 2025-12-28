<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Support;

use Illuminate\Support\Str;
use Tetthys\Notification\Core\Contracts\IdGenerator;

final class LaravelIdGenerator implements IdGenerator
{
    public function generate(): string
    {
        // ULID is sortable and collision-resistant enough for notification ids.
        return (string) Str::ulid();
    }
}
