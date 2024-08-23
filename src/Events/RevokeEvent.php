<?php

namespace Nuke\Events;

class RevokeEvent extends AbstractEvent
{
    private const NAME = 'revoke';

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
