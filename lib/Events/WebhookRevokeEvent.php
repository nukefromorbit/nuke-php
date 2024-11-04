<?php

namespace Nuke\Events;

class WebhookRevokeEvent extends AbstractEvent
{
    private const TYPE = 'revoke';

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
