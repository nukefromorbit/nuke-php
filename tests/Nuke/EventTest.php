<?php

namespace Nuke;

use Nuke\Events\BrowserAuthorizeEvent;
use Nuke\Events\WebhookAuthorizeEvent;
use Nuke\Events\WebhookNukeEvent;
use Nuke\Events\WebhookRevokeEvent;
use Nuke\Exceptions\InvalidEventClassException;
use Nuke\Exceptions\InvalidEventPropertyException;
use Nuke\Exceptions\MissingIdentifierException;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Event::class)]
#[UsesClass(Nuke::class)]
#[UsesClass(Crypt::class)]
#[UsesClass(WebhookAuthorizeEvent::class)]
#[UsesClass(WebhookRevokeEvent::class)]
#[UsesClass(WebhookNukeEvent::class)]
#[UsesClass(BrowserAuthorizeEvent::class)]
final class EventTest extends TestCase
{
    private const NUKE_IDENTIFIER = '6lMyeWiyWZYeA1hBbtrtgGNQXMKbD9fV';
    private const NUKE_SECRET = 'GvFyG1eeaxyjVmFAWhCI6f8DZsYZch1rux3iFyBYgxU=';

    public function testConstructWebhookAuthorizeEvent(): void
    {
        $this->expectException(MissingIdentifierException::class);
        Event::construct(WebhookAuthorizeEvent::class);
    }

    public function testConstructWebhookRevokeEvent(): void
    {
        $this->expectException(MissingIdentifierException::class);
        Event::construct(WebhookRevokeEvent::class);
    }

    public function testConstructWebhookNukeEvent(): void
    {
        $this->expectException(MissingIdentifierException::class);
        Event::construct(WebhookNukeEvent::class);
    }

    public function testConstructBrowserAuthorizeEvent(): void
    {
        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testConstructInvalidClassException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $this->expectException(InvalidEventClassException::class);
        Event::construct('Invalid');
    }
}
