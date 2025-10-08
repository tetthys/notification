<?php

namespace Tetthys\Notification\Integration\Laravel\Infra;

use Tetthys\Notification\Core\Contracts\IdGenerator;
use Illuminate\Support\Str;

final class UuidIds implements IdGenerator
{
    public function generate(): string
    {
        return (string) Str::uuid();
    }
}
