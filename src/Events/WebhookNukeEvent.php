<?php

namespace Nuke\Events;

class WebhookNukeEvent extends AbstractEvent
{
    private const TYPE = 'nuke';

    /**
     * @var string|null
     */
    public ?string $token = null;

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return self::TYPE;
    }
}
