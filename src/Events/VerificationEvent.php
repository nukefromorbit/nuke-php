<?php

namespace Nuke\Events;

class VerificationEvent extends AbstractEvent
{
    private const NAME = 'verification';

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
