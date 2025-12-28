<?php

declare(strict_types=1);

namespace Tetthys\Notification\Integration\Laravel\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Tetthys\Notification\Core\Model\Notification trigger(string $callerRole, string $type, array $recipients, array $data = [], ?array $channelsOverride = null, string $source = 'App', ?string $tenantId = null, ?string $parentId = null)
 */
final class Notify extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'tetthys.notify';
    }
}
