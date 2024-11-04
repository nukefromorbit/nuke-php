<?php

namespace Nuke\Events;

class BrowserAuthorizeEvent extends AbstractEvent
{
    private const TYPE = 'authorize';

    /**
     * @var string|null
     */
    public ?string $nuke_identifier = null;

    /**
     * @var string|null
     */
    public ?string $nuke_token = null;

    /**
     * @var string|null
     */
    public ?string $redirect_uri = null;

    /**
     * @inheritDoc
     */
    public static function getType(): string
    {
        return self::TYPE;
    }
}
