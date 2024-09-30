<?php

namespace Nuke\Events;

class WebhookAuthorizeEvent extends AbstractEvent
{
    private const NAME = 'authorize';

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
