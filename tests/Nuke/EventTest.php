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
    private const NUKE_IDENTIFIER = '85cc991a23025910772a5fc7f42b3387';
    private const NUKE_SECRET = '7df8fc9466a07da2710af3b5514e4403faecc3df54908c5b6c5214158d24cb1e';

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
