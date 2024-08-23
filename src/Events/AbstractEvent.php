<?php

namespace Nuke\Events;

abstract class AbstractEvent
{
    /**
     * Get Event Name
     *
     * @return string
     */
    abstract public static function getName(): string;
}
