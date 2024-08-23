<?php

namespace Nuke\Events;

class AuthorizeEvent extends AbstractEvent
{
    private const NAME = 'authorize';

    /**
     * @var string|null
     */
    public ?string $state = null;

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return self::NAME;
    }
}
