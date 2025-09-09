<?php

namespace Tetthys\Notification\Core\Contracts;

interface IdGenerator
{
    public function generate(): string;
}