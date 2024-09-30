<?php

namespace Nuke\Events;

class WebhookVerifyEvent extends AbstractEvent
{
    private const NAME = 'webhook-verify';

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
