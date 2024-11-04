<?php

namespace Nuke\Events;

abstract class AbstractEvent
{
    /**
     * Get Event Type
     *
     * @return string
     */
    abstract public static function getType(): string;
}
