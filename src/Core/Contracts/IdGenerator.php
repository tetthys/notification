<?php

namespace Tetthys\Notification\Core\Contracts;

interface IdGenerator
{
    /**
     * Generates a unique identifier.
     * 
     * @return string The generated unique identifier.
     */
    public function generate(): string;
}
