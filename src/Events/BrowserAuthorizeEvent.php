<?php

namespace Nuke\Events;

class BrowserAuthorizeEvent extends AbstractEvent
{
    private const NAME = 'authorize';

    /**
     * @var string|null
     */
    public ?string $redirect_uri = null;

    /**
     * @var string|null
     */
    public ?string $state = null;

    /**
     * @var string|null
     */
    public ?string $nuke_identifier = null;

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return self::NAME;
    }
}
