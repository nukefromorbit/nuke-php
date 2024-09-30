<?php

namespace Nuke\Events;

class BrowserAuthorizeEvent extends AbstractEvent
{
    private const NAME = 'browser-authorize';

    /**
     * @var string|null
     */
    public ?string $redirect_uri = null;

    /**
     * @var string|null
     */
    public ?string $nuke_identifier = null;

    /**
     * @var string|null
     */
    public ?string $nuke_token = null;

    /**
     * @inheritDoc
     */
    public static function getName(): string
    {
        return self::NAME;
    }
}
