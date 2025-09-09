<?php

namespace Tetthys\Notification\Core\Model;

final class Notification
{
    public function __construct(
        public string $id,
        public string $source,
        public string $type,
        public array $recipients,
        public array $channels,
        public array $content,
        public int $priority = 0,
        public \DateTimeImmutable $timestamp = new \DateTimeImmutable(),
        public string $status = 'Pending',
        public ?string $parentId = null,
        public ?string $tenantId = null,
    ) {}
}