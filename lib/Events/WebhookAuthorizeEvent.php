<?php

namespace Nuke\Events;

class WebhookAuthorizeEvent extends AbstractEvent
{
    private const TYPE = 'authorize';

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
