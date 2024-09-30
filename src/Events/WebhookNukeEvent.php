<?php

namespace Nuke\Events;

class WebhookNukeEvent extends AbstractEvent
{
    private const NAME = 'nuke';

    /**
     * @var string|null
     */
    public ?string $token = null;

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return self::NAME;
    }
}
