<?php

namespace Nuke\Events;

use Nuke\Crypt;
use Nuke\Nuke;
use Nuke\Event;
use Nuke\Exceptions\InvalidEventPropertyException;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(BrowserAuthorizeEvent::class)]
#[UsesClass(Nuke::class)]
#[UsesClass(Event::class)]
#[UsesClass(Crypt::class)]
final class BrowserAuthorizeEventTest extends TestCase
{
    private const NUKE_IDENTIFIER = '85cc991a23025910772a5fc7f42b3387';
    private const NUKE_SECRET = '7df8fc9466a07da2710af3b5514e4403faecc3df54908c5b6c5214158d24cb1e';

    public function testInvalidEventPropertyTypeException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $_GET = [];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testInvalidEventPropertySourceException(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
        ];

        $this->expectException(InvalidEventPropertyException::class);
        Event::construct(BrowserAuthorizeEvent::class);
    }

    public function testConstruct(): void
    {
        Nuke::setIdentifier(self::NUKE_IDENTIFIER);
        Nuke::setSecret(self::NUKE_SECRET);

        $token = bin2hex(random_bytes(64));
        /** @noinspection PhpArrayWriteIsNotUsedInspection */
        $_GET = [
            'type' => BrowserAuthorizeEvent::getType(),
            'source' => BrowserAuthorizeEvent::SOURCE_NUKE,
            'redirect_uri' => Nuke::APP_URL,
            'nuke_identifier' => Nuke::$identifier,
            'nuke_token' => Nuke::encrypt($token),
        ];

        $event = Event::construct(BrowserAuthorizeEvent::class);
        $this->assertEquals(BrowserAuthorizeEvent::SOURCE_NUKE, $event->source);
        $this->assertEquals(Nuke::$identifier, $event->nuke_identifier);
        $this->assertEquals($token, Nuke::decrypt($event->nuke_token));
        $this->assertEquals(Nuke::APP_URL, $event->redirect_uri);
    }

    public function testGetType(): void
    {
        $this->assertEquals('authorize', BrowserAuthorizeEvent::getType());
    }
}
