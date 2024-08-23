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
     * @var array|null
     */
    public ?array $actions = null;

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return self::NAME;
    }
}
